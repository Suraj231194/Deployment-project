<?php

namespace App\Services\SupportTicket;

use App\Models\Authorization\User;
use App\Models\SupportTicket\SupportTicket;
use App\Models\SupportTicket\SupportTicketAttachment;
use App\Models\SupportTicket\SupportTicketCategory;
use App\Services\Utility\FileHandlingService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class SupportTicketService
{
    // This fallback list keeps the ticket form usable until the category master is migrated and seeded.
    public const CATEGORIES = ['technical', 'billing', 'account', 'general', 'other'];

    public const PRIORITIES = ['low', 'medium', 'high', 'urgent'];

    public function __construct(protected FileHandlingService $fileHandlingService)
    {
    }

    // This prepares the support ticket list page for the signed-in user.
    public function indexPageData(User $user): array
    {
        // Step 1: load the current user's support tickets.
        $tickets = $this->listTicketsForUser($user);

        // Step 2: prepare the page data used by the profile support screen.
        $pageData = [];
        $pageData['tickets'] = $tickets;
        $pageData['selectedTicket'] = null;
        $pageData['ticketAttachments'] = collect();
        $pageData['canCreateTicket'] = $this->canCreateTicket($user);

        // Step 3: return the final page data.
        return $pageData;
    }

    // This returns the active support ticket category slugs used by forms and validation.
    public function availableCategorySlugs(): array
    {
        try {
            // Step 1: start the category query.
            $categoryQuery = SupportTicketCategory::query();

            // Step 2: keep only active categories.
            $categoryQuery->where('is_active', true);

            // Step 3: keep the business display order stable.
            $categoryQuery->orderBy('sort_order');
            $categoryQuery->orderBy('name');

            // Step 4: load the raw category slugs.
            $rawCategorySlugs = $categoryQuery->pluck('slug')->all();

            // Step 5: clean the category values one by one.
            $categorySlugs = [];

            foreach ($rawCategorySlugs as $rawCategorySlug) {
                if (! is_string($rawCategorySlug)) {
                    continue;
                }

                $categorySlug = trim($rawCategorySlug);

                if ($categorySlug === '') {
                    continue;
                }

                $categorySlugs[] = $categorySlug;
            }

            // Step 6: keep the form usable even before master data is ready.
            if ($categorySlugs === []) {
                $categorySlugs = self::CATEGORIES;
            }
        } catch (Throwable $exception) {
            Log::error('Failed to load support ticket categories.', ['error' => $exception->getMessage()]);

            // Step 7: fall back to the safe default list when category loading fails.
            $categorySlugs = self::CATEGORIES;
        }

        // Step 8: return the final category list.
        return $categorySlugs;
    }

    // This prepares the support ticket detail page for one selected ticket.
    public function showPageData(User $user, int $ticketId): array
    {
        // Step 1: load the base list page data.
        $pageData = $this->indexPageData($user);

        // Step 2: load the selected ticket for the current user.
        $selectedTicket = $this->findSupportTicketById($user, $ticketId);

        // Step 3: load the attachments uploaded during ticket creation.
        $ticketAttachments = $this->ticketAttachments($ticketId);

        // Step 4: add the detail data to the page payload.
        $pageData['selectedTicket'] = $selectedTicket;
        $pageData['ticketAttachments'] = $ticketAttachments;

        // Step 5: return the final page payload.
        return $pageData;
    }

    // This downloads one attachment for a ticket owned by the current user.
    public function downloadTicketAttachment(User $user, int $ticketId, int $attachmentId): BinaryFileResponse
    {
        // Step 1: confirm the ticket belongs to the current user.
        $this->findSupportTicketById($user, $ticketId);

        // Step 2: load the requested attachment for the selected ticket.
        $attachment = SupportTicketAttachment::query()
            ->where('support_ticket_id', $ticketId)
            ->whereKey($attachmentId)
            ->first();

        // Step 3: stop the flow when the attachment does not belong to the selected ticket.
        if (! $attachment) {
            throw new NotFoundHttpException('Support ticket attachment not found.');
        }

        // Step 4: stop the download when the saved file path is empty.
        $storedFilePath = trim((string) ($attachment->stored_file_path ?? ''));

        if ($storedFilePath === '') {
            throw new NotFoundHttpException('Support ticket attachment file is not available.');
        }

        // Step 5: return the saved file as a browser download.
        return $this->fileHandlingService->downloadPublicFile(
            $storedFilePath,
            (string) ($attachment->original_file_name ?? basename($storedFilePath)),
        );
    }

    // This creates a support ticket and stores any uploaded files.
    public function createTicket(User $user, array $ticketData, array $attachments = []): int
    {
        // Step 1: stop the request when the current account is not allowed to create tickets.
        if (! $this->canCreateTicket($user)) {
            throw new AuthorizationException('You are not allowed to create support tickets.');
        }

        // Step 2: start a database transaction so the ticket and attachments stay in sync.
        DB::beginTransaction();

        // Step 3: prepare the return value before the ticket is created.
        $ticketId = 0;

        try {
            // Step 4: create the main support ticket record.
            $ticket = SupportTicket::query()->create([
                'ticket_number' => $this->generateTicketNumber(),
                'owner_user_id' => $user->id,
                'owner_company_id' => $user->company_id,
                'created_by_user_id' => $user->id,
                'category' => $ticketData['category'],
                'priority' => $ticketData['priority'],
                'description' => $ticketData['description'],
                'status' => 'open',
                'last_activity_at' => now(),
            ]);

            // Step 5: store the new ticket id for the return value.
            $ticketId = (int) $ticket->id;

            // Step 6: save any uploaded files linked to the ticket.
            $this->saveTicketAttachments($ticketId, $user->id, $attachments);

            // Step 7: finish the database transaction after all records are ready.
            DB::commit();
        } catch (Throwable $exception) {
            // Step 8: undo database changes when ticket creation fails.
            DB::rollBack();

            Log::error('Failed to create support ticket.', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }

        // Step 9: return the created ticket id.
        return $ticketId;
    }

    // This checks whether the user can create tickets.
    public function canCreateTicket(User $user): bool
    {
        // Step 1: define the user types allowed to raise tickets from the profile area.
        $allowedUserTypes = ['b2c', 'b2b', 'internal', 'admin', 'delegated_admin'];

        // Step 2: check whether the current user type is allowed.
        $canCreateTicket = in_array((string) $user->user_type, $allowedUserTypes, true);

        // Step 3: return the final decision.
        return $canCreateTicket;
    }

    // This loads the current user's tickets for the profile support page.
    protected function listTicketsForUser(User $user)
    {
        // Step 1: start the ticket query.
        $ticketQuery = SupportTicket::query();

        // Step 2: keep only the tickets created for the current user.
        $ticketQuery->where('owner_user_id', $user->id);

        // Step 3: show the newest tickets first.
        $ticketQuery->orderByDesc('created_at');
        $ticketQuery->orderByDesc('id');

        // Step 4: return the paginated list for the profile page.
        return $ticketQuery->paginate(15)->withQueryString();
    }

    // This loads one ticket owned by the current user.
    protected function findSupportTicketById(User $user, int $ticketId): SupportTicket
    {
        // Step 1: start the ticket query.
        $ticketQuery = SupportTicket::query();

        // Step 2: load only the requested ticket id.
        $ticketQuery->whereKey($ticketId);

        // Step 3: limit the result to the current user's own tickets.
        $ticketQuery->where('owner_user_id', $user->id);

        // Step 4: fetch the ticket record.
        $ticket = $ticketQuery->first();

        // Step 5: stop the flow when the ticket does not belong to the current user.
        if (! $ticket) {
            throw new NotFoundHttpException('Support ticket not found.');
        }

        // Step 6: return the selected ticket.
        return $ticket;
    }

    // This loads the files uploaded when the ticket was created.
    protected function ticketAttachments(int $ticketId)
    {
        // Step 1: start the attachment query.
        $attachmentQuery = SupportTicketAttachment::query();

        // Step 2: keep only attachments linked to the selected ticket.
        $attachmentQuery->where('support_ticket_id', $ticketId);

        // Step 3: keep only the files uploaded on the main ticket form.
        $attachmentQuery->whereNull('support_ticket_comment_id');

        // Step 4: keep the attachment order stable for the profile page.
        $attachmentQuery->orderBy('created_at');
        $attachmentQuery->orderBy('id');

        // Step 5: return the final attachment collection.
        return $attachmentQuery->get();
    }

    // This stores files uploaded during ticket creation.
    protected function saveTicketAttachments(int $ticketId, int $userId, array $attachments): void
    {
        // Step 1: stop early when there are no files to save.
        if ($attachments === []) {
            return;
        }

        // Step 2: save each uploaded file as a ticket attachment.
        foreach ($attachments as $attachment) {
            if (! $attachment instanceof UploadedFile) {
                continue;
            }

            // Step 3: read the file values before moving the upload.
            $originalFileName = $attachment->getClientOriginalName();
            $fileSize = (int) ($attachment->getSize() ?? 0);
            $mimeType = $attachment->getClientMimeType();

            // Step 4: store the file in the shared support ticket folder.
            $storedFilePath = $this->fileHandlingService->storeUploadedFile(
                $attachment,
                FileHandlingService::DOCUMENT_DIRECTORY.'/support-tickets/'.$ticketId,
            );

            // Step 5: save the file record in the database.
            SupportTicketAttachment::query()->create([
                'support_ticket_id' => $ticketId,
                'support_ticket_comment_id' => null,
                'original_file_name' => $originalFileName,
                'stored_file_path' => $storedFilePath,
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
                'uploaded_by_user_id' => $userId,
                'created_at' => now(),
            ]);
        }
    }

    // This generates a readable support ticket number.
    protected function generateTicketNumber(): string
    {
        try {
            return 'ST-'.now()->format('YmdHis').'-'.str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        } catch (Throwable $exception) {
            Log::error('Failed to generate support ticket number.', ['error' => $exception->getMessage()]);
            throw $exception;
        }
    }
}

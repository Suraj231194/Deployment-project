<?php

namespace App\Http\Controllers\SupportTicket;

use App\Http\Controllers\Controller;
use App\Services\SupportTicket\SupportTicketService;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class SupportTicketController extends Controller
{
    // This renders the main support ticket page for the signed-in user.
    public function index(Request $request, SupportTicketService $supportTicketService): View
    {
        // Step 1: load the page data from the support ticket service.
        $pageData = $supportTicketService->indexPageData($request->user());

        // Step 2: send the prepared data to the support ticket screen.
        return view('userProfile.support-tickets.index', $pageData);
    }

    // This renders the support ticket page with one selected ticket opened.
    public function show(int $ticketId, Request $request, SupportTicketService $supportTicketService): View
    {
        // Step 1: load the selected ticket data for the current user.
        $pageData = $supportTicketService->showPageData($request->user(), $ticketId);

        // Step 2: return the same support ticket screen with detail data.
        return view('userProfile.support-tickets.index', $pageData);
    }

    // This downloads one attachment from a support ticket owned by the current user.
    public function downloadAttachment(int $ticketId, int $attachmentId, Request $request, SupportTicketService $supportTicketService): BinaryFileResponse|RedirectResponse
    {
        try {
            // Step 1: ask the service to validate ownership and return the file.
            return $supportTicketService->downloadTicketAttachment($request->user(), $ticketId, $attachmentId);
        } catch (Throwable $exception) {
            // Step 2: log the attachment download failure for later review.
            Log::error('Failed to download support ticket attachment.', [
                'user_id' => $request->user()?->id,
                'ticket_id' => $ticketId,
                'attachment_id' => $attachmentId,
                'error' => $exception->getMessage(),
            ]);

            // Step 3: return the user back with a simple download error.
            return $this->redirectBackWithError($exception, 'Unable to download the support ticket attachment right now.');
        }
    }

    // This creates a new support ticket from the shared layout widget.
    public function store(Request $request, SupportTicketService $supportTicketService): RedirectResponse
    {
        // Step 1: read the signed-in user once so the next steps stay easy to follow.
        $authenticatedUser = $request->user();

        // Step 2: read the form source so logs can show which business entry point was used.
        $requestSource = $this->readSupportTicketFormSource($request);

        // Step 3: write a start log before validation and database work begin.
        Log::info('Support ticket submission started.', [
            'user_id' => $authenticatedUser?->id,
            'request_source' => $requestSource,
        ]);

        try {
            // Step 4: validate the submitted widget fields against backend rules.
            $validated = $this->validateStoreRequest($request, $supportTicketService);

            // Step 5: prepare the clean ticket payload expected by the service layer.
            $ticketData = $this->buildTicketCreateData($validated);

            // Step 6: load uploaded files as a simple array for the service layer.
            $attachments = $request->file('attachments', []);

            // Step 7: create the support ticket and store any uploaded files.
            $ticketId = $supportTicketService->createTicket(
                $authenticatedUser,
                $ticketData,
                $attachments,
            );

            // Step 8: write a completion log after the ticket is saved successfully.
            Log::info('Support ticket submission completed.', [
                'user_id' => $authenticatedUser?->id,
                'ticket_id' => $ticketId,
                'category' => $ticketData['category'],
                'priority' => $ticketData['priority'],
                'request_source' => $requestSource,
            ]);

            // Step 9: prepare the success response for the same page.
            $response = redirect()->back();
            $response = $response->with('success', 'Support ticket submitted successfully. Our support team will review it shortly.');
        } catch (Throwable $exception) {
            // Step 10: prepare the widget-friendly failure response.
            $response = $this->handleStoreException($exception, $authenticatedUser?->id, $requestSource);
        }

        // Step 11: return the prepared response as the final controller step.
        return $response;
    }

    // This reads the widget source label used in logs and error recovery.
    protected function readSupportTicketFormSource(Request $request): string
    {
        // Step 1: read the raw source from the submitted form.
        $requestSource = trim((string) $request->input('support_ticket_form_source', 'unknown'));

        // Step 2: return a safe fallback when the form does not send a source value.
        if ($requestSource === '') {
            $requestSource = 'unknown';
        }

        // Step 3: return the cleaned source value.
        return $requestSource;
    }

    // This validates the support ticket widget request using backend-owned rules.
    protected function validateStoreRequest(Request $request, SupportTicketService $supportTicketService): array
    {
        // Step 1: load allowed categories from the backend so the form and server stay aligned.
        $categorySlugs = $supportTicketService->availableCategorySlugs();

        // Step 2: validate each submitted field with clear business limits.
        return $request->validate([
            'subject' => ['required', 'string', 'max:150'],
            'category' => ['required', Rule::in($categorySlugs)],
            'priority' => ['nullable', Rule::in(SupportTicketService::PRIORITIES)],
            'description' => ['required', 'string', 'max:4000'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'max:5120'],
            'support_ticket_form_source' => ['nullable', 'string', 'max:50'],
        ]);
    }

    // This prepares the create-ticket payload in a simple service-ready structure.
    protected function buildTicketCreateData(array $validated): array
    {
        // Step 1: clean the short subject line so saved ticket text stays neat.
        $subject = trim((string) $validated['subject']);

        // Step 2: clean the detailed message so extra spaces do not reach the database.
        $description = trim((string) $validated['description']);

        // Step 3: start with the submitted priority value.
        $priority = trim((string) ($validated['priority'] ?? ''));

        // Step 4: use the normal business default when the form does not send a priority.
        if ($priority === '') {
            $priority = SupportTicketService::PRIORITIES[1] ?? 'medium';
        }

        // Step 5: combine the subject and message into one readable ticket body.
        $storedDescription = $this->buildStoredDescription($subject, $description);

        // Step 6: return the exact payload shape expected by the service layer.
        return [
            'category' => $validated['category'],
            'priority' => $priority,
            'description' => $storedDescription,
        ];
    }

    // This combines the subject and message into one readable ticket body.
    protected function buildStoredDescription(string $subject, string $description): string
    {
        // Step 1: start with the short issue summary for quick scanning.
        $lines = [];
        $lines[] = 'Subject: '.$subject;

        // Step 2: add one blank line so the saved text is easier to read.
        $lines[] = '';

        // Step 3: add a clear label before the detailed message.
        $lines[] = 'Details:';

        // Step 4: append the full customer message after trimming.
        $lines[] = $description;

        // Step 5: join the lines into one database-ready text block.
        return implode(PHP_EOL, $lines);
    }

    // This converts submit failures into a widget-friendly response for the same page.
    protected function handleStoreException(Throwable $exception, ?int $userId, string $requestSource): RedirectResponse
    {
        // Step 1: start the redirect response that always returns to the same business page.
        $response = redirect()->back();
        $response = $response->withInput();
        $response = $response->with('support_ticket_widget_open', true);

        // Step 1: handle validation failures with field-level feedback for the same widget.
        if ($exception instanceof ValidationException) {
            Log::warning('Support ticket submission validation failed.', [
                'user_id' => $userId,
                'request_source' => $requestSource,
                'errors' => $exception->errors(),
            ]);

            $errorMessages = $exception->errors();
        }

        // Step 2: handle blocked users with a clear business message.
        elseif ($exception instanceof AuthorizationException) {
            Log::warning('Support ticket submission blocked.', [
                'user_id' => $userId,
                'request_source' => $requestSource,
                'error' => $exception->getMessage(),
            ]);

            $errorMessages = [
                'form' => 'You are not allowed to create support tickets from this account.',
            ];
        }

        // Step 3: log unexpected failures so support issues can be investigated.
        else {
            Log::error('Support ticket submission failed unexpectedly.', [
                'user_id' => $userId,
                'request_source' => $requestSource,
                'error' => $exception->getMessage(),
            ]);

            $errorMessages = [
                'form' => 'Unable to submit support ticket right now. Please try again.',
            ];
        }

        // Step 4: send the user back with one simple retry message.
        $response = $response->withErrors($errorMessages);

        // Step 5: return the prepared response as the last step.
        return $response;
    }
}

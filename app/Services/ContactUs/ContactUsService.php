<?php

namespace App\Services\ContactUs;

use App\Models\ContactUs\ContactUsEnquiry;
use App\Models\ContactUs\EnquiryType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

class ContactUsService
{
    // This prepares the contact page data used by the view.
    public function contactPageData(): array
    {
        try {
            return [
                'enquiryTypes' => $this->activeEnquiryTypes(),
            ];
        } catch (Throwable $exception) {
            Log::error('Failed to build contact page data.', ['error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This loads the active enquiry types in business display order.
    public function activeEnquiryTypes(): Collection
    {
        try {
            return EnquiryType::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        } catch (Throwable $exception) {
            Log::error('Failed to load active enquiry types.', ['error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This returns only the active enquiry type ids used for safe form validation.
    public function activeEnquiryTypeIds(): array
    {
        try {
            return $this->activeEnquiryTypes()
                ->pluck('id')
                ->map(fn ($id): int => (int) $id)
                ->all();
        } catch (Throwable $exception) {
            Log::error('Failed to load active enquiry type ids.', ['error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This saves one contact enquiry row from the public contact page.
    public function createEnquiry(array $validated): int
    {
        try {
            // Business step: save the enquiry in one simple row so the team can start follow-up quickly.
            $contactUsEnquiry = ContactUsEnquiry::query()->create([
                'enquiry_type_id' => (int) $validated['enquiry_type_id'],
                'full_name' => trim((string) $validated['full_name']),
                'email' => trim((string) $validated['email']),
                'phone' => trim((string) $validated['phone']),
                'message' => trim((string) $validated['message']),
                'status' => 'new',
                'submitted_at' => now(),
            ]);

            Log::info('Contact enquiry saved successfully.', [
                'contact_us_enquiry_id' => $contactUsEnquiry->id,
                'enquiry_type_id' => $contactUsEnquiry->enquiry_type_id,
                'email' => $contactUsEnquiry->email,
            ]);

            return (int) $contactUsEnquiry->id;
        } catch (Throwable $exception) {
            Log::error('Failed to save contact enquiry.', [
                'enquiry_type_id' => $validated['enquiry_type_id'] ?? null,
                'email' => $validated['email'] ?? null,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }
}

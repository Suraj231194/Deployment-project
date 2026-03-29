<?php

namespace App\Http\Controllers\ContactUs;

use App\Http\Controllers\Controller;
use App\Services\ContactUs\ContactUsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class ContactUsController extends Controller
{
    // This renders the public contact page with the enquiry type master data.
    public function index(ContactUsService $contactUsService): View
    {
        try {
            // Step 1: load the active enquiry types for the contact form dropdown.
            return view('information.contact', $contactUsService->contactPageData());
        } catch (Throwable $exception) {
            Log::error('Failed to load contact us page.', ['error' => $exception->getMessage()]);

            return $this->viewWithError('information.contact', [
                'enquiryTypes' => collect(),
            ], $exception, 'Unable to load the contact page.');
        }
    }

    // This validates and stores one contact enquiry from the website.
    public function store(Request $request, ContactUsService $contactUsService): RedirectResponse
    {
        try {
            // Step 1: load the active enquiry type ids so the form only accepts business-approved options.
            $activeEnquiryTypeIds = $contactUsService->activeEnquiryTypeIds();

            if ($activeEnquiryTypeIds === []) {
                Log::warning('Contact enquiry submission blocked because no active enquiry types are configured.');

                return redirect()->back()
                    ->withInput()
                    ->withErrors([
                        'form' => 'Inquiry types are not available right now. Please try again later.',
                    ]);
            }

            // Step 2: validate the basic public contact form fields.
            $validated = $request->validate([
                'full_name' => ['required', 'string', 'max:150'],
                'email' => ['required', 'email', 'max:150'],
                'phone' => ['required', 'digits:10'],
                'enquiry_type_id' => ['required', Rule::in($activeEnquiryTypeIds)],
                'message' => ['required', 'string', 'max:500'],
            ]);

            // Step 3: store the enquiry through the service so the controller stays easy to follow.
            $contactUsEnquiryId = $contactUsService->createEnquiry($validated);

            Log::info('Contact enquiry submitted from contact page.', [
                'contact_us_enquiry_id' => $contactUsEnquiryId,
                'enquiry_type_id' => $validated['enquiry_type_id'],
            ]);

            return redirect()
                ->route('contact')
                ->with('success', 'Your enquiry has been submitted successfully. Our team will contact you soon.');
        } catch (Throwable $exception) {
            Log::error('Failed to submit contact enquiry.', ['error' => $exception->getMessage()]);

            return $this->redirectBackWithError($exception, 'Unable to submit your enquiry right now.');
        }
    }
}

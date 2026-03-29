<?php

namespace App\Http\Controllers\BookMeeting;

use App\Http\Controllers\Controller;
use App\Services\BookMeeting\BookMeetingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class BookMeetingController extends Controller
{
    // This renders the public meeting booking page.
    public function index(BookMeetingService $bookMeetingService): View
    {
        try {
            // Step 1: load the basic view data used by the meeting booking form.
            return view('information.book-meeting', $bookMeetingService->meetingPageData());
        } catch (Throwable $exception) {
            Log::error('Failed to load book meeting page.', ['error' => $exception->getMessage()]);

            return $this->viewWithError('information.book-meeting', [
                'minimumMeetingDate' => now()->toDateString(),
            ], $exception, 'Unable to load the book meeting page.');
        }
    }

    // This validates and stores one website meeting request.
    public function store(Request $request, BookMeetingService $bookMeetingService): RedirectResponse
    {
        try {
            // Step 1: validate the basic meeting request fields.
            $validated = $request->validate([
                'preferred_date' => ['required', 'date', 'after_or_equal:today'],
                'start_time' => ['required', 'date_format:H:i'],
                'end_time' => ['required', 'date_format:H:i'],
                'full_name' => ['required', 'string', 'max:150'],
                'email' => ['required', 'email', 'max:150'],
                'phone' => ['required', 'digits:10'],
                'organization_name' => ['nullable', 'string', 'max:150'],
            ]);

            // Step 2: keep the requested meeting time inside the normal business schedule window.
            $meetingStartTime = (string) config('common.meeting_hours.start_time', '09:00');
            $meetingEndTime = (string) config('common.meeting_hours.end_time', '18:00');
            $meetingTimezoneLabel = (string) config('common.meeting_hours.timezone_label', 'IST');

            if ($validated['start_time'] < $meetingStartTime || $validated['end_time'] > $meetingEndTime) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors([
                        'time_range' => "Meeting time must stay between {$meetingStartTime} and {$meetingEndTime} {$meetingTimezoneLabel}.",
                    ]);
            }

            // Step 3: make sure the ending time is after the starting time.
            if ($validated['end_time'] <= $validated['start_time']) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors([
                        'end_time' => 'End time must be later than start time.',
                    ]);
            }

            // Step 4: store the meeting request through the service so the controller stays easy to follow.
            $meetingRequestId = $bookMeetingService->createMeetingRequest($validated);

            Log::info('Meeting request submitted from book meeting page.', [ 'meeting_request_id' => $meetingRequestId,  'preferred_date' => $validated['preferred_date'],  ]);

            return redirect()
                ->route('book-meeting')
                ->with('success', 'Your meeting request has been submitted successfully. Our team will confirm it soon.');
        } catch (Throwable $exception) {
            Log::error('Failed to submit meeting request.', ['error' => $exception->getMessage()]);

            return $this->redirectBackWithError($exception, 'Unable to submit your meeting request right now.');
        }
    }
}

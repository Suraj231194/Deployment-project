<?php

namespace App\Services\BookMeeting;

use App\Models\BookMeeting\MeetingRequest;
use Illuminate\Support\Facades\Log;
use Throwable;

class BookMeetingService
{
    // This prepares the meeting page data used by the view.
    public function meetingPageData(): array
    {
        try {
            return [
                'minimumMeetingDate' => now()->toDateString(),
            ];
        } catch (Throwable $exception) {
            Log::error('Failed to build book meeting page data.', ['error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This saves one meeting request from the website form.
    public function createMeetingRequest(array $validated): int
    {
        try {
            // Business step: save the request in one row so the team can review and confirm the schedule quickly.
            $meetingRequest = MeetingRequest::query()->create([
                'full_name' => trim((string) $validated['full_name']),
                'email' => trim((string) $validated['email']),
                'phone' => trim((string) $validated['phone']),
                'organization_name' => filled($validated['organization_name'] ?? null)
                    ? trim((string) $validated['organization_name'])
                    : null,
                'preferred_date' => $validated['preferred_date'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'status' => 'new',
                'submitted_at' => now(),
            ]);

            Log::info('Meeting request saved successfully.', [   'meeting_request_id' => $meetingRequest->id, 'email' => $meetingRequest->email,  'preferred_date' => $meetingRequest->preferred_date?->format('Y-m-d'),  ]);
            return (int) $meetingRequest->id;
        } catch (Throwable $exception) {
            Log::error('Failed to save meeting request.', [  'email' => $validated['email'] ?? null, 'preferred_date' => $validated['preferred_date'] ?? null,  'error' => $exception->getMessage(), ]);
            throw $exception;
        }
    }
}

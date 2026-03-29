<?php

namespace App\Http\Controllers\Quize;

use App\Http\Controllers\Controller;
use App\Services\Quize\QuizeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class QuizeController extends Controller
{
    // This renders the public diagnostic quiz page with database-backed quiz content.
    public function index(QuizeService $quizeService): View
    {
        try {
            // Step 1: load the quiz questions and supporting details used by the existing UI.
            return view('information.diagnostic-quiz', $quizeService->quizePageData());
        } catch (Throwable $exception) {
            Log::error('Failed to load diagnostic quiz page.', ['error' => $exception->getMessage()]);

            // Step 2: return the page without the shared error bag so the quiz view can decide its own fallback message.
            return view('information.diagnostic-quiz', [
                'quizQuestions' => collect(),
            ]);
        }
    }

    // This validates the submitted quiz payload and returns the score result for the current page.
    public function store(Request $request, QuizeService $quizeService): JsonResponse
    {
        try {
            // Step 1: validate the participant details and the selected answers sent from the quiz page.
            $validatedInput = $request->validate([
                'participant_first_name' => ['required', 'string', 'max:100'],
                'participant_last_name' => ['nullable', 'string', 'max:100'],
                'participant_email' => ['required', 'email', 'max:150'],
                'selected_answers' => ['required', 'array', 'min:1'],
                'selected_answers.*' => ['required', 'integer'],
            ]);

            // Step 2: save the response and calculate the final result through the shared quiz service.
            $quizResponseResult = $quizeService->createQuizResponse($validatedInput);

            return response()->json([
                'status' => 'success',
                'message' => 'Quiz submitted successfully.',
                'data' => $quizResponseResult,
            ]);
        } catch (ValidationException $exception) {
            Log::warning('Diagnostic quiz submit validation failed.', [
                'participant_email' => $request->input('participant_email'),
                'errors' => $exception->errors(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => collect($exception->errors())->flatten()->first() ?: 'Please review the quiz details and try again.',
                'errors' => $exception->errors(),
            ], 422);
        } catch (Throwable $exception) {
            Log::error('Diagnostic quiz submit request failed.', [
                'participant_email' => $request->input('participant_email'),
                'error' => $exception->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Unable to submit the quiz right now. Please try again.',
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;
// Base controller providing common methods for error handling and response formatting across the application.
abstract class Controller
{
    // This returns the user to the previous page with form-friendly errors.
    protected function redirectBackWithError(Throwable $exception, string $defaultMessage): RedirectResponse
    {
        if ($exception instanceof ValidationException) {
            return redirect()->back()
                ->withInput()
                ->withErrors($exception->errors());
        }

        return redirect()->back()
            ->withInput()
            ->withErrors([
                'form' => $this->resolveErrorMessage($exception, $defaultMessage),
            ]);
    }

    // This returns the same view with a normal Laravel error bag.
    protected function viewWithError(string $viewName, array $data, Throwable $exception, string $defaultMessage): View
    {
        $errors = new ViewErrorBag();
        $errors->put('default', new MessageBag([
            'page' => $this->resolveErrorMessage($exception, $defaultMessage),
        ]));

        return view($viewName, array_merge($data, [
            'errors' => $errors,
        ]));
    }

    // This converts known exceptions into a simple message for the UI.
    protected function resolveErrorMessage(Throwable $exception, string $defaultMessage): string
    {
        if ($exception instanceof ValidationException) {
            $messages = collect($exception->errors())->flatten()->filter()->values();

            return $messages->first() ?: $defaultMessage;
        }

        if ($exception instanceof QueryException) {
            return 'Database operation failed. Please check the submitted data.';
        }

        return $defaultMessage;
    }
}

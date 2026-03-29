<?php

namespace App\Http\Controllers\Faq;

use App\Http\Controllers\Controller;
use App\Services\Faq\FaqService;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class FaqController extends Controller
{
    // This only loads FAQ rows from the database and passes them to the static page view.
    public function index(FaqService $faqService): View
    {
        try {
            $faqs = $faqService->getFaqs();

            return view('information.faq', [
                'faqs' => $faqs,
            ]);
        } catch (Throwable $exception) {
            Log::error('Failed to load FAQ public page.', ['error' => $exception->getMessage()]);

            return $this->viewWithError('information.faq', [
                'faqs' => collect(),
            ], $exception, 'Unable to load the FAQ page right now.');
        }
    }
}

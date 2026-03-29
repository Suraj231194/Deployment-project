<?php

namespace App\Http\Controllers\Quotation;

use App\Http\Controllers\Controller;
use App\Services\Quotation\QuotationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class QuotationController extends Controller
{
    public function showCreatePage(Request $request, QuotationService $quotationService): View
    {
        try {
            // Step 1: load the quotation form with visible products and allowed companies.
            $selectedProductId = $request->integer('product_id');
            $pageData = $quotationService->getCreatePageData($request->user(), $selectedProductId);

            return view('information.generate-quotation', $pageData);
        } catch (Throwable $exception) {
            Log::error('Failed to load quotation page.', ['error' => $exception->getMessage()]);

            return $this->viewWithError('information.generate-quotation', [
                'products' => collect(),
                'clientCompanies' => collect(),
                'prefilledProductId' => $request->integer('product_id'),
            ], $exception, 'Unable to load quotation form.');
        }
    }

    public function generate(Request $request, QuotationService $quotationService): Response|RedirectResponse
    {
        try {
            // Step 1: validate the submitted quotation details.
            $quotationData = $this->validateQuotationRequest($request);
            $signedInUser = $request->user();

            // Step 2: prepare item rows and calculate the final totals.
            $quotationItems = $quotationService->prepareItems($quotationData, $signedInUser);
            $quotationTotals = $quotationService->calculateTotals($quotationItems);
            $quotationNumber = $this->createQuotationNumber();

            // Step 3: save the quotation and load the final PDF record.
            $quotation = $quotationService->saveQuotation(
                $quotationData,
                $signedInUser,
                $quotationItems,
                $quotationTotals,
                $quotationNumber,
                $request->session()->getId(),
            );

            // Step 4: return the quotation PDF file.
            return $quotationService->downloadPdf($quotation);
        } catch (Throwable $exception) {
            Log::error('Failed to create quotation.', ['error' => $exception->getMessage()]);

            return $this->redirectBackWithError($exception, 'Unable to generate quotation.');
        }
    }

    protected function validateQuotationRequest(Request $request): array
    {
        return $request->validate([
            'product_id' => ['required', 'array', 'min:1'],
            'product_id.*' => ['nullable', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'array', 'min:1'],
            'quantity.*' => ['nullable', 'integer', 'min:1'],
            'purpose' => ['required', 'in:self,other'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:40'],
            'target_company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
    }

    protected function createQuotationNumber(): string
    {
        $datePart = now()->format('YmdHis');
        $randomPart = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        return 'QT-'.$datePart.'-'.$randomPart;
    }
}

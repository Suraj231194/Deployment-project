<?php

namespace App\Http\Controllers\Proforma;

use App\Http\Controllers\Controller;
use App\Services\Proforma\ProformaInvoiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class ProformaInvoiceController extends Controller
{
     // this method will return the view with form to submit a new PI request, and the form will be pre-filled with the selected product if product_id is provided in the query string.
    public function showRequestPage(Request $request, ProformaInvoiceService $proformaInvoiceService): View
    {
        try {
            // Step 1: load the PI request page with visible products and allowed companies.
            $selectedProductId = $request->integer('product_id');
            $pageData = $proformaInvoiceService->getRequestPageData($request->user(), $selectedProductId);

            return view('information.pi-quotation', $pageData);
        } catch (Throwable $exception) {
            Log::error('Failed to load PI request page.', ['error' => $exception->getMessage()]);

            return $this->viewWithError('information.pi-quotation', [
                'products' => collect(),
                'clientCompanies' => collect(),
                'prefilledProductId' => $request->integer('product_id'),
            ], $exception, 'Unable to load PI request form.');
        }
    }

    public function submitRequest(Request $request, ProformaInvoiceService $proformaInvoiceService): RedirectResponse
    {
        try {
            // Step 1: validate the submitted PI request details.
            $requestData = $this->validateProformaRequest($request);
            $signedInUser = $request->user();

            // Step 2: prepare item rows and calculate the final request totals.
            $requestedItems = $proformaInvoiceService->prepareItems($requestData, $signedInUser);
            $requestTotals = $proformaInvoiceService->calculateTotals($requestedItems);
            $requestNumber = $this->createRequestNumber();

            // Step 3: save the PI request for later internal review.
            $proformaInvoiceService->saveRequestedProforma(
                $requestData,
                $signedInUser,
                $requestedItems,
                $requestTotals,
                $requestNumber,
                $request->session()->getId(),
            );

            // Step 4: store a simple activity log for the submitted request.
            $proformaInvoiceService->recordRequestSubmitted(
                $signedInUser,
                $request->session()->getId(),
                $request->path(),
                $requestNumber,
            );

            return redirect()->route('pi-quotation.generate')
                ->with('status', "PI request {$requestNumber} submitted successfully. Our team will review it and issue the final PI after verification.");
        } catch (Throwable $exception) {
            Log::error('Failed to submit PI request.', ['error' => $exception->getMessage()]);

            return $this->redirectBackWithError($exception, 'Unable to submit PI request.');
        }
    }

    protected function validateProformaRequest(Request $request): array
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

    protected function createRequestNumber(): string
    {
        $datePart = now()->format('YmdHis');
        $randomPart = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        return 'PI-REQ-'.$datePart.'-'.$randomPart;
    }
}

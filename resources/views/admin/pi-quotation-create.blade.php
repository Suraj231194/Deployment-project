@extends('admin.layout')

@section('title', 'Generate PI Quotation - Biogenix Admin')

@section('admin_content')
<div class="w-full py-8">

    <!-- Back Arrow + Breadcrumb -->
    <div class="flex items-center gap-3 mb-4">
        <a href="{{ route('admin.pi-quotation.index') }}" class="ajax-link h-8 w-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-50 hover:border-slate-300 transition shrink-0 cursor-pointer" title="Back to Quotations">
            <svg class="h-4 w-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <nav class="flex text-[13px] text-slate-500 font-medium">
            <a href="{{ route('admin.dashboard') }}" class="ajax-link hover:text-slate-900 transition flex items-center gap-1.5 cursor-pointer">
                Admin
            </a>
            <span class="mx-2 text-slate-300">/</span>
            <a href="{{ route('admin.pi-quotation.index') }}" class="ajax-link hover:text-slate-900 transition flex items-center gap-1.5 cursor-pointer">
                Quotation / PI
            </a>
            <span class="mx-2 text-slate-300">/</span>
            <span class="text-slate-900 font-semibold cursor-pointer">Generate PI Quotation</span>
        </nav>
    </div>

    {{-- ─── Page Header ─── --}}
    <div class="mb-5 flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Generate PI Quotation</h1>
            <p class="text-sm text-slate-500 mt-1">Create a new Proforma Invoice with product and billing details.</p>
        </div>
    </div>

    {{-- â•â•â• PI Header Info â•â•â• --}}
    <div class="mb-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-5 flex items-center gap-2.5">
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-primary-600 text-white">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </span>
            <h2 class="text-lg font-bold tracking-tight text-slate-900">PI Header Info</h2>
        </div>
        <div class="grid grid-cols-2 gap-5 sm:grid-cols-4">
            <div>
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-widest text-slate-500">PI Number</label>
                <input id="piNumber" type="text" readonly
                    class="h-10 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-sm font-semibold text-slate-800 outline-none">
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-widest text-slate-500">Date</label>
                <input id="piDate" type="date"
                    class="h-10 w-full rounded-lg border border-slate-200 bg-white px-3 text-sm text-slate-800 outline-none focus:border-primary-600">
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-widest text-slate-500">State</label>
                <input id="piStateCode" type="text" placeholder="27 (Maharashtra)"
                    class="h-10 w-full rounded-lg border border-slate-200 bg-white px-3 text-sm text-slate-800 outline-none placeholder:text-slate-400 focus:border-primary-600">
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-widest text-slate-500">GSTIN</label>
                <input id="piGstin" type="text" placeholder="27AAACB1234F1Z5"
                    class="h-10 w-full rounded-lg border border-slate-200 bg-white px-3 text-sm text-slate-800 outline-none placeholder:text-slate-400 focus:border-primary-600">
            </div>
        </div>
    </div>

    {{-- â•â•â• Customer Details â•â•â• --}}
    <div class="mb-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-5 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-primary-600 text-white">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M5.5 21a7.5 7.5 0 0113 0"/></svg>
                </span>
                <h2 class="text-lg font-bold tracking-tight text-slate-900">Customer Details</h2>
            </div>
            <div class="flex items-center gap-2 select-none">
                <span class="text-sm font-semibold text-slate-600">Same as Billing</span>
                <button id="toggleTrack" type="button" role="switch" aria-checked="false"
                    class="relative h-6 w-[42px] rounded-full bg-slate-300 transition-colors duration-200 cursor-pointer">
                    <span id="toggleThumb"
                        class="absolute left-[3px] top-[3px] h-[18px] w-[18px] translate-x-0 rounded-full bg-white shadow-[0_1px_3px_rgba(0,0,0,0.2)] transition-transform duration-200"></span>
                </button>
                <input id="sameAsBilling" type="checkbox" class="hidden">
            </div>
        </div>

        <div class="mb-1.5 grid grid-cols-1 gap-5 md:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-widest text-slate-500">Billing Address</label>
                <textarea id="billingAddress" rows="4" placeholder="Enter full billing address..."
                    class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-800 outline-none placeholder:text-slate-400 focus:border-primary-600"></textarea>
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-widest text-slate-500">Shipping Address</label>
                <textarea id="shippingAddress" rows="4" placeholder="Enter full shipping address..."
                    class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-800 outline-none placeholder:text-slate-400 focus:border-primary-600"></textarea>
            </div>
        </div>

        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
                <input id="contactPerson" type="text" placeholder="Contact Person"
                    class="h-11 w-full rounded-lg border border-slate-200 bg-white px-3 text-sm text-slate-800 outline-none placeholder:text-slate-400 focus:border-primary-600">
            </div>
            <div>
                <input id="customerGstin" type="text" placeholder="GSTIN (Customer)"
                    class="h-11 w-full rounded-lg border border-slate-200 bg-white px-3 text-sm text-slate-800 outline-none placeholder:text-slate-400 focus:border-primary-600">
            </div>
            <div>
                <input id="deliveryPhone" type="text" placeholder="Delivery Contact / Phone"
                    class="h-11 w-full rounded-lg border border-slate-200 bg-white px-3 text-sm text-slate-800 outline-none placeholder:text-slate-400 focus:border-primary-600">
            </div>
        </div>
    </div>

    {{-- â•â•â• Product Details â•â•â• --}}
    <div class="mb-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-primary-600 text-white">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </span>
                <h2 class="text-lg font-bold tracking-tight text-slate-900">Product Details</h2>
            </div>
            <button id="addProductRow" type="button"
                class="inline-flex items-center gap-1.5 rounded-xl bg-primary-600 px-4 py-2 text-[0.82rem] font-bold text-white shadow-[0_2px_8px_rgba(26,77,46,0.2)] transition-colors hover:bg-primary-700 cursor-pointer">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14m-7-7h14"/></svg>
                Add Product Row
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="productTable">
                <thead>
                    <tr class="border-b-2 border-slate-200 bg-slate-50">
                        <th class="w-[45px] whitespace-nowrap px-2 py-2.5 text-center text-xs font-bold uppercase tracking-wider text-slate-500">S.No</th>
                        <th class="w-[130px] whitespace-nowrap px-2 py-2.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Cat. No</th>
                        <th class="min-w-[130px] whitespace-nowrap px-2 py-2.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Product Name</th>
                        <th class="w-[100px] whitespace-nowrap px-2 py-2.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Pack Size</th>
                        <th class="w-[60px] whitespace-nowrap px-2 py-2.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Qty</th>
                        <th class="w-[95px] whitespace-nowrap px-2 py-2.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Rate (&#8377;)</th>
                        <th class="w-[95px] whitespace-nowrap px-2 py-2.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Value (&#8377;)</th>
                        <th class="w-[60px] whitespace-nowrap px-2 py-2.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500">GST %</th>
                        <th class="w-[100px] whitespace-nowrap px-2 py-2.5 text-right text-xs font-bold uppercase tracking-wider text-slate-500">Total (&#8377;)</th>
                        <th class="w-9"></th>
                    </tr>
                </thead>
                <tbody id="productTableBody">
                </tbody>
            </table>
            <p id="emptyTableMsg" class="py-6 text-center text-sm text-slate-400">No products added yet. Click "Add Product Row" to add items.</p>
        </div>
    </div>

    {{-- â•â•â• Bottom: Terms & Totals â•â•â• --}}
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

        {{-- Terms & Conditions --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-3 flex items-center gap-2.5">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-primary-600 text-white">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </span>
                <h2 class="text-lg font-bold tracking-tight text-slate-900">Terms & Conditions</h2>
            </div>
            <ol id="termsList" class="space-y-2">
                <li class="flex items-baseline gap-2 text-sm text-slate-700">
                    <span class="shrink-0 font-bold text-primary-600">1.</span>
                    <input type="text" value="Supply within 3-4 week after confirmation order along with 100% advance payment."
                        class="w-full border-0 bg-transparent p-0 text-sm text-slate-700 outline-none">
                </li>
                <li class="flex items-baseline gap-2 text-sm text-slate-700">
                    <span class="shrink-0 font-bold text-primary-600">2.</span>
                    <input type="text" value="All Disputes are subject to Lucknow Jurisdiction only"
                        class="w-full border-0 bg-transparent p-0 text-sm text-slate-700 outline-none">
                </li>
            </ol>
            <button id="addTermBtn" type="button" class="mt-3 text-xs font-semibold text-primary-600 hover:underline cursor-pointer">+ Add Term</button>
        </div>

        {{-- Actions and Summary / Totals --}}
        <div>
            <!-- Save/Preview actions -->
            <div class="mb-5 flex items-center gap-3">
                <button id="saveDraftTopBtn" type="button"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-200/60 px-5 py-2.5 text-sm font-bold text-slate-700 transition-colors hover:bg-slate-300 cursor-pointer">
                    Save Draft
                </button>
                <button id="previewPiTopBtn" type="button"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-700 transition-colors hover:bg-slate-50 cursor-pointer">
                    Preview PI
                </button>
                <div class="flex-1 text-right">
                    <button id="sendEmailTopBtn" type="button"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-secondary-600 px-5 py-2.5 text-sm font-bold text-primary-800 shadow-sm transition-colors hover:bg-secondary-500 cursor-pointer">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Send Email
                    </button>
                    <button id="generatePdfTopBtn" type="button"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-800 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition-colors hover:bg-primary-700 cursor-pointer ml-3">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                        Generate PDF
                    </button>
                </div>
            </div>

        <div class="rounded-2xl bg-primary-600 p-6 text-white shadow-lg">
            <div class="space-y-3">
                <div class="flex justify-between border-b border-primary-500/30 pb-3 text-sm">
                    <span class="text-primary-100/80">Subtotal</span>
                    <span class="font-bold text-white" id="sumSubtotal">₹ 0.00</span>
                </div>
                <div class="flex justify-between border-b border-primary-500/30 pb-3 text-sm">
                    <span class="text-primary-100/80">Freight Charges</span>
                    <input id="freightCharges" type="number" value="0" min="0" step="1"
                        class="w-20 rounded-lg border border-primary-500/50 bg-primary-700/50 px-2 py-1 text-right text-sm font-bold text-white outline-none focus:border-secondary-400">
                </div>
                <div class="flex justify-between border-b border-primary-500/30 pb-3 text-sm">
                    <span class="text-primary-100/80">Freight GST (18%)</span>
                    <span class="font-bold text-white" id="sumFreightTax">₹ 0.00</span>
                </div>
                <div class="flex justify-between pb-1 text-sm">
                    <span class="text-primary-100/80">Round Off</span>
                    <span class="font-bold text-white" id="sumRoundOff">₹ 0.00</span>
                </div>
            </div>

            <div class="mt-6 flex flex-col items-end border-t border-primary-500/50 pt-6">
                <p class="mb-1 text-[0.65rem] font-bold uppercase tracking-widest text-secondary-600">Grand Total</p>
                <div class="flex items-center gap-2">
                    <span class="text-3xl font-extrabold text-secondary-600">₹</span>
                    <span class="text-4xl font-black text-secondary-600 tracking-tight" id="sumGrandTotal">0.00</span>
                </div>
                <div class="mt-3 w-full text-left">
                    <p class="text-[0.65rem] font-bold uppercase tracking-[0.2em] text-secondary-600">Amount in Words</p>
                    <p class="mt-1 text-xs font-medium italic text-primary-100/90" id="sumAmountWords">Zero Only</p>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <button id="sendEmailBtn" type="button"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-secondary-600 px-5 py-3.5 text-sm font-bold text-primary-800 shadow-[0_4px_14px_rgba(253,224,71,0.25)] transition-all hover:bg-secondary-500 hover:-translate-y-px cursor-pointer">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Send Email
                </button>
                <button id="generatePdfBtn" type="button"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-800 px-5 py-3.5 text-sm font-bold text-white shadow-[0_4px_14px_rgba(15,23,42,0.25)] transition-all hover:bg-primary-700 hover:-translate-y-px cursor-pointer">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                    Generate PDF
                </button>
            </div>
        </div>
    </div>
</div>

{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     ADD PRODUCT MODAL
     â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
<div id="addProductModal" class="fixed inset-0 z-[9999] hidden">
    {{-- Backdrop --}}
    <div id="modalBackdrop" class="absolute inset-0 bg-slate-950/55 opacity-0 backdrop-blur-[4px] transition-opacity duration-300"></div>

    {{-- Dialog --}}
    <div class="fixed inset-0 flex items-center justify-center p-4 pointer-events-none">
        <div id="modalDialog" class="pointer-events-auto relative w-full max-w-[520px] translate-y-[10px] scale-95 rounded-[20px] bg-white p-8 opacity-0 shadow-[0_25px_60px_rgba(15,23,42,0.22)] transition-all duration-300 ease-[cubic-bezier(0.32,0.72,0,1)]">

            {{-- Close button --}}
            <button id="modalCloseBtn" type="button"
                class="absolute right-4 top-4 inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 transition-colors hover:bg-slate-100 cursor-pointer">
                <svg class="h-[18px] w-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            {{-- Header --}}
            <h3 class="text-[1.3rem] font-extrabold text-slate-800">Add Product to Invoice</h3>
            <p class="mt-1 text-[0.82rem] text-slate-500">Search and configure product details for Biogenix Inventory</p>

            {{-- Form fields --}}
            <div class="mt-6 grid gap-4 md:grid-cols-2">
                {{-- Cat NO --}}
                <div>
                    <label class="mb-1.5 block text-[0.78rem] font-bold text-slate-800">Cat NO</label>
                    <div class="flex h-11 items-center overflow-hidden rounded-[10px] border border-slate-200 bg-white focus-within:border-primary-600">
                        <span class="flex h-full w-[38px] shrink-0 items-center justify-center text-slate-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                        </span>
                        <input id="modalCatNo" type="text" placeholder="e.g., BG-9920"
                            class="h-full flex-1 bg-transparent pr-3 text-sm text-slate-800 outline-none placeholder:text-slate-400">
                    </div>
                </div>

                {{-- Product Name --}}
                <div>
                    <label class="mb-1.5 block text-[0.78rem] font-bold text-slate-800">Product Name</label>
                    <div id="modalProductNameField" class="flex h-11 items-center overflow-hidden rounded-[10px] border border-slate-200 bg-white focus-within:border-primary-600">
                        <span class="flex h-full w-[38px] shrink-0 items-center justify-center text-slate-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                        </span>
                        <input id="modalProductName" type="text" placeholder="Search or select product"
                            class="h-full flex-1 bg-transparent pr-3 text-sm text-slate-800 outline-none placeholder:text-slate-400">
                    </div>
                </div>

                {{-- Pack Size (read-only styled) --}}
                <div>
                    <label class="mb-1.5 block text-[0.78rem] font-bold text-slate-800">Pack Size</label>
                    <div class="flex h-11 items-center overflow-hidden rounded-[10px] border border-slate-200 bg-slate-100">
                        <span class="flex h-full w-[38px] shrink-0 items-center justify-center text-slate-500">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </span>
                        <input id="modalPackSize" type="text" placeholder="e.g. Box of 5"
                            class="h-full flex-1 bg-transparent pr-3 text-sm text-slate-600 outline-none placeholder:text-slate-400">
                    </div>
                </div>

                {{-- Quantity --}}
                <div>
                    <label class="mb-1.5 block text-[0.78rem] font-bold text-slate-800">Quantity</label>
                    <div class="flex h-11 items-center overflow-hidden rounded-[10px] border border-slate-200 bg-white focus-within:border-primary-600">
                        <span class="flex h-full w-[38px] shrink-0 items-center justify-center text-slate-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 3v4M8 3v4"/></svg>
                        </span>
                        <input id="modalQty" type="number" min="1" value="1" placeholder="Enter qty"
                            class="h-full flex-1 bg-transparent pr-3 text-sm text-slate-800 outline-none placeholder:text-slate-400">
                    </div>
                </div>

                {{-- Rate (Unit Price) --}}
                <div>
                    <label class="mb-1.5 block text-[0.78rem] font-bold text-slate-800">Rate (Unit Price)</label>
                    <div class="flex h-11 items-center overflow-hidden rounded-[10px] border border-slate-200 bg-slate-100">
                        <span class="flex h-full w-[38px] shrink-0 items-center justify-center text-slate-500">
                           <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M3 3h18v2H3V3zm0 4h10a5 5 0 010 10H9l6 6h-4l-6-6v-2h8a3 3 0 000-6H3V7z"/>
                            </svg>
                         </span>
                        <input id="modalRate" type="number" min="0" step="0.01" value="0" placeholder="&#8377; 0.00"
                            class="h-full flex-1 bg-transparent pr-3 text-sm text-slate-600 outline-none placeholder:text-slate-400">
                    </div>
                </div>

                {{-- GST % --}}
                <div>
                    <label class="mb-1.5 block text-[0.78rem] font-bold text-slate-800">GST (%)</label>
                    <div class="flex h-11 items-center overflow-hidden rounded-[10px] border border-slate-200 bg-white focus-within:border-primary-600">
                        <span class="flex h-full w-[38px] shrink-0 items-center justify-center text-[0.82rem] font-bold text-slate-400">%</span>
                        <input id="modalGst" type="number" min="0" max="100" step="0.01" value="18"
                            class="h-full flex-1 bg-transparent pr-3 text-sm text-slate-800 outline-none">
                    </div>
                </div>
            </div>

            {{-- Total Calculated Amount --}}
            <div class="mt-5 flex items-center justify-between rounded-[14px] border border-slate-200 bg-slate-50 px-5 py-4">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-primary-50 text-primary-600">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                    </span>
                    <span class="text-[0.88rem] font-semibold text-slate-600">Total Calculated Amount</span>
                </div>
                <div class="text-right">
                    <p class="text-[0.65rem] font-bold uppercase tracking-[0.1em] text-primary-600">INCL. TAXES</p>
                    <p id="modalTotalAmount" class="text-2xl font-extrabold text-slate-800">&#8377; 0.00</p>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="mt-6 flex justify-end gap-3">
                <button id="modalCancelBtn" type="button"
                    class="h-11 rounded-xl border border-slate-200 bg-white px-6 text-sm font-semibold text-slate-600 transition-colors hover:bg-slate-50 cursor-pointer">
                    Cancel
                </button>
                <button id="modalAddBtn" type="button"
                    class="inline-flex h-11 items-center gap-2 rounded-xl bg-primary-600 px-7 text-sm font-bold text-white shadow-[0_2px_10px_rgba(26,77,46,0.25)] transition-colors hover:bg-primary-700 cursor-pointer">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                    Add to Invoice
                </button>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // â”€â”€â”€ Auto-fill PI Number and Date â”€â”€â”€
    var now = new Date();
    var fy = now.getMonth() >= 3 ? now.getFullYear() : now.getFullYear() - 1;
    var piNum = 'PI/' + fy + '-' + String(fy + 1).slice(2) + '/' + String(Math.floor(1000 + Math.random() * 9000));
    document.getElementById('piNumber').value = piNum;
    document.getElementById('piDate').value = now.toISOString().split('T')[0];

    // â”€â”€â”€ Same as Billing toggle â”€â”€â”€
    var toggleTrack = document.getElementById('toggleTrack');
    var toggleThumb = document.getElementById('toggleThumb');
    var sameCheckbox = document.getElementById('sameAsBilling');
    var billingAddr = document.getElementById('billingAddress');
    var shippingAddr = document.getElementById('shippingAddress');
    var toggleOn = false;

    function syncShippingToggleState() {
        sameCheckbox.checked = toggleOn;
        toggleTrack.setAttribute('aria-checked', toggleOn ? 'true' : 'false');
        toggleTrack.classList.toggle('bg-primary-600', toggleOn);
        toggleTrack.classList.toggle('bg-slate-300', !toggleOn);
        toggleThumb.classList.toggle('translate-x-[18px]', toggleOn);
        toggleThumb.classList.toggle('translate-x-0', !toggleOn);

        if (toggleOn) {
            shippingAddr.value = billingAddr.value;
        }

        shippingAddr.readOnly = toggleOn;
        shippingAddr.classList.toggle('bg-slate-100', toggleOn);
        shippingAddr.classList.toggle('bg-white', !toggleOn);
    }

    toggleTrack.addEventListener('click', function () {
        toggleOn = !toggleOn;
        syncShippingToggleState();
    });

    billingAddr.addEventListener('input', function () {
        if (toggleOn) { shippingAddr.value = billingAddr.value; }
    });

    // â”€â”€â”€ Product Table â”€â”€â”€
    var tableBody = document.getElementById('productTableBody');
    var emptyMsg = document.getElementById('emptyTableMsg');

    function addRowToTable(data) {
        var rows = tableBody.querySelectorAll('.product-row');
        var sno = rows.length + 1;
        var value = data.qty * data.rate;
        var gstAmt = value * data.gst / 100;
        var total = value + gstAmt;

        var tr = document.createElement('tr');
        tr.className = 'product-row border-b border-slate-100';
        tr.innerHTML =
            '<td class="sno px-2 py-2 text-center text-sm font-semibold text-slate-500">' + String(sno).padStart(2, '0') + '</td>' +
            '<td class="px-2 py-2 text-[0.85rem] text-slate-800">' + escHtml(data.catNo) + '</td>' +
            '<td class="px-2 py-2 text-[0.85rem] text-slate-800">' + escHtml(data.productName) + '</td>' +
            '<td class="px-2 py-2 text-[0.85rem] text-slate-800">' + escHtml(data.packSize) + '</td>' +
            '<td class="row-qty-val px-2 py-2 text-center text-[0.85rem] text-slate-800">' + data.qty + '</td>' +
            '<td class="row-rate-val px-2 py-2 text-right text-[0.85rem] text-slate-800">' + formatNum(data.rate) + '</td>' +
            '<td class="row-value px-2 py-2 text-right text-[0.85rem] text-slate-600">' + formatNum(value) + '</td>' +
            '<td class="row-gst-val px-2 py-2 text-center text-[0.85rem] text-slate-800">' + data.gst + '</td>' +
            '<td class="row-total px-2 py-2 text-right text-sm font-bold text-slate-800">' + formatNum(total) + '</td>' +
            '<td class="px-2 py-2 text-center"><button type="button" class="del-row-btn inline-flex h-[30px] w-[30px] items-center justify-center rounded-lg border border-rose-200 text-rose-600 transition-colors hover:bg-rose-50 cursor-pointer"><svg class="h-[15px] w-[15px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></td>';

        // Store data attributes for calc
        tr.setAttribute('data-qty', data.qty);
        tr.setAttribute('data-rate', data.rate);
        tr.setAttribute('data-gst', data.gst);

        tableBody.appendChild(tr);

        // Delete button
        tr.querySelector('.del-row-btn').addEventListener('click', function () {
            tr.remove();
            renumberRows();
            recalcTotals();
            toggleEmptyMsg();
        });

        renumberRows();
        recalcTotals();
        toggleEmptyMsg();
    }

    function escHtml(str) {
        var div = document.createElement('div');
        div.textContent = str || '';
        return div.innerHTML;
    }

    function renumberRows() {
        tableBody.querySelectorAll('.product-row').forEach(function (r, i) {
            r.querySelector('.sno').textContent = String(i + 1).padStart(2, '0');
        });
    }

    function toggleEmptyMsg() {
        var rows = tableBody.querySelectorAll('.product-row');
        emptyMsg.classList.toggle('hidden', rows.length > 0);
    }

    function recalcTotals() {
        var subtotal = 0;
        var gstTotal = 0;
        tableBody.querySelectorAll('.product-row').forEach(function (row) {
            var qty = parseFloat(row.getAttribute('data-qty')) || 0;
            var rate = parseFloat(row.getAttribute('data-rate')) || 0;
            var gstPct = parseFloat(row.getAttribute('data-gst')) || 0;
            var val = qty * rate;
            subtotal += val;
            gstTotal += val * gstPct / 100;
        });

        var freight = parseFloat(document.getElementById('freightCharges').value) || 0;
        var freightTax = freight * 0.18;
        var rawTotal = subtotal + gstTotal + freight + freightTax;
        var rounded = Math.round(rawTotal);
        var roundOff = rounded - rawTotal;

        document.getElementById('sumSubtotal').textContent = '\u20B9 ' + formatNum(subtotal);
        document.getElementById('sumGst').textContent = '\u20B9 ' + formatNum(gstTotal);
        document.getElementById('sumFreightTax').textContent = '\u20B9 ' + formatNum(freightTax);
        document.getElementById('sumRoundOff').textContent = '\u20B9 ' + (roundOff >= 0 ? '' : '-') + formatNum(Math.abs(roundOff));
        document.getElementById('sumGrandTotal').textContent = '\u20B9 ' + formatNum(rounded);
        document.getElementById('sumAmountWords').textContent = numberToWords(rounded) + ' Only';
    }

    function formatNum(n) {
        return Number(n).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function numberToWords(num) {
        if (num === 0) return 'Zero';
        var ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine',
            'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        var tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
        function convert(n) {
            if (n < 0) return 'Minus ' + convert(-n);
            if (n < 20) return ones[n];
            if (n < 100) return tens[Math.floor(n / 10)] + (n % 10 ? ' ' + ones[n % 10] : '');
            if (n < 1000) return ones[Math.floor(n / 100)] + ' Hundred' + (n % 100 ? ' ' + convert(n % 100) : '');
            if (n < 100000) return convert(Math.floor(n / 1000)) + ' Thousand' + (n % 1000 ? ' ' + convert(n % 1000) : '');
            if (n < 10000000) return convert(Math.floor(n / 100000)) + ' Lakh' + (n % 100000 ? ' ' + convert(n % 100000) : '');
            return convert(Math.floor(n / 10000000)) + ' Crore' + (n % 10000000 ? ' ' + convert(n % 10000000) : '');
        }
        return convert(Math.abs(Math.round(num)));
    }

    // â”€â”€â”€ Modal logic â”€â”€â”€
    var modal = document.getElementById('addProductModal');
    // Move modal to body to guarantee it breaks out of relative stacking contexts
    document.body.appendChild(modal);
    
    var backdrop = document.getElementById('modalBackdrop');
    var dialog = document.getElementById('modalDialog');
    var closeBtn = document.getElementById('modalCloseBtn');
    var cancelBtn = document.getElementById('modalCancelBtn');
    var addBtn = document.getElementById('modalAddBtn');

    var mCatNo = document.getElementById('modalCatNo');
    var mProdName = document.getElementById('modalProductName');
    var mProdNameField = document.getElementById('modalProductNameField');
    var mPackSize = document.getElementById('modalPackSize');
    var mQty = document.getElementById('modalQty');
    var mRate = document.getElementById('modalRate');
    var mGst = document.getElementById('modalGst');
    var mTotal = document.getElementById('modalTotalAmount');
    var modalIsOpen = false;
    var closeTimer = null;

    function setProductNameError(hasError) {
        mProdNameField.classList.toggle('border-rose-400', hasError);
        mProdNameField.classList.toggle('ring-1', hasError);
        mProdNameField.classList.toggle('ring-rose-200', hasError);
    }

    function openModal() {
        mCatNo.value = '';
        mProdName.value = '';
        mPackSize.value = '';
        mQty.value = '1';
        mRate.value = '0';
        mGst.value = '18';
        setProductNameError(false);
        updateModalTotal();

        clearTimeout(closeTimer);
        modal.classList.remove('hidden');
        modalIsOpen = true;
        document.body.classList.add('overflow-hidden');
        requestAnimationFrame(function () {
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');
            dialog.classList.remove('opacity-0', 'scale-95', 'translate-y-[10px]');
            dialog.classList.add('opacity-100', 'scale-100', 'translate-y-0');
        });
    }

    function closeModal() {
        if (!modalIsOpen && modal.classList.contains('hidden')) {
            return;
        }

        modalIsOpen = false;
        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');
        dialog.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
        dialog.classList.add('opacity-0', 'scale-95', 'translate-y-[10px]');
        document.body.classList.remove('overflow-hidden');
        clearTimeout(closeTimer);
        closeTimer = setTimeout(function () {
            if (!modalIsOpen) {
                modal.classList.add('hidden');
            }
        }, 300);
    }

    function updateModalTotal() {
        var qty = parseFloat(mQty.value) || 0;
        var rate = parseFloat(mRate.value) || 0;
        var gst = parseFloat(mGst.value) || 0;
        var value = qty * rate;
        var total = value + (value * gst / 100);
        mTotal.textContent = '\u20B9 ' + formatNum(total);
    }

    mQty.addEventListener('input', updateModalTotal);
    mRate.addEventListener('input', updateModalTotal);
    mGst.addEventListener('input', updateModalTotal);
    mProdName.addEventListener('input', function () {
        setProductNameError(false);
    });

    document.getElementById('addProductRow').addEventListener('click', openModal);
    closeBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);
    backdrop.addEventListener('click', closeModal);
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && modalIsOpen) closeModal(); });

    addBtn.addEventListener('click', function () {
        var catNo = mCatNo.value.trim();
        var productName = mProdName.value.trim();
        var qty = parseFloat(mQty.value) || 1;
        var rate = parseFloat(mRate.value) || 0;
        var gst = parseFloat(mGst.value) || 0;
        var packSize = mPackSize.value.trim();

        if (!productName) {
            setProductNameError(true);
            mProdName.focus();
            return;
        }
        setProductNameError(false);

        addRowToTable({
            catNo: catNo,
            productName: productName,
            packSize: packSize,
            qty: qty,
            rate: rate,
            gst: gst
        });

        closeModal();
    });

    // â”€â”€â”€ Freight input recalc â”€â”€â”€
    document.getElementById('freightCharges').addEventListener('input', recalcTotals);

    // â”€â”€â”€ Add Term button â”€â”€â”€
    document.getElementById('addTermBtn').addEventListener('click', function () {
        var list = document.getElementById('termsList');
        var count = list.querySelectorAll('li').length + 1;
        var li = document.createElement('li');
        li.className = 'flex items-baseline gap-2 text-sm text-slate-700';
        li.innerHTML = '<span class="shrink-0 font-bold text-primary-600">' + count + '.</span>' +
            '<input type="text" value="" placeholder="Enter term..." class="w-full border-0 bg-transparent p-0 text-sm text-slate-700 outline-none">';
        list.appendChild(li);
    });

    document.getElementById('sendEmailBtn').addEventListener('click', function () {
        if (window.BiogenixToast) {
            window.BiogenixToast.show('Email functionality coming soon!', 'info');
        } else {
            alert('Email functionality coming soon!');
        }
    });

    document.getElementById('sendEmailTopBtn').addEventListener('click', function () {
        document.getElementById('sendEmailBtn').click();
    });

    // â”€â”€â”€ Generate PDF (placeholder) â”€â”€â”€
    document.getElementById('generatePdfBtn').addEventListener('click', function () {
        if (window.BiogenixToast) {
            window.BiogenixToast.show('PDF generation coming soon! All form data is captured.', 'info');
        } else {
            alert('PDF generation coming soon! All form data is captured.');
        }
    });

    document.getElementById('generatePdfTopBtn').addEventListener('click', function () {
        document.getElementById('generatePdfBtn').click();
    });

    // â”€â”€â”€ Show empty message initially â”€â”€â”€
    toggleEmptyMsg();
    updateModalTotal();
    recalcTotals();
    syncShippingToggleState();
});
</script>
@endsection


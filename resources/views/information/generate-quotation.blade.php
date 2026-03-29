@extends('layouts.app')

@section('title', $pageTitle ?? 'Generate Quotation')

@section('content')
@push('styles')
<style>
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-4px); }
        75% { transform: translateX(4px); }
    }
    .animate-slide-in { animation: slideInRight 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    .animate-slide-out { animation: slideOutRight 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    .animate-shake { animation: shake 0.4s cubic-bezier(0.36, 0.07, 0.19, 0.97) both; }
    .toast-glass { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(226, 232, 240, 0.8); }
</style>
@endpush
@php
    $oldProductIds = old('product_id', $prefilledProductId ? [$prefilledProductId] : ['']);
    $oldQuantities = old('quantity', array_fill(0, max(1, count($oldProductIds)), 1));
    $pageShellClass = 'mx-auto w-full max-w-none space-y-8 px-4 py-8 sm:px-6 lg:px-8 xl:px-10 md:py-12';
    $formCardClass = 'space-y-5 rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm md:p-8';
    $sidebarCardClass = 'space-y-4 rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm';
    $inputClass = 'h-11 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:ring-2 focus:ring-primary-500/40';
    $textareaClass = 'w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:ring-2 focus:ring-primary-500/40';
    $buttonPrimaryClass = 'inline-flex h-11 items-center justify-center rounded-xl bg-primary-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700 disabled:cursor-not-allowed disabled:opacity-70';
    $buttonSecondaryClass = 'inline-flex h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-70';
    $rowClass = 'space-y-4 rounded-3xl border border-slate-200 bg-slate-50 p-4';
    $previewClass = 'rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm leading-6 text-slate-600';
    
    $pageTitle = 'Generate Quotation';
    $pageDescription = 'Select products, set quantities, and generate branded quotation PDFs using visible MRP values.';
    $sectionSubtitle = 'Choose product, quantity, and recipient information.';
    $formAction = route('quotation.store');
    $primarySubmitLabel = 'Generate Quotation';
    $showDownloadActions = true;
    $actionHelpText = 'Both actions use MRP-only values and server validation.';

    if (! is_array($oldProductIds) || $oldProductIds === []) {
        $oldProductIds = [''];
    }

    if (! is_array($oldQuantities) || $oldQuantities === []) {
        $oldQuantities = array_fill(0, count($oldProductIds), 1);
    }
@endphp

<div>
    {{-- Premium Toast Container --}}
    <div id="toast-container" class="fixed right-6 top-24 z-[1000] flex flex-col gap-3"></div>

    <section class="relative overflow-hidden bg-primary-800 py-16 text-white md:py-24">
        <img src="{{ asset('upload/corousel/image3.jpg') }}" alt="Biogenix Quotation" class="absolute inset-0 h-full w-full object-cover opacity-10" loading="lazy" decoding="async">
        <div class="absolute inset-0 bg-gradient-to-b from-[#013b2a] via-[#013b2a]/95 to-[#013b2a]/90"></div>
        <div class="relative z-10 mx-auto w-full max-w-none px-4 text-center sm:px-6 lg:px-8 xl:px-10">
            <h1 class="mx-auto max-w-4xl font-display text-4xl font-bold tracking-tight text-white md:text-5xl lg:text-6xl">
                {{ $pageTitle }}
            </h1>
            <p class="mx-auto mt-6 max-w-2xl text-base leading-8 text-slate-200 md:text-lg">
                {{ $pageDescription }}
            </p>
        </div>
    </section>

    <div class="{{ $pageShellClass }}">
        <section class="space-y-6">
            <div class="mx-auto max-w-4xl">
                <div class="mb-6 space-y-1">
                    <h2 class="text-2xl font-bold tracking-tight text-slate-900 md:text-3xl">Product Selection</h2>
                    <p class="text-base text-slate-500">{{ $sectionSubtitle }}</p>
                </div>

                <div class="{{ $formCardClass }}">
                    @if ($errors->any())
                        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="generateQuoteForm" method="POST" action="{{ $formAction }}" class="space-y-3">
                        @csrf

                        <div class="space-y-3">
                            <label class="text-sm font-semibold text-slate-700">Products</label>
                            <div id="quote-item-list" class="space-y-3">
                                @foreach ($oldProductIds as $index => $oldProductId)
                                    <div class="{{ $rowClass }}" data-quote-row>
                                        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                                            <div class="space-y-2" data-quote-product-container>
                                                <label class="text-sm font-semibold text-slate-700">Product</label>
                                                <div class="relative" data-custom-select-wrapper>
                                                    <input type="hidden" name="product_id[]" data-quote-product-select value="{{ $oldProductId }}" required>
                                                    <button type="button" class="{{ $inputClass }} flex items-center justify-between gap-2 text-left" data-custom-select-trigger>
                                                        <span class="truncate" data-custom-select-label>Select product</span>
                                                        <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                    </button>
                                                    
                                                    <div class="absolute inset-x-0 top-full z-50 mt-2 hidden max-h-72 overflow-y-auto rounded-2xl border border-slate-200 bg-white p-2 shadow-2xl transition-all" data-custom-select-dropdown>
                                                        <div class="sticky top-0 mb-2 bg-white pb-1">
                                                            <input type="text" class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-xs outline-none focus:ring-2 focus:ring-primary-500/30" placeholder="Search products..." data-custom-select-search>
                                                        </div>
                                                        <div class="space-y-1" data-custom-select-options>
                                                            @foreach ($products as $product)
                                                                <button type="button" 
                                                                    class="flex w-full items-start gap-3 rounded-xl p-3 text-left transition hover:bg-primary-800 hover:text-white group"
                                                                    data-option-id="{{ $product->id }}"
                                                                    data-option-name="{{ $product->name }}"
                                                                    data-option-sku="{{ $product->sku }}"
                                                                    data-option-currency="{{ $product->visible_currency ?? 'INR' }}"
                                                                    data-option-mrp="{{ (float) ($product->visible_price ?? 0) }}"
                                                                    data-option-min-qty="{{ $product->min_order_quantity ?? 1 }}"
                                                                    data-option-max-qty="{{ $product->max_order_quantity ?? '' }}"
                                                                    data-option-lot-size="{{ $product->lot_size ?? 1 }}"
                                                                >
                                                                    <div class="flex-1 overflow-hidden">
                                                                        <p class="truncate text-sm font-semibold text-slate-900 group-hover:text-white">{{ $product->name }}</p>
                                                                        <p class="truncate text-xs text-slate-500 group-hover:text-slate-100 italic">SKU: {{ $product->sku }} | MRP: {{ $product->visible_currency ?? 'INR' }} {{ number_format((float) ($product->visible_price ?? 0), 2) }}</p>
                                                                    </div>
                                                                </button>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                @error('product_id.' . $index)
                                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div class="space-y-2">
                                                <label for="quantity_{{ $index }}" class="text-sm font-semibold text-slate-700">Quantity</label>
                                                <input id="quantity_{{ $index }}" name="quantity[]" data-quote-quantity-input class="{{ $inputClass }} @error('quantity.' . $index) border-red-500 ring-1 ring-red-100 @enderror" type="number" min="1" step="1" value="{{ $oldQuantities[$index] ?? 1 }}" placeholder="1" required>
                                                @error('quantity.' . $index)
                                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="{{ $previewClass }}" data-quote-preview>Select a product to preview MRP and quantity rules.</div>
                                        <p class="text-sm text-slate-500" data-quote-rule>Select a product to view quantity constraints.</p>
                                        <p class="text-sm text-slate-500" data-quote-line-total>Estimated MRP line total will appear here.</p>

                                        <button type="button" class="{{ $buttonSecondaryClass }} remove-quote-item hidden">Remove Item</button>
                                    </div>
                                @endforeach
                            </div>

                            <button type="button" id="add-quote-item" class="{{ $buttonPrimaryClass }} mt-2 gap-2">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                                </svg>
                                Add Product
                            </button>
                        </div>

                        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                            <input type="hidden" id="purpose" name="purpose" value="other">

                            <div class="space-y-2">
                                <label for="customer_name" class="text-sm font-semibold text-slate-700">Recipient Name</label>
                                <input id="customer_name" name="customer_name" class="{{ $inputClass }} @error('customer_name') border-red-500 ring-1 ring-red-100 @enderror" value="{{ old('customer_name', auth()->user()->name ?? '') }}" placeholder="Enter recipient's full name" required>
                                @error('customer_name')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="customer_email" class="text-sm font-semibold text-slate-700">Recipient Email</label>
                                <input id="customer_email" name="customer_email" type="email" class="{{ $inputClass }} @error('customer_email') border-red-500 ring-1 ring-red-100 @enderror" value="{{ old('customer_email', auth()->user()->email ?? '') }}" placeholder="name@example.com" required>
                                @error('customer_email')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="customer_phone" class="text-sm font-semibold text-slate-700">Recipient Phone</label>
                                <input id="customer_phone" name="customer_phone" class="{{ $inputClass }} @error('customer_phone') border-red-500 ring-1 ring-red-100 @enderror" value="{{ old('customer_phone') }}" placeholder="">
                                @error('customer_phone')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            @auth
                                @if (auth()->user()->isB2b())
                                    <div class="space-y-2 md:col-span-2">
                                        <label for="target_company_id" class="text-sm font-semibold text-slate-700">Target Company (optional)</label>
                                        <select id="target_company_id" name="target_company_id" class="{{ $inputClass }} @error('target_company_id') border-red-500 ring-1 ring-red-100 @enderror">
                                            <option value="">Not selected</option>
                                            @foreach ($clientCompanies as $company)
                                                <option value="{{ $company->id }}" @selected((int) old('target_company_id') === (int) $company->id)>
                                                    {{ $company->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('target_company_id')
                                            <p class="text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif
                            @endauth
                        </div>

                        <div class="space-y-2">
                            <label for="notes" class="text-sm font-semibold text-slate-700">Notes</label>
                            <textarea id="notes" name="notes" class="{{ $textareaClass }} @error('notes') border-red-500 ring-1 ring-red-100 @enderror" rows="3" placeholder="Include any special terms, delivery preferences, or additional context..."></textarea>
                            @error('notes')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex flex-wrap items-center gap-2 pt-1">
                            <button type="submit" id="generateQuoteSubmitBtn" class="{{ $buttonPrimaryClass }} w-full sm:w-auto">{{ $primarySubmitLabel }}</button>
                            @if ($showDownloadActions)
                                <button type="submit" id="downloadQuoteSubmitBtn" name="download_pdf" value="1" class="inline-flex h-11 w-full items-center justify-center rounded-xl bg-neutral-800 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-neutral-700 sm:w-auto">Download PDF</button>
                            @endif
                            <!-- <p class="text-xs text-slate-500">{{ $actionHelpText }}</p> -->
                            <p id="quoteDraftStatus" class="w-full text-xs text-slate-500"></p>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const quoteForm = document.getElementById('generateQuoteForm');
        const quoteSubmitBtn = document.getElementById('generateQuoteSubmitBtn');
        const downloadQuoteSubmitBtn = document.getElementById('downloadQuoteSubmitBtn');
        const itemList = document.getElementById('quote-item-list');
        const addItemButton = document.getElementById('add-quote-item');
        const toastContainer = document.getElementById('toast-container');
        let activeSubmitBtn = null;

        /**
         * Show a premium toast notification
         */
        function showToast(title, message, type = 'error') {
            const toast = document.createElement('div');
            const isError = type === 'error';
            const icon = isError ? 
                `<svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>` :
                `<svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>`;
            
            toast.className = `toast-glass animate-slide-in flex max-w-sm items-start gap-4 rounded-3xl p-4 shadow-2xl transition-all duration-300`;
            toast.innerHTML = `
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl ${isError ? 'bg-red-50' : 'bg-green-50'}">
                    ${icon}
                </div>
                <div class="flex-1 space-y-1">
                    <p class="text-sm font-bold text-slate-900">${title}</p>
                    <p class="text-xs text-slate-500 leading-relaxed">${message}</p>
                </div>
                <button class="text-slate-400 hover:text-slate-600 transition" onclick="this.parentElement.remove()">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            `;
            
            toastContainer.appendChild(toast);
            
            // Auto remove after 5s
            setTimeout(() => {
                toast.classList.replace('animate-slide-in', 'animate-slide-out');
                setTimeout(() => toast.remove(), 400);
            }, 5000);
        }

        [quoteSubmitBtn, downloadQuoteSubmitBtn].forEach(function (btn) {
            if (!btn) return;
            btn.addEventListener('click', function () {
                activeSubmitBtn = btn;
            });
        });

        if (quoteForm && quoteSubmitBtn) {
            quoteForm.addEventListener('submit', function (e) {
                const nameInput = document.getElementById('customer_name');
                const emailInput = document.getElementById('customer_email');
                const nameValue = nameInput.value.trim();
                const emailValue = emailInput.value.trim();
                let hasError = false;

                // Reset error styles
                [nameInput, emailInput].forEach(el => {
                    el.classList.remove('border-red-500', 'ring-1', 'ring-red-100', 'animate-shake');
                });

                if (!nameValue) {
                    nameInput.classList.add('border-red-500', 'ring-1', 'ring-red-100', 'animate-shake');
                    showToast('Required Field', 'Please enter the recipient name for the quotation.', 'error');
                    hasError = true;
                } else if (!emailValue) {
                    emailInput.classList.add('border-red-500', 'ring-1', 'ring-red-100', 'animate-shake');
                    showToast('Required Field', 'Please enter the recipient email for delivery.', 'error');
                    hasError = true;
                }

                if (hasError) {
                    e.preventDefault();
                    return;
                }

                const submitTarget = activeSubmitBtn || quoteSubmitBtn;
                submitTarget.disabled = true;
                submitTarget.classList.add('opacity-70', 'cursor-not-allowed');
                submitTarget.setAttribute('aria-disabled', 'true');
            });
        }

        function formatMoney(currency, amount) {
            return currency + ' ' + Number(amount || 0).toFixed(2);
        }

        function updateRemoveButtons() {
            const rows = itemList.querySelectorAll('[data-quote-row]');
            rows.forEach(function (row, index) {
                const removeButton = row.querySelector('.remove-quote-item');
                if (!removeButton) return;
                
                // User requirement: First row (index 0) MUST NOT show remove button.
                // Also hide if there is only one row total.
                if (index === 0 || rows.length <= 1) {
                    removeButton.classList.add('hidden');
                    removeButton.style.setProperty('display', 'none', 'important');
                } else {
                    removeButton.classList.remove('hidden');
                    removeButton.style.display = '';
                }
            });
        }

        function reindexRows() {
            itemList.querySelectorAll('[data-quote-row]').forEach(function (row, index) {
                const select = row.querySelector('[data-quote-product-select]');
                const input = row.querySelector('[data-quote-quantity-input]');
                if (select) select.id = 'product_id_' + index;
                if (input) input.id = 'quantity_' + index;
            });
        }

        function updateRow(row) {
            const hiddenInput = row.querySelector('[data-quote-product-select]');
            const quantityInput = row.querySelector('[data-quote-quantity-input]');
            const preview = row.querySelector('[data-quote-preview]');
            const rule = row.querySelector('[data-quote-rule]');
            const lineTotal = row.querySelector('[data-quote-line-total]');
            
            // Get data from the hidden input (which we populate on selection)
            const productId = hiddenInput.value;

            if (!productId) {
                quantityInput.min = 1;
                quantityInput.step = 1;
                quantityInput.removeAttribute('max');
                preview.textContent = 'Select a product to preview MRP and quantity rules.';
                rule.textContent = 'Select a product to view quantity constraints.';
                lineTotal.textContent = 'Estimated MRP line total will appear here.';
                return;
            }

            const minQty = parseInt(hiddenInput.dataset.minQty || '1', 10);
            const lotSize = parseInt(hiddenInput.dataset.lotSize || '1', 10);
            const maxQty = hiddenInput.dataset.maxQty || '';
            const currency = hiddenInput.dataset.currency || 'INR';
            const mrp = parseFloat(hiddenInput.dataset.mrp || '0');
            const name = hiddenInput.dataset.name || '';
            const sku = hiddenInput.dataset.sku || '';

            quantityInput.min = minQty;
            quantityInput.step = lotSize;

            if (maxQty !== '') {
                quantityInput.max = maxQty;
            } else {
                quantityInput.removeAttribute('max');
            }

            if (!quantityInput.value || parseInt(quantityInput.value, 10) < minQty) {
                quantityInput.value = minQty;
            }

            const qty = parseInt(quantityInput.value || minQty, 10);
            const total = mrp * qty;

            preview.innerHTML =
                '<strong>' + name + '</strong><br>' +
                'SKU: ' + sku + '<br>' +
                'MRP / Unit: ' + formatMoney(currency, mrp);

            let ruleMessage = 'Min Qty: ' + minQty + ' | Lot Size: ' + lotSize;
            if (maxQty !== '') {
                ruleMessage += ' | Max Qty: ' + maxQty;
            }

            rule.textContent = ruleMessage;
            lineTotal.textContent = 'Estimated line MRP total: ' + formatMoney(currency, total);
        }

        function attachRowEvents(row) {
            const wrapper = row.querySelector('[data-custom-select-wrapper]');
            const trigger = row.querySelector('[data-custom-select-trigger]');
            const dropdown = row.querySelector('[data-custom-select-dropdown]');
            const searchInput = row.querySelector('[data-custom-select-search]');
            const options = row.querySelectorAll('[data-option-id]');
            const hiddenInput = row.querySelector('[data-quote-product-select]');
            const label = row.querySelector('[data-custom-select-label]');
            const quantityInput = row.querySelector('[data-quote-quantity-input]');
            const removeBtn = row.querySelector('.remove-quote-item');

            // Toggle dropdown
            if (trigger) {
                trigger.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const isOpen = !dropdown.classList.contains('hidden');
                    
                    // Close all other dropdowns
                    document.querySelectorAll('[data-custom-select-dropdown]').forEach(d => d.classList.add('hidden'));
                    
                    if (!isOpen) {
                        dropdown.classList.remove('hidden');
                        if (searchInput) {
                            setTimeout(() => searchInput.focus(), 50);
                        }
                    }
                });
            }

            // Search logic
            if (searchInput) {
                searchInput.addEventListener('click', e => e.stopPropagation());
                searchInput.addEventListener('input', function () {
                    const term = this.value.toLowerCase();
                    options.forEach(opt => {
                        const name = opt.dataset.optionName.toLowerCase();
                        const sku = opt.dataset.optionSku.toLowerCase();
                        opt.classList.toggle('hidden', !name.includes(term) && !sku.includes(term));
                    });
                });
            }

            // Option selection
            options.forEach(opt => {
                opt.addEventListener('click', function (e) {
                    e.stopPropagation();
                    
                    // Update hidden input and its datasets
                    hiddenInput.value = opt.dataset.optionId;
                    hiddenInput.dataset.name = opt.dataset.optionName;
                    hiddenInput.dataset.sku = opt.dataset.optionSku;
                    hiddenInput.dataset.currency = opt.dataset.optionCurrency;
                    hiddenInput.dataset.mrp = opt.dataset.optionMrp;
                    hiddenInput.dataset.minQty = opt.dataset.optionMinQty;
                    hiddenInput.dataset.maxQty = opt.dataset.optionMaxQty;
                    hiddenInput.dataset.lotSize = opt.dataset.optionLotSize;

                    // Update UI
                    label.textContent = opt.dataset.optionName + ' (' + opt.dataset.optionSku + ')';
                    dropdown.classList.add('hidden');
                    
                    updateRow(row);
                });
            });

            if (quantityInput) {
                quantityInput.addEventListener('input', function () {
                    updateRow(row);
                });
            }

            if (removeBtn) {
                removeBtn.addEventListener('click', function () {
                    row.remove();
                    reindexRows();
                    updateRemoveButtons();
                });
            }

            // Initialize state (if it has a value from old input)
            if (hiddenInput.value) {
                const selectedOpt = Array.from(options).find(opt => opt.dataset.optionId == hiddenInput.value);
                if (selectedOpt) {
                    label.textContent = selectedOpt.dataset.optionName + ' (' + selectedOpt.dataset.optionSku + ')';
                    hiddenInput.dataset.name = selectedOpt.dataset.optionName;
                    hiddenInput.dataset.sku = selectedOpt.dataset.optionSku;
                    hiddenInput.dataset.currency = selectedOpt.dataset.optionCurrency;
                    hiddenInput.dataset.mrp = selectedOpt.dataset.optionMrp;
                    hiddenInput.dataset.minQty = selectedOpt.dataset.optionMinQty;
                    hiddenInput.dataset.maxQty = selectedOpt.dataset.optionMaxQty;
                    hiddenInput.dataset.lotSize = selectedOpt.dataset.optionLotSize;
                }
            }

            updateRow(row);
        }

        // Global click to close dropdowns
        document.addEventListener('click', function () {
            document.querySelectorAll('[data-custom-select-dropdown]').forEach(d => d.classList.add('hidden'));
        });

        // Initialize existing rows
        itemList.querySelectorAll('[data-quote-row]').forEach(function (row) {
            attachRowEvents(row);
        });

        // Add Product logic
        addItemButton.addEventListener('click', function () {
            const rows = itemList.querySelectorAll('[data-quote-row]');
            if (rows.length === 0) return;

            const newRow = rows[0].cloneNode(true);

            // Reset values
            const hiddenInput = newRow.querySelector('[data-quote-product-select]');
            const label = newRow.querySelector('[data-custom-select-label]');
            const dropdown = newRow.querySelector('[data-custom-select-dropdown]');
            const searchInput = newRow.querySelector('[data-custom-select-search]');
            const input = newRow.querySelector('[data-quote-quantity-input]');
            const preview = newRow.querySelector('[data-quote-preview]');
            const rule = newRow.querySelector('[data-quote-rule]');
            const total = newRow.querySelector('[data-quote-line-total]');

            if (hiddenInput) {
                hiddenInput.value = '';
                delete hiddenInput.dataset.name;
                delete hiddenInput.dataset.sku;
            }
            if (label) label.textContent = 'Select product';
            if (dropdown) dropdown.classList.add('hidden');
            if (searchInput) searchInput.value = '';
            if (input) input.value = 1;

            if (preview) preview.textContent = 'Select a product to preview MRP and quantity rules.';
            if (rule) rule.textContent = 'Select a product to view quantity constraints.';
            if (total) total.textContent = 'Estimated MRP line total will appear here.';

            itemList.appendChild(newRow);
            attachRowEvents(newRow);
            reindexRows();
            updateRemoveButtons();
        });

        reindexRows();
        updateRemoveButtons();
    });
</script>
@endpush
@endsection

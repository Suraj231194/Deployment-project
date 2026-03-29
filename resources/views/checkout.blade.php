@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
    @php
        $pageWrapClass = 'mx-auto w-full max-w-none px-3 py-4 sm:px-6 sm:py-6 lg:px-8 xl:px-10';
        $backLinkClass = 'inline-flex h-11 w-full items-center justify-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 sm:w-auto';
        $layoutGridClass = 'mt-6 grid gap-5 xl:mt-8 xl:grid-cols-[minmax(0,1fr)_24rem] xl:gap-6';
        $mainColumnClass = 'space-y-6';
        $sectionCardClass = 'rounded-[24px] border border-slate-200 bg-white p-4 shadow-sm sm:p-6 md:rounded-[28px] md:p-8';
        $summaryCardClass = 'rounded-[24px] border border-slate-200 bg-white p-4 shadow-sm sm:p-6 md:rounded-[28px] md:p-8 xl:sticky xl:top-6';
        $helpCardClass = 'rounded-[24px] border border-slate-200 bg-white p-4 shadow-sm sm:p-5 md:rounded-[28px]';
        $stepClass = 'inline-flex h-10 w-10 items-center justify-center rounded-full bg-primary-600 text-sm font-semibold text-white shadow-sm';
        $sectionTitleClass = 'text-xl font-semibold text-slate-950';
        $sectionCopyClass = 'mt-1 text-sm leading-6 text-slate-500';
        $selectionCardBaseClass = 'relative flex min-h-[180px] flex-col gap-4 rounded-[24px] border p-4 text-left shadow-sm transition duration-200 sm:min-h-[220px] sm:rounded-[28px] sm:p-5';
        $selectionCardActiveClass = 'border-primary-200 bg-primary-50';
        $selectionCardInactiveClass = 'border-slate-200 bg-white hover:-translate-y-0.5 hover:border-primary-200 hover:shadow-md';
        $paymentCardBaseClass = 'flex flex-col gap-3 rounded-[24px] border p-4 shadow-sm transition duration-200 sm:flex-row sm:items-start sm:gap-4 sm:rounded-[28px] sm:p-5';
        $paymentCardActiveClass = 'border-primary-200 bg-primary-50';
        $paymentCardInactiveClass = 'border-slate-200 bg-white hover:-translate-y-0.5 hover:border-primary-200 hover:shadow-md';
        $iconTilePrimaryClass = 'inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-primary-50 text-primary-700';
        $iconTileNeutralClass = 'inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-slate-600';
        $iconTileSuccessClass = 'inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-primary-50 text-primary-600';
        $buttonPrimaryClass = 'inline-flex h-14 items-center justify-center rounded-2xl bg-primary-600 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700 disabled:cursor-not-allowed disabled:opacity-70 cursor-pointer';
        $buttonSecondaryClass = 'inline-flex h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 cursor-pointer';
        $inputClass = 'w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 placeholder-slate-400 outline-none transition focus:border-primary-400 focus:bg-white focus:ring-2 focus:ring-primary-600/15';
        $labelClass = 'mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500';
        $savedAddressCollection = collect($savedAddresses ?? []);
        $selectedAddress = $savedAddressCollection->firstWhere('is_default_shipping', true) ?? $savedAddressCollection->first();
        $checkoutBusinessDetails = $checkoutBusinessDetails ?? [];
        $isReOrderCheckout = (bool) ($isReOrderCheckout ?? false);
        $reOrderCheckout = is_array($reOrderCheckout ?? null) ? $reOrderCheckout : [];
        $showBusinessInvoiceDetails = (bool) ($checkoutBusinessDetails['show_business_fields'] ?? false);
        $selectedAddressSource = old('selected_address_source', $selectedAddress ? 'existing' : (auth()->check() ? 'new' : 'existing'));
        $selectedAddressId = old('selected_user_address_id', $selectedAddress?->id);
        $newAddressFieldNames = ['new_address_label', 'new_address_line1', 'new_address_city', 'new_address_state', 'new_address_postal_code', 'new_address_phone'];
        $newAddressFormVisible = auth()->check() && (
            $selectedAddressSource === 'new'
            || $savedAddressCollection->isEmpty()
            || collect($newAddressFieldNames)->contains(fn ($fieldName) => $errors->has($fieldName))
        );
    @endphp

    <div class="{{ $pageWrapClass }}">
        <div>
            <div class="mb-4 flex justify-start">
                <a href="{{ route('products.index') }}" class="{{ $backLinkClass }}">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m15 18-6-6 6-6"></path>
                    </svg>
                    <span>Back to Products</span>
                </a>
            </div>

            {{-- ─── Hero ─── --}}
            {{-- ════════════════════════════════════════════════════════ --}}
            {{-- STEP PROGRESS BAR --}}
            {{-- ════════════════════════════════════════════════════════ --}}
            <div class="{{ $layoutGridClass }}">
                <div class="{{ $mainColumnClass }}">

                    {{-- ════════════════════════════════════════════════════════ --}}
                    {{-- STEP 1: SHIPPING ADDRESS --}}
                    {{-- ════════════════════════════════════════════════════════ --}}
                    <section class="{{ $sectionCardClass }}">
                        <div class="flex items-center gap-3">
                            <span class="{{ $stepClass }}">1</span>
                            <div>
                                <h2 class="{{ $sectionTitleClass }}">Shipping Address</h2>
                                <p class="{{ $sectionCopyClass }}">Choose the destination for this order.</p>
                            </div>
                        </div>

                        <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                            @auth
                                @forelse ($savedAddressCollection as $savedAddress)
                                    @php
                                        $isSelectedAddress = $selectedAddressSource === 'existing' && (int) $savedAddress->id === (int) $selectedAddressId;
                                        $addressTitle = trim((string) ($savedAddress->city . ', ' . $savedAddress->state));
                                        $addressLines = collect([
                                            $savedAddress->line1,
                                            $savedAddress->line2,
                                            trim($savedAddress->city . ', ' . $savedAddress->state . ' ' . $savedAddress->postal_code),
                                            $savedAddress->country,
                                        ])->filter();
                                    @endphp
                                    <button
                                        type="button"
                                        class="{{ $selectionCardBaseClass }} {{ $isSelectedAddress ? $selectionCardActiveClass : $selectionCardInactiveClass }}"
                                        data-address-card
                                        data-address-id="{{ $savedAddress->id }}"
                                        data-address-source="existing"
                                        data-selected="{{ $isSelectedAddress ? 'true' : 'false' }}"
                                    >
                                        <span class="{{ $isSelectedAddress ? $iconTilePrimaryClass : $iconTileNeutralClass }}">
                                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                                <path d="M3 10.5 12 3l9 7.5"></path><path d="M5 9.5V21h14V9.5"></path><path d="M9 21v-6h6v6"></path>
                                            </svg>
                                        </span>
                                        <span class="text-base font-semibold text-slate-950">{{ $addressTitle !== '' ? $addressTitle : 'Saved Address' }}</span>
                                        <span class="text-sm leading-7 text-slate-500">
                                            @foreach ($addressLines as $addressLine)
                                                {{ $addressLine }}@if (! $loop->last)<br>@endif
                                            @endforeach
                                        </span>
                                        <span class="mt-auto pt-3 text-sm {{ $isSelectedAddress ? 'font-semibold text-primary-700' : 'font-medium text-slate-400' }}">
                                            {{ $isSelectedAddress ? 'Selected' : 'Available address' }}
                                        </span>
                                        @if ($isSelectedAddress)
                                            <span class="absolute right-4 top-4 inline-flex h-8 w-8 items-center justify-center rounded-full bg-white text-primary-700 shadow-sm">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 13l4 4L19 7"></path></svg>
                                            </span>
                                        @endif
                                    </button>
                                @empty
                                    <div class="sm:col-span-2 xl:col-span-3 rounded-[24px] border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-slate-500">
                                        <p class="text-base font-semibold text-slate-900">No saved addresses found</p>
                                        <p class="mt-2 text-sm leading-6">This account does not have any addresses in the saved address book yet.</p>
                                    </div>
                                @endforelse
                            @else
                                <div class="sm:col-span-2 xl:col-span-3 rounded-[24px] border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-slate-500">
                                    <p class="text-base font-semibold text-slate-900">Saved addresses are available after login</p>
                                    <p class="mt-2 text-sm leading-6">Guest checkout is using the current cart only, so no account address book is loaded here.</p>
                                </div>
                            @endauth

                            @auth
                                <button
                                    type="button"
                                    id="addAddressToggleBtn"
                                    class="{{ $selectionCardBaseClass }} {{ $selectedAddressSource === 'new' ? $selectionCardActiveClass : $selectionCardInactiveClass }} items-center justify-center border-dashed text-center text-slate-500"
                                    data-address-source="new"
                                >
                                    <span class="{{ $selectedAddressSource === 'new' ? $iconTilePrimaryClass : $iconTileNeutralClass }}">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                            <path d="M12 5v14"></path><path d="M5 12h14"></path>
                                        </svg>
                                    </span>
                                    <span class="text-base font-semibold">Add New Address</span>
                                    <span class="text-sm leading-6">Create another dispatch point for a different lab or facility.</span>
                                </button>
                            @endauth
                        </div>

                        @error('selected_user_address_id')
                            <p class="mt-3 text-sm font-medium text-rose-600">{{ $message }}</p>
                        @enderror
                        @error('selected_address_source')
                            <p class="mt-3 text-sm font-medium text-rose-600">{{ $message }}</p>
                        @enderror

                        @auth
                            {{-- Step 2: keep the new address form available to every logged-in customer so checkout can save a fresh destination when needed. --}}
                            <div id="addAddressForm" class="mt-5 {{ $newAddressFormVisible ? '' : 'hidden' }}">
                                <div class="rounded-[22px] border border-primary-200 bg-primary-50/40 p-5 md:p-6">
                                    <div class="mb-5 flex items-center justify-between">
                                        <h3 class="text-base font-semibold text-slate-900">New Address Details</h3>
                                        <button type="button" id="addAddressCloseBtn" aria-label="Close form" class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-900">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div class="sm:col-span-2">
                                            <label class="{{ $labelClass }}" for="newAddressLabelInput">Address Label / Facility Name</label>
                                            <input id="newAddressLabelInput" type="text" class="{{ $inputClass }}" placeholder="e.g. North Wing Lab, Warehouse B" value="{{ old('new_address_label') }}">
                                            @error('new_address_label')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="{{ $labelClass }}" for="newAddressLine1Input">Street Address</label>
                                            <input id="newAddressLine1Input" type="text" class="{{ $inputClass }}" placeholder="Building no., street, locality" value="{{ old('new_address_line1') }}">
                                            @error('new_address_line1')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                                        </div>
                                        <div>
                                            <label class="{{ $labelClass }}" for="newAddressCityInput">City</label>
                                            <input id="newAddressCityInput" type="text" class="{{ $inputClass }}" placeholder="City" value="{{ old('new_address_city') }}">
                                            @error('new_address_city')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                                        </div>
                                        <div>
                                            <label class="{{ $labelClass }}" for="newAddressPostalCodeInput">Pincode</label>
                                            <input id="newAddressPostalCodeInput" type="text" class="{{ $inputClass }}" placeholder="6-digit pincode" maxlength="6" inputmode="numeric" value="{{ old('new_address_postal_code') }}">
                                            @error('new_address_postal_code')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                                        </div>
                                        <div>
                                            <label class="{{ $labelClass }}" for="newAddressStateInput">State</label>
                                            <input id="newAddressStateInput" type="text" class="{{ $inputClass }}" placeholder="State" value="{{ old('new_address_state') }}">
                                            @error('new_address_state')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                                        </div>
                                        <div>
                                            <label class="{{ $labelClass }}" for="newAddressPhoneInput">Contact Phone</label>
                                            <input id="newAddressPhoneInput" type="tel" class="{{ $inputClass }}" placeholder="+91 XXXXX XXXXX" value="{{ old('new_address_phone', auth()->user()->phone) }}">
                                            @error('new_address_phone')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                                        </div>
                                    </div>
                                    <div class="mt-5 flex flex-wrap gap-3">
                                        <button type="button" id="saveAddressBtn" class="{{ $buttonPrimaryClass }} h-11 text-sm">
                                            Save & Use This Address
                                        </button>
                                        <button type="button" id="addAddressCloseBtn2" class="{{ $buttonSecondaryClass }}">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endauth
                        @if ($showBusinessInvoiceDetails)
                        {{-- GST / PAN Field --}}
                        <div class="mt-5 rounded-[20px] border border-slate-200 bg-slate-50 p-4 md:p-5">
                            <div class="flex items-center gap-2 mb-3">
                                <svg class="h-4 w-4 shrink-0 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z"/>
                                </svg>
                                <p class="text-sm font-semibold text-slate-900">GST / Business Invoice Details <span class="ml-1 text-xs font-normal text-slate-400">(optional — for B2B buyers)</span></p>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="{{ $labelClass }}" for="gstinInput">GSTIN Number</label>
                                    <input
                                        type="text"
                                        id="gstinInput"
                                        class="{{ $inputClass }} uppercase"
                                        placeholder="22AAAAA0000A1Z5"
                                        maxlength="15"
                                        value="{{ old('gstin', $checkoutBusinessDetails['gstin'] ?? '') }}"
                                    >
                                    @error('gstin')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                                    <p class="mt-1 text-xs text-slate-500">15-character GST Identification Number</p>
                                </div>
                                <div>
                                    <label class="{{ $labelClass }}" for="panInput">PAN Number</label>
                                    <input
                                        type="text"
                                        id="panInput"
                                        class="{{ $inputClass }} uppercase"
                                        placeholder="AAAPL1234C"
                                        maxlength="10"
                                        value="{{ old('pan_number', $checkoutBusinessDetails['pan_number'] ?? '') }}"
                                    >
                                    @error('pan_number')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                                    <p class="mt-1 text-xs text-slate-500">Required for high-value orders (&gt; ₹2L)</p>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="{{ $labelClass }}" for="registeredBusinessNameInput">Registered Business Name (for invoice)</label>
                                    <input id="registeredBusinessNameInput" type="text" class="{{ $inputClass }}" placeholder="As per GST registration" value="{{ old('registered_business_name', $checkoutBusinessDetails['registered_business_name'] ?? '') }}">
                                    @error('registered_business_name')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </div>
                        @endif
                    </section>

                    {{-- ════════════════════════════════════════════════════════ --}}
                    {{-- STEP 2: DELIVERY METHOD --}}
                    {{-- ════════════════════════════════════════════════════════ --}}
                    <section class="{{ $sectionCardClass }}">
                        <div class="flex items-center gap-3">
                            <span class="{{ $stepClass }}">2</span>
                            <div>
                                <h2 class="{{ $sectionTitleClass }}">Delivery Method</h2>
                                <p class="{{ $sectionCopyClass }}">Fast tracked medical and biotech shipment options.</p>
                            </div>
                        </div>

                        <div class="checkout-delivery-card mt-5 rounded-3xl border border-primary-100 bg-primary-50 p-5">
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                <div class="flex items-start gap-4">
                                    <span class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-white text-primary-700 shadow-sm">
                                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M13 3 4 14h7l-1 7 10-12h-7l1-6Z"></path>
                                        </svg>
                                    </span>
                                    <div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <p class="text-lg font-semibold text-primary-700">Lucknow Same-Day Delivery</p>
                                            <x-ui.status-badge type="cart" value="fastest" label="Fastest" />
                                        </div>

                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-semibold text-slate-950">FREE</p>
                                    <p class="mt-1 text-sm text-slate-500">Priority tier included</p>
                                </div>
                            </div>
                        </div>

                        {{-- Order Notes / Special Instructions --}}
                        <div class="mt-5">
                            <label class="{{ $labelClass }}" for="orderNotes">
                                <span class="flex items-center gap-1.5">
                                    <svg class="h-3.5 w-3.5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h6m-6 4h4M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"/>
                                    </svg>
                                    Order Notes / Special Instructions
                                    <span class="font-normal text-slate-400">(optional)</span>
                                </span>
                            </label>
                            <textarea
                                id="orderNotes"
                                rows="3"
                                class="{{ $inputClass }} resize-none"
                                placeholder="e.g. Deliver to loading bay B, fragile items, leave with reception, specific handling instructions..."
                            >{{ old('notes') }}</textarea>
                            @error('notes')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            <p class="mt-1.5 text-xs text-slate-400">Your delivery team will see this note on the dispatch sheet.</p>
                        </div>
                    </section>

                    {{-- ════════════════════════════════════════════════════════ --}}
                    {{-- STEP 3: PAYMENT METHOD --}}
                    {{-- ════════════════════════════════════════════════════════ --}}
                    <!-- <section class="{{ $sectionCardClass }}">
                        <div class="flex items-center gap-3">
                            <span class="{{ $stepClass }}">3</span>
                            <div>
                                <h2 class="{{ $sectionTitleClass }}">Payment Method</h2>
                                <p class="{{ $sectionCopyClass }}">Select the payment option best aligned to your procurement workflow.</p>
                            </div>
                        </div>

                        <div class="mt-5 space-y-4">
                            <label class="{{ $paymentCardBaseClass }} {{ $paymentCardActiveClass }}" data-payment-card>
                                <input type="radio" name="payment_method" value="card" class="h-4 w-4 text-primary-600" checked>
                                <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-white text-primary-700">
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                        <rect x="3" y="6" width="18" height="12" rx="2"></rect><path d="M3 10h18"></path>
                                    </svg>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-base font-semibold text-slate-950">Credit / Debit Card</p>
                                    <p class="mt-1 text-sm text-slate-500">Secure via Stripe</p>
                                </div>
                            </label>

                            <label class="{{ $paymentCardBaseClass }} {{ $paymentCardInactiveClass }}" data-payment-card>
                                <input type="radio" name="payment_method" value="banking" class="h-4 w-4 text-primary-600">
                                <span class="{{ $iconTileNeutralClass }}">
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                        <path d="M3 10h18"></path><path d="M6 10v8"></path><path d="M10 10v8"></path><path d="M14 10v8"></path><path d="M18 10v8"></path><path d="M2 21h20"></path><path d="M12 3 2 8h20L12 3Z"></path>
                                    </svg>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-base font-semibold text-slate-950">Net Banking</p>
                                    <p class="mt-1 text-sm text-slate-500">Corporate banking for institutional purchases.</p>
                                </div>
                            </label>

                            <label class="{{ $paymentCardBaseClass }} {{ $paymentCardInactiveClass }}" data-payment-card>
                                <input type="radio" name="payment_method" value="po" class="h-4 w-4 text-primary-600">
                                <span class="{{ $iconTileNeutralClass }}">
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><path d="M14 2v6h6"></path><path d="M8 13h8"></path><path d="M8 17h5"></path>
                                    </svg>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-base font-semibold text-slate-950">B2B Purchase Order (PO)</p>
                                    <p class="mt-1 text-sm text-slate-500">Available for verified institutional procurement teams.</p>
                                </div>
                            </label>
                        </div>

                        <div id="poUploadPanel" class="mt-4 hidden rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-6">
                            <div class="text-center">
                                <div class="mx-auto inline-flex h-14 w-14 items-center justify-center rounded-full bg-primary-50 text-primary-700">
                                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                        <path d="M12 3v12"></path><path d="m7 10 5-5 5 5"></path><path d="M4 20h16"></path>
                                    </svg>
                                </div>
                                <p class="mt-4 text-base font-semibold text-slate-950">Upload Signed PO Document</p>
                            </div>
                            <x-ui.file-upload
                                id="po_document"
                                name="po_document"
                                label="Signed purchase order"
                                hint="PDF or JPG up to 10MB. This is a UI-only placeholder and does not change backend submission."
                                class="mt-4"
                                disabled
                            />
                        </div>
                    </section> -->
                </div>

                {{-- ════════════════════════════════════════════════════════ --}}
                {{-- RIGHT COLUMN: ORDER SUMMARY --}}
                {{-- ════════════════════════════════════════════════════════ --}}
                <div class="space-y-5">
                    <section class="{{ $summaryCardClass }}">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h3 class="{{ $sectionTitleClass }}">Order Summary</h3>
                                <p class="{{ $sectionCopyClass }}">Shared pricing and summary treatment across checkout.</p>
                            </div>
                            <div class="{{ $iconTilePrimaryClass }}">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                    <path d="M3 7h18"></path><path d="M7 3v4"></path><path d="M17 3v4"></path><path d="M6 12h4"></path><path d="M6 16h6"></path><rect x="3" y="5" width="18" height="16" rx="2"></rect>
                                </svg>
                            </div>
                        </div>

                        <x-ui.empty-state
                            id="checkoutEmptyState"
                            compact
                            icon="order"
                            title="No items available"
                            description="Return to the cart page to add products before placing the order."
                        />

                        <div id="checkoutSummaryItems" class="mt-4 space-y-2.5"></div>

                        {{-- Pricing rows --}}
                        <div class="mt-4 space-y-3 border-t border-slate-100 pt-4 text-sm text-slate-600">
                            <div class="flex items-center justify-between">
                                <span>Subtotal</span>
                                <span id="checkoutSubtotal" class="font-medium text-slate-900">Rs. 0.00</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Shipping (Same-Day)</span>
                                <span class="font-semibold text-primary-600">FREE</span>
                            </div>
                            <div class="hidden items-center justify-between" id="couponDiscountRow">
                                <span class="text-primary-600">Coupon Discount</span>
                                <span id="couponDiscountAmount" class="font-semibold text-primary-600">– Rs. 0.00</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>GST</span>
                                <span id="checkoutTax" class="font-medium text-slate-900">Rs. 0.00</span>
                            </div>
                            <div class="flex items-center justify-between border-t border-slate-200 pt-3 text-base font-bold text-secondary-600">
                                <span>Total</span>
                                <span id="checkoutTotal">Rs. 0.00</span>
                            </div>
                        </div>

                        {{-- ─── COUPON CODE FIELD ─── --}}
                        <div class="mt-4 rounded-[18px] border border-slate-200 bg-slate-50 p-3">
                            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">Coupon / Promo Code</p>
                            <div class="flex gap-2">
                                <input
                                    id="couponInput"
                                    type="text"
                                    class="{{ $inputClass }} min-w-0 flex-1 bg-white uppercase"
                                    placeholder="Enter code (e.g. BIO10)"
                                    value="{{ old('coupon_code') }}"
                                    autocomplete="off"
                                >
                                <button
                                    id="couponApplyBtn"
                                    type="button"
                                    class="inline-flex h-10 shrink-0 items-center justify-center rounded-xl bg-primary-600 px-4 text-sm font-semibold text-white transition hover:bg-primary-700 cursor-pointer"
                                >
                                    Apply
                                </button>
                            </div>
                            <p id="couponMsg" class="mt-2 min-h-[1.1rem] text-xs font-medium text-slate-500">
                                @error('coupon_code')
                                    {{ $message }}
                                @else
                                    Click Apply to validate the coupon.
                                @enderror
                            </p>
                        </div>

                        @guest
                            {{-- Step 1: guide guest buyers to login before they can submit the checkout order. --}}
                            <div class="mt-4 rounded-2xl border border-amber-200 bg-secondary-50 px-4 py-4 text-sm text-amber-900">
                                <p class="font-semibold">Login required</p>
                                <p class="mt-1 leading-6 text-amber-800">Please sign in before placing the order so we can save the cart, billing context, and final order reference correctly.</p>
                                <a href="{{ route('login') }}" class="{{ $buttonPrimaryClass }} mt-4">Login</a>
                            </div>
                        @endguest

                        @auth
                            {{-- Step 1: submit the current checkout through the correct backend flow. --}}
                            <form method="POST" action="{{ $isReOrderCheckout ? route('orders.reorder.checkout.submit') : route('checkout.submit') }}" class="mt-5" onsubmit="
                                const couponField = document.getElementById('checkoutCouponCodeField');
                                const couponInput = document.getElementById('couponInput');
                                if (couponField && couponInput) {
                                    couponField.value = (couponInput.value || '').trim().toUpperCase();
                                }
                                const copyFieldValue = function (targetId, sourceId, transform) {
                                    const target = document.getElementById(targetId);
                                    const source = document.getElementById(sourceId);
                                    if (!target || !source) return;
                                    const rawValue = source.value || '';
                                    target.value = transform ? transform(rawValue) : rawValue;
                                };
                                copyFieldValue('checkoutSelectedAddressSourceField', 'checkoutAddressSourceStateField');
                                copyFieldValue('checkoutSelectedUserAddressIdField', 'checkoutSelectedAddressIdStateField');
                                copyFieldValue('checkoutNewAddressLabelField', 'newAddressLabelInput');
                                copyFieldValue('checkoutNewAddressLine1Field', 'newAddressLine1Input');
                                copyFieldValue('checkoutNewAddressCityField', 'newAddressCityInput');
                                copyFieldValue('checkoutNewAddressStateField', 'newAddressStateInput');
                                copyFieldValue('checkoutNewAddressPostalCodeField', 'newAddressPostalCodeInput');
                                copyFieldValue('checkoutNewAddressPhoneField', 'newAddressPhoneInput');
                                copyFieldValue('checkoutNotesField', 'orderNotes');
                                copyFieldValue('checkoutGstinField', 'gstinInput', function (value) { return value.trim().toUpperCase(); });
                                copyFieldValue('checkoutPanNumberField', 'panInput', function (value) { return value.trim().toUpperCase(); });
                                copyFieldValue('checkoutRegisteredBusinessNameField', 'registeredBusinessNameInput');
                                if (typeof window.syncCheckoutReOrderItems === 'function') {
                                    window.syncCheckoutReOrderItems();
                                }
                                const btn = this.querySelector('button[type=submit]');
                                btn.disabled = true;
                                btn.innerHTML = `
                                    <svg class='h-4 w-4 animate-spin' xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24'>
                                        <circle class='opacity-25' cx='12' cy='12' r='10' stroke='currentColor' stroke-width='4'></circle>
                                        <path class='opacity-75' fill='currentColor' d='M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z'></path>
                                    </svg>
                                    Processing Order...
                                `;
                                btn.classList.add('cursor-wait');
                            ">
                                @csrf

                                {{-- Step 2: submit the current coupon code and reorder payload when needed. --}}
                                <input type="hidden" name="reorder_items" id="checkoutReOrderItemsField" value="{{ old('reorder_items') }}">
                                <input type="hidden" name="coupon_code" id="checkoutCouponCodeField" value="{{ old('coupon_code') }}">
                                <input type="hidden" name="selected_address_source" id="checkoutSelectedAddressSourceField" value="{{ old('selected_address_source', $selectedAddressSource) }}">
                                <input type="hidden" id="checkoutAddressSourceStateField" value="{{ old('selected_address_source', $selectedAddressSource) }}">
                                <input type="hidden" name="selected_user_address_id" id="checkoutSelectedUserAddressIdField" value="{{ old('selected_user_address_id', $selectedAddressId) }}">
                                <input type="hidden" id="checkoutSelectedAddressIdStateField" value="{{ old('selected_user_address_id', $selectedAddressId) }}">
                                <input type="hidden" name="new_address_label" id="checkoutNewAddressLabelField" value="{{ old('new_address_label') }}">
                                <input type="hidden" name="new_address_line1" id="checkoutNewAddressLine1Field" value="{{ old('new_address_line1') }}">
                                <input type="hidden" name="new_address_city" id="checkoutNewAddressCityField" value="{{ old('new_address_city') }}">
                                <input type="hidden" name="new_address_state" id="checkoutNewAddressStateField" value="{{ old('new_address_state') }}">
                                <input type="hidden" name="new_address_postal_code" id="checkoutNewAddressPostalCodeField" value="{{ old('new_address_postal_code') }}">
                                <input type="hidden" name="new_address_phone" id="checkoutNewAddressPhoneField" value="{{ old('new_address_phone') }}">
                                <input type="hidden" name="new_address_country" value="India">
                                <input type="hidden" name="gstin" id="checkoutGstinField" value="{{ old('gstin') }}">
                                <input type="hidden" name="pan_number" id="checkoutPanNumberField" value="{{ old('pan_number') }}">
                                <input type="hidden" name="registered_business_name" id="checkoutRegisteredBusinessNameField" value="{{ old('registered_business_name') }}">
                                <input type="hidden" name="notes" id="checkoutNotesField" value="{{ old('notes') }}">

                                {{-- Step 3: keep the submit action simple because backend already has the final payload. --}}
                                <button type="submit" class="{{ $buttonPrimaryClass }} w-full gap-2 transition-all">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>Place Order</span>
                                </button>
                            </form>
                        @endauth

                        {{-- ─── PROMINENT TRUST BADGES ─── --}}
                        <div class="mt-5 grid grid-cols-3 gap-2">
                            @foreach ([
                                ['icon' => 'M12 3 5 6v6c0 5 3.5 8 7 9 3.5-1 7-4 7-9V6l-7-3Zm0 4v5m0 2.5h.01', 'title' => 'SSL Secured', 'sub' => '256-bit encryption', 'color' => 'bg-primary-50 text-primary-600'],
                                ['icon' => 'M3 6h18M3 10h18M5 6v14h14V6M9 10v8M15 10v8',                          'title' => 'Secure Pay',   'sub' => 'PCI-DSS compliant', 'color' => 'bg-primary-50 text-primary-600'],
                                ['icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',                       'title' => 'Safe Order',   'sub' => '100% guaranteed',   'color' => 'bg-violet-50 text-violet-700'],
                            ] as $badge)
                                <div class="flex flex-col items-center gap-1.5 rounded-2xl border border-slate-100 bg-slate-50 p-3 text-center">
                                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl {{ $badge['color'] }}">
                                        <svg class="h-4.5 w-4.5 h-[18px] w-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $badge['icon'] }}" />
                                        </svg>
                                    </span>
                                    <p class="text-[11px] font-bold text-slate-800 leading-tight">{{ $badge['title'] }}</p>
                                    <p class="text-[10px] text-slate-500 leading-tight">{{ $badge['sub'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </section>

                    <section class="{{ $helpCardClass }}">
                        <div class="flex items-start gap-3">
                            <div class="{{ $iconTileSuccessClass }}">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                    <path d="M22 12h-4l-3 8-5-16-3 8H2"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-base font-semibold text-slate-950">Need help with your order?</h4>
                                <p class="mt-1 text-sm leading-6 text-slate-500">Contact our bioscience experts for delivery scheduling, compliance, and procurement support.</p>
                                <p class="mt-3 text-sm font-semibold text-primary-700">1800-BIO-GENIX</p>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                /* ── selectors ── */
                const summaryItems     = document.getElementById('checkoutSummaryItems');
                const subtotalEl       = document.getElementById('checkoutSubtotal');
                const taxEl            = document.getElementById('checkoutTax');
                const totalEl          = document.getElementById('checkoutTotal');
                const couponDiscountRow = document.getElementById('couponDiscountRow');
                const couponDiscountAmountEl = document.getElementById('couponDiscountAmount');
                const emptyState       = document.getElementById('checkoutEmptyState');
                const paymentCards     = Array.from(document.querySelectorAll('[data-payment-card]'));
                const paymentInputs    = Array.from(document.querySelectorAll('input[name="payment_method"]'));
                const addressCards     = Array.from(document.querySelectorAll('[data-address-card]'));
                const poUploadPanel    = document.getElementById('poUploadPanel');
                const selectedAddressSourceStateField = document.getElementById('checkoutAddressSourceStateField');
                const selectedAddressIdStateField     = document.getElementById('checkoutSelectedAddressIdStateField');

                /* ── Add New Address inline form ── */
                const addToggleBtn  = document.getElementById('addAddressToggleBtn');
                const addForm       = document.getElementById('addAddressForm');
                const addCloseBtn   = document.getElementById('addAddressCloseBtn');
                const addCloseBtn2  = document.getElementById('addAddressCloseBtn2');
                const saveAddressBtn = document.getElementById('saveAddressBtn');

                function openAddressForm() {
                    if (addForm) {
                        addForm.classList.remove('hidden');
                        addForm.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                }
                function closeAddressForm() {
                    if (addForm) addForm.classList.add('hidden');
                }

                const syncAddAddressButtonState = function (isActive) {
                    if (!addToggleBtn) return;
                    addToggleBtn.classList.toggle('border-primary-200', isActive);
                    addToggleBtn.classList.toggle('bg-primary-50', isActive);
                    addToggleBtn.classList.toggle('border-slate-200', !isActive);
                    addToggleBtn.classList.toggle('bg-white', !isActive);
                };

                const activateNewAddressSelection = function (keepFormOpen) {
                    if (selectedAddressSourceStateField) selectedAddressSourceStateField.value = 'new';
                    if (selectedAddressIdStateField) selectedAddressIdStateField.value = '';
                    addressCards.forEach(function (node) {
                        node.dataset.selected = 'false';
                        node.classList.remove('border-primary-200', 'bg-primary-50');
                        node.classList.add('border-slate-200', 'bg-white');
                    });
                    syncAddAddressButtonState(true);
                    if (keepFormOpen) {
                        openAddressForm();
                    } else {
                        closeAddressForm();
                    }
                };

                if (addToggleBtn) addToggleBtn.addEventListener('click', openAddressForm);
                if (addCloseBtn)  addCloseBtn.addEventListener('click', closeAddressForm);
                if (addCloseBtn2) addCloseBtn2.addEventListener('click', closeAddressForm);
                if (saveAddressBtn) {
                    saveAddressBtn.addEventListener('click', function () {
                        activateNewAddressSelection(false);
                    });
                }

                /* ── Step progress bar ── */
                const progressLine = document.getElementById('stepProgressLine');
                const progressConnectors = progressLine ? Array.from(progressLine.querySelectorAll('[data-step-connector]')) : [];
                function setStep(n) {
                    if (!progressLine) return;
                    progressConnectors.forEach(function (connector, index) {
                        const active = index < n - 1;
                        connector.classList.toggle('bg-primary-600', active);
                        connector.classList.toggle('bg-slate-200', !active);
                    });
                    [1, 2, 3].forEach(function (s) {
                        var circle = document.querySelector('#stepIndicator' + s + ' .step-circle');
                        if (!circle) return;
                        if (s <= n) {
                            circle.classList.remove('border-slate-300', 'text-slate-400');
                            circle.classList.add('border-primary-600', 'bg-primary-600', 'text-white');
                        } else {
                            circle.classList.add('border-slate-300', 'text-slate-400');
                            circle.classList.remove('border-primary-600', 'bg-primary-600', 'text-white');
                        }
                    });
                }
                setStep(1);

                /* ── Coupon code ── */
                const couponInput       = document.getElementById('couponInput');
                const couponApplyBtn    = document.getElementById('couponApplyBtn');
                const couponMsg         = document.getElementById('couponMsg');
                const couponCodeField   = document.getElementById('checkoutCouponCodeField');
                const couponValidationUrl = @json(auth()->check() ? route('checkout.coupon.validate') : null);
                const reOrderPricingUrl = @json(auth()->check() && $isReOrderCheckout ? route('checkout.reorder.pricing') : null);
                const defaultCouponMessage = 'Click Apply to validate the coupon.';
                let activeCouponPreview = null;
                let liveCouponCode = '';
                let latestCouponRequestNumber = 0;
                let latestReOrderPricingRequestNumber = 0;

                const showCouponMessage = function (message, isSuccess) {
                    if (!couponMsg) {
                        return;
                    }

                    couponMsg.textContent = message;

                    if (isSuccess) {
                        couponMsg.className = 'mt-2 min-h-[1.1rem] text-xs font-semibold text-primary-600';
                    } else {
                        couponMsg.className = 'mt-2 min-h-[1.1rem] text-xs font-medium text-slate-500';
                    }
                };

                const hideCouponDiscountRow = function () {
                    if (!couponDiscountRow) {
                        return;
                    }

                    couponDiscountRow.classList.add('hidden');
                    couponDiscountRow.classList.remove('flex');
                };

                const showCouponDiscountRow = function (couponDiscountAmount) {
                    if (couponDiscountRow) {
                        couponDiscountRow.classList.remove('hidden');
                        couponDiscountRow.classList.add('flex');
                    }

                    if (couponDiscountAmountEl) {
                        couponDiscountAmountEl.textContent = '– ' + formatInr(couponDiscountAmount);
                    }
                };

                const currentCouponCode = function () {
                    const couponCode = (couponInput ? couponInput.value : '').trim().toUpperCase();

                    return couponCode;
                };

                const syncCouponCodeField = function () {
                    const couponCode = currentCouponCode();

                    if (couponInput) {
                        couponInput.value = couponCode;
                    }

                    if (couponCodeField) {
                        couponCodeField.value = couponCode;
                    }
                };

                const clearCouponPreview = function () {
                    activeCouponPreview = null;
                    hideCouponDiscountRow();
                };

                const currentLiveCouponCode = function () {
                    const couponCode = currentCouponCode();

                    if (!couponCode) {
                        return '';
                    }

                    if (!liveCouponCode) {
                        return '';
                    }

                    if (liveCouponCode !== couponCode) {
                        return '';
                    }

                    return couponCode;
                };

                /* ── formatInr ── */
                const isReOrderCheckout = @json($isReOrderCheckout);
                const initialReOrderItems = @json($reOrderCheckout['items'] ?? []);
                let reOrderItems = [];
                let optimisticCartItems = null;

                const normalizeReOrderItem = function (item) {
                    const currentItem = item || {};
                    let variantId = null;
                    let productId = 0;
                    let quantity = 1;
                    let unitPrice = 0;
                    let unitTaxAmount = 0;
                    let unitPriceAfterGst = 0;
                    let taxAmount = 0;
                    let lineSubtotal = 0;
                    let lineTotal = 0;
                    let discountAmount = 0;
                    let currency = 'INR';
                    let priceType = null;
                    let minOrderQuantity = 1;
                    let maxOrderQuantity = null;
                    let lotSize = 1;
                    let name = 'Product';
                    let model = 'N/A';
                    let image = 'https://via.placeholder.com/96x96?text=Bio';

                    if (Object.prototype.hasOwnProperty.call(currentItem, 'variantId')) {
                        variantId = currentItem.variantId;
                    }

                    if (productId === 0 && currentItem.productId) {
                        productId = currentItem.productId;
                    }

                    if (productId === 0 && currentItem.product_id) {
                        productId = currentItem.product_id;
                    }

                    if (currentItem.quantity) {
                        quantity = currentItem.quantity;
                    }

                    if (currentItem.unitPrice !== undefined) {
                        unitPrice = currentItem.unitPrice;
                    } else if (currentItem.unit_price !== undefined) {
                        unitPrice = currentItem.unit_price;
                    }

                    if (currentItem.unitTaxAmount !== undefined) {
                        unitTaxAmount = currentItem.unitTaxAmount;
                    } else if (currentItem.unit_tax_amount !== undefined) {
                        unitTaxAmount = currentItem.unit_tax_amount;
                    }

                    if (currentItem.unitPriceAfterGst !== undefined) {
                        unitPriceAfterGst = currentItem.unitPriceAfterGst;
                    } else if (currentItem.unit_price_after_gst !== undefined) {
                        unitPriceAfterGst = currentItem.unit_price_after_gst;
                    }

                    if (currentItem.taxAmount !== undefined) {
                        taxAmount = currentItem.taxAmount;
                    } else if (currentItem.tax_amount !== undefined) {
                        taxAmount = currentItem.tax_amount;
                    }

                    if (currentItem.lineSubtotal !== undefined) {
                        lineSubtotal = currentItem.lineSubtotal;
                    } else if (currentItem.line_subtotal !== undefined) {
                        lineSubtotal = currentItem.line_subtotal;
                    }

                    if (currentItem.lineTotal !== undefined) {
                        lineTotal = currentItem.lineTotal;
                    } else if (currentItem.line_total !== undefined) {
                        lineTotal = currentItem.line_total;
                    }

                    if (currentItem.discountAmount !== undefined) {
                        discountAmount = currentItem.discountAmount;
                    } else if (currentItem.discount_amount !== undefined) {
                        discountAmount = currentItem.discount_amount;
                    }

                    if (currentItem.currency !== undefined) {
                        currency = currentItem.currency;
                    }

                    if (currentItem.priceType !== undefined) {
                        priceType = currentItem.priceType;
                    } else if (currentItem.price_type !== undefined) {
                        priceType = currentItem.price_type;
                    }

                    if (currentItem.minOrderQuantity !== undefined) {
                        minOrderQuantity = currentItem.minOrderQuantity;
                    } else if (currentItem.min_order_quantity !== undefined) {
                        minOrderQuantity = currentItem.min_order_quantity;
                    }

                    if (currentItem.maxOrderQuantity !== undefined) {
                        maxOrderQuantity = currentItem.maxOrderQuantity;
                    } else if (currentItem.max_order_quantity !== undefined) {
                        maxOrderQuantity = currentItem.max_order_quantity;
                    }

                    if (currentItem.lotSize !== undefined) {
                        lotSize = currentItem.lotSize;
                    } else if (currentItem.lot_size !== undefined) {
                        lotSize = currentItem.lot_size;
                    }

                    if (currentItem.name) {
                        name = currentItem.name;
                    } else if (currentItem.product_name) {
                        name = currentItem.product_name;
                    }

                    if (currentItem.model) {
                        model = currentItem.model;
                    } else if (currentItem.sku) {
                        model = currentItem.sku;
                    }

                    if (currentItem.image) {
                        image = currentItem.image;
                    } else if (currentItem.image_url) {
                        image = currentItem.image_url;
                    }

                    return {
                        productId: Number(productId || 0),
                        variantId: variantId === null || variantId === '' ? null : Number(variantId),
                        quantity: Math.max(1, Number(quantity || 1)),
                        unitPrice: Number(unitPrice || 0),
                        unitTaxAmount: Number(unitTaxAmount || 0),
                        unitPriceAfterGst: Number(unitPriceAfterGst || 0),
                        taxAmount: Number(taxAmount || 0),
                        lineSubtotal: Number(lineSubtotal || 0),
                        lineTotal: Number(lineTotal || 0),
                        discountAmount: Number(discountAmount || 0),
                        currency: String(currency || 'INR'),
                        priceType: priceType === null ? null : String(priceType),
                        minOrderQuantity: Math.max(1, Number(minOrderQuantity || 1)),
                        maxOrderQuantity: maxOrderQuantity === null || maxOrderQuantity === '' ? null : Number(maxOrderQuantity),
                        lotSize: Math.max(1, Number(lotSize || 1)),
                        name: String(name || 'Product'),
                        model: String(model || 'N/A'),
                        image: String(image || 'https://via.placeholder.com/96x96?text=Bio'),
                    };
                };

                const recalculateReOrderItem = function (item) {
                    const quantity = Math.max(1, Number(item.quantity || 1));
                    const unitPrice = Number(item.unitPrice || 0);
                    const unitTaxAmount = Number(item.unitTaxAmount || 0);
                    const unitPriceAfterGst = Number(item.unitPriceAfterGst || (unitPrice + unitTaxAmount));

                    item.quantity = quantity;
                    item.lineSubtotal = unitPrice * quantity;
                    item.taxAmount = unitTaxAmount * quantity;
                    item.lineTotal = unitPriceAfterGst * quantity;

                    return item;
                };

                const syncReOrderItemsField = function () {
                    const reOrderItemsField = document.getElementById('checkoutReOrderItemsField');

                    if (!reOrderItemsField) {
                        return;
                    }

                    if (!isReOrderCheckout) {
                        reOrderItemsField.value = '';
                        return;
                    }

                    reOrderItemsField.value = JSON.stringify(reOrderItems);
                };

                if (Array.isArray(initialReOrderItems)) {
                    reOrderItems = initialReOrderItems.map(function (item) {
                        const normalizedItem = normalizeReOrderItem(item);

                        return recalculateReOrderItem(normalizedItem);
                    });
                }

                const getCheckoutItems = function () {
                    if (isReOrderCheckout) {
                        return reOrderItems;
                    }

                    if (optimisticCartItems !== null) {
                        return optimisticCartItems;
                    }

                    return window.CartStore ? window.CartStore.getItems() : [];
                };

                const buildUpdatedCheckoutItems = function (checkoutItems, productId, variantId, quantity) {
                    const normalizedProductId = Number(productId || 0);
                    const normalizedVariantId = variantId === null || variantId === '' ? null : Number(variantId);
                    const normalizedQuantity = Math.max(1, Number(quantity || 1));

                    return checkoutItems.map(function (item) {
                        const normalizedItem = normalizeReOrderItem(item);
                        const currentProductId = Number(normalizedItem.productId || 0);
                        const currentVariantId = normalizedItem.variantId === null ? null : Number(normalizedItem.variantId);

                        if (currentProductId === normalizedProductId && currentVariantId === normalizedVariantId) {
                            normalizedItem.quantity = normalizedQuantity;
                        }

                        return recalculateReOrderItem(normalizedItem);
                    });
                };

                window.syncCheckoutReOrderItems = function () {
                    syncReOrderItemsField();
                };

                const readCouponValidationMessage = function (responseData) {
                    let couponMessage = 'Unable to validate coupon right now.';

                    if (responseData && responseData.message) {
                        couponMessage = responseData.message;
                    }

                    if (
                        responseData
                        && responseData.errors
                        && responseData.errors.coupon_code
                        && responseData.errors.coupon_code.length
                    ) {
                        couponMessage = responseData.errors.coupon_code[0];
                    }

                    return couponMessage;
                };

                const readReOrderPricingMessage = function (responseData) {
                    let pricingMessage = 'Unable to refresh checkout pricing right now.';

                    if (responseData && responseData.message) {
                        pricingMessage = responseData.message;
                    }

                    if (responseData && responseData.errors) {
                        for (const errorKey in responseData.errors) {
                            const errorMessages = responseData.errors[errorKey];

                            if (Array.isArray(errorMessages) && errorMessages.length) {
                                pricingMessage = errorMessages[0];
                                break;
                            }
                        }
                    }

                    return pricingMessage;
                };

                const applyReOrderPreviewItems = function (previewItems) {
                    reOrderItems = previewItems.map(function (item) {
                        const normalizedItem = normalizeReOrderItem(item);

                        return recalculateReOrderItem(normalizedItem);
                    });

                    syncReOrderItemsField();
                };

                const refreshReOrderPricing = async function (updatedReOrderItems) {
                    // Step 1: stop early when reorder live pricing is not available.
                    if (!reOrderPricingUrl) {
                        applyReOrderPreviewItems(updatedReOrderItems);

                        if (typeof render === 'function') {
                            render();
                        }

                        return;
                    }

                    // Step 2: keep one request number so old responses do not replace a newer price result.
                    latestReOrderPricingRequestNumber += 1;
                    const reOrderPricingRequestNumber = latestReOrderPricingRequestNumber;

                    // Step 3: prepare the reorder pricing request body from the latest items.
                    const requestBody = {
                        reorder_items: JSON.stringify(updatedReOrderItems),
                        coupon_code: currentLiveCouponCode(),
                    };

                    try {
                        // Step 4: request the latest live pricing from the backend.
                        const response = await fetch(reOrderPricingUrl, {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                            },
                            body: JSON.stringify(requestBody),
                        });

                        // Step 5: read the JSON result once for both success and error paths.
                        const responseData = await response.json().catch(function () {
                            return null;
                        });

                        // Step 6: stop when a newer reorder pricing request has already started.
                        if (reOrderPricingRequestNumber !== latestReOrderPricingRequestNumber) {
                            return;
                        }

                        // Step 7: stop and keep the old values when the backend rejects the change.
                        if (!response.ok || !responseData || responseData.status !== 'success') {
                            showCouponMessage(readReOrderPricingMessage(responseData), false);
                            return;
                        }

                        // Step 8: apply the refreshed item prices returned by the backend.
                        const reOrderPreview = responseData.reorder_preview || {};
                        const previewItems = Array.isArray(reOrderPreview.items) ? reOrderPreview.items : updatedReOrderItems;
                        applyReOrderPreviewItems(previewItems);

                        // Step 9: keep the live coupon summary aligned with the refreshed item prices.
                        const couponPreview = reOrderPreview.coupon_preview || null;

                        if (couponPreview && couponPreview.is_coupon_applied) {
                            activeCouponPreview = couponPreview;
                            showCouponMessage(couponPreview.coupon_message || 'Coupon applied successfully.', true);
                        } else {
                            activeCouponPreview = null;

                            if (couponPreview && couponPreview.coupon_message) {
                                showCouponMessage(couponPreview.coupon_message, false);
                            }
                        }

                        // Step 10: refresh the checkout summary using the latest live values.
                        if (typeof render === 'function') {
                            render();
                        }
                    } catch (error) {
                        // Step 11: stop when a newer reorder pricing request has already started.
                        if (reOrderPricingRequestNumber !== latestReOrderPricingRequestNumber) {
                            return;
                        }

                        // Step 12: keep the page stable when live repricing fails.
                        showCouponMessage('Unable to refresh checkout pricing right now.', false);
                    }
                };

                const validateCouponForCheckout = async function (showCheckingMessage) {
                    // Step 1: keep the visible and hidden coupon values aligned.
                    syncCouponCodeField();

                    // Step 2: stop early when the coupon field is empty.
                    const couponCode = currentCouponCode();

                    if (!couponCode) {
                        clearCouponPreview();
                        showCouponMessage('Please enter a coupon code.', false);
                        if (typeof render === 'function') {
                            render();
                        }

                        return;
                    }

                    // Step 3: stop early when there are no checkout items yet.
                    if (!getCheckoutItems().length) {
                        clearCouponPreview();
                        showCouponMessage('Add at least one item before applying a coupon.', false);
                        if (typeof render === 'function') {
                            render();
                        }

                        return;
                    }

                    // Step 4: ask the user to login first when the live coupon endpoint is not available.
                    if (!couponValidationUrl) {
                        clearCouponPreview();
                        showCouponMessage('Please login before applying a coupon.', false);
                        if (typeof render === 'function') {
                            render();
                        }

                        return;
                    }

                    // Step 5: show a short checking message before the request starts.
                    if (showCheckingMessage) {
                        showCouponMessage('Checking coupon...', false);
                    }

                    // Step 6: prepare one request id so older responses do not replace a newer coupon result.
                    latestCouponRequestNumber += 1;
                    const couponRequestNumber = latestCouponRequestNumber;

                    // Step 7: build the request payload from the current checkout state.
                    const requestBody = {
                        coupon_code: couponCode,
                        is_reorder_checkout: isReOrderCheckout,
                        reorder_items: isReOrderCheckout ? JSON.stringify(reOrderItems) : '',
                    };

                    try {
                        // Step 8: call the live coupon validation endpoint.
                        const response = await fetch(couponValidationUrl, {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                            },
                            body: JSON.stringify(requestBody),
                        });

                        // Step 9: read the JSON payload once for both success and error paths.
                        const responseData = await response.json().catch(function () {
                            return null;
                        });

                        // Step 10: stop when a newer coupon request has already started.
                        if (couponRequestNumber !== latestCouponRequestNumber) {
                            return;
                        }

                        // Step 11: clear the coupon preview when the backend rejects the coupon.
                        if (!response.ok || !responseData || responseData.status !== 'success') {
                            clearCouponPreview();
                            showCouponMessage(readCouponValidationMessage(responseData), false);

                            if (typeof render === 'function') {
                                render();
                            }

                            return;
                        }

                        // Step 12: keep the latest coupon preview ready for the summary section.
                        activeCouponPreview = responseData.coupon_preview || null;
                        liveCouponCode = couponCode;

                        // Step 13: refresh the summary totals and message from the live preview.
                        if (typeof render === 'function') {
                            render();
                        }

                        showCouponMessage(
                            activeCouponPreview && activeCouponPreview.coupon_message
                                ? activeCouponPreview.coupon_message
                                : 'Coupon applied successfully.',
                            true
                        );
                    } catch (error) {
                        // Step 14: stop when the request result is no longer the latest one.
                        if (couponRequestNumber !== latestCouponRequestNumber) {
                            return;
                        }

                        // Step 15: keep the UI stable when the live request fails.
                        clearCouponPreview();
                        showCouponMessage('Unable to validate coupon right now.', false);

                        if (typeof render === 'function') {
                            render();
                        }
                    }
                };

                const refreshAppliedCouponPreview = function (showCheckingMessage) {
                    // Step 1: clear the preview when the current code is no longer the applied one.
                    if (!currentLiveCouponCode()) {
                        clearCouponPreview();

                        if (typeof render === 'function') {
                            render();
                        }

                        return;
                    }

                    // Step 2: revalidate the current coupon against the latest items.
                    return validateCouponForCheckout(showCheckingMessage);
                };

                window.checkoutUpdateItem = function (productId, variantId, quantity) {
                    if (isReOrderCheckout) {
                        const normalizedProductId = Number(productId || 0);
                        const normalizedVariantId = variantId === null || variantId === '' ? null : Number(variantId);
                        const normalizedQuantity = Math.max(1, Number(quantity || 1));

                        const updatedReOrderItems = reOrderItems.map(function (item) {
                            const currentProductId = Number(item.productId || 0);
                            const currentVariantId = item.variantId === null ? null : Number(item.variantId);

                            if (currentProductId !== normalizedProductId) {
                                return item;
                            }

                            if (currentVariantId !== normalizedVariantId) {
                                return item;
                            }

                            const updatedItem = Object.assign({}, item);
                            updatedItem.quantity = normalizedQuantity;

                            return recalculateReOrderItem(updatedItem);
                        });

                        refreshReOrderPricing(updatedReOrderItems);

                        return;
                    }

                    if (window.CartStore && typeof window.CartStore.updateQuantity === 'function') {
                        const previousCouponPreview = activeCouponPreview;
                        const previousLiveCouponCode = liveCouponCode;
                        const updatedCartItems = buildUpdatedCheckoutItems(getCheckoutItems(), productId, variantId, quantity);

                        optimisticCartItems = updatedCartItems;
                        clearCouponPreview();

                        if (typeof render === 'function') {
                            render();
                        }

                        const updateRequest = window.CartStore.updateQuantity(
                            Number(productId || 0),
                            variantId === null || variantId === '' ? null : Number(variantId),
                            Math.max(1, Number(quantity || 1))
                        );

                        if (updateRequest && typeof updateRequest.then === 'function') {
                            updateRequest.then(function (updateResult) {
                                optimisticCartItems = null;

                                if (!updateResult || updateResult.ok !== false) {
                                    if (typeof render === 'function') {
                                        render();
                                    }

                                    refreshAppliedCouponPreview(false);
                                    return;
                                }

                                activeCouponPreview = previousCouponPreview;
                                liveCouponCode = previousLiveCouponCode;

                                if (typeof render === 'function') {
                                    render();
                                }

                                if (
                                    updateResult.errors
                                    && updateResult.errors.quantity
                                    && updateResult.errors.quantity.length
                                ) {
                                    showCouponMessage(updateResult.errors.quantity[0], false);
                                    return;
                                }

                                if (updateResult.message) {
                                    showCouponMessage(updateResult.message, false);
                                }
                            });
                        }
                    }
                };

                window.checkoutRemoveItem = function (productId, variantId) {
                    if (isReOrderCheckout) {
                        const normalizedProductId = Number(productId || 0);
                        const normalizedVariantId = variantId === null || variantId === '' ? null : Number(variantId);

                        reOrderItems = reOrderItems.filter(function (item) {
                            const currentProductId = Number(item.productId || 0);
                            const currentVariantId = item.variantId === null ? null : Number(item.variantId);

                            if (currentProductId !== normalizedProductId) {
                                return true;
                            }

                            return currentVariantId !== normalizedVariantId;
                        });

                        syncReOrderItemsField();

                        if (typeof render === 'function') {
                            render();
                        }

                        refreshAppliedCouponPreview(false);

                        return;
                    }

                    if (window.CartStore && typeof window.CartStore.removeItem === 'function') {
                        const removeRequest = window.CartStore.removeItem(
                            Number(productId || 0),
                            variantId === null || variantId === '' ? null : Number(variantId)
                        );

                        if (removeRequest && typeof removeRequest.then === 'function') {
                            removeRequest.then(function (removeResult) {
                                if (!removeResult || removeResult.ok !== false) {
                                    refreshAppliedCouponPreview(false);
                                }
                            });
                        }
                    }
                };

                const formatInr = function (value) {
                    const numeric = Number(value);
                    if (!Number.isFinite(numeric)) return 'Rs. 0.00';
                    return 'Rs. ' + numeric.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                };

                /* ── render summary row ── */
                const getLineSubtotal = function (item) {
                    let lineSubtotal = item.lineSubtotal;

                    if (lineSubtotal === undefined) {
                        lineSubtotal = item.line_subtotal;
                    }

                    // Step 1: prefer the backend subtotal when the current cart line came from the authenticated cart.
                    if (Number.isFinite(Number(lineSubtotal))) {
                        return Number(lineSubtotal);
                    }

                    // Step 2: keep the existing guest subtotal fallback for pre-login browsing.
                    let unitPrice = item.unitPrice;

                    if (unitPrice === undefined) {
                        unitPrice = item.unit_price;
                    }

                    return Number(unitPrice || 0) * Math.max(1, Number(item.quantity || 1));
                };

                const getLineTax = function (item) {
                    let taxAmount = item.taxAmount;

                    if (taxAmount === undefined) {
                        taxAmount = item.tax_amount;
                    }

                    // Step 1: prefer the backend tax when the current cart line came from the authenticated cart.
                    if (Number.isFinite(Number(taxAmount))) {
                        return Number(taxAmount);
                    }

                    // Step 2: keep the existing guest GST fallback for pre-login browsing.
                    return getLineSubtotal(item) * 0.18;
                };

                const getLineTotal = function (item) {
                    let lineTotal = item.lineTotal;

                    if (lineTotal === undefined) {
                        lineTotal = item.line_total;
                    }

                    // Step 1: prefer the backend total when the current cart line came from the authenticated cart.
                    if (Number.isFinite(Number(lineTotal))) {
                        return Number(lineTotal);
                    }

                    // Step 2: keep the existing guest total fallback for pre-login browsing.
                    return getLineSubtotal(item) + getLineTax(item);
                };

                const getItemMinimumQuantity = function (item) {
                    let minimumQuantity = item.minOrderQuantity;

                    if (minimumQuantity === undefined) {
                        minimumQuantity = item.min_order_quantity;
                    }

                    return Math.max(1, Number(minimumQuantity || 1));
                };

                const getItemQuantityStep = function (item) {
                    let quantityStep = item.lotSize;

                    if (quantityStep === undefined) {
                        quantityStep = item.lot_size;
                    }

                    return Math.max(1, Number(quantityStep || 1));
                };

                const renderSummaryRow = function (item) {
                    const quantity = Math.max(1, Number(item.quantity || 1));
                    const minimumQuantity = getItemMinimumQuantity(item);
                    const quantityStep = getItemQuantityStep(item);
                    const decreaseQuantity = Math.max(minimumQuantity, quantity - quantityStep);
                    const increaseQuantity = quantity + quantityStep;
                    // Step 1: show the product line price before GST so it stays aligned with the subtotal.
                    const subtotal = getLineSubtotal(item);
                    const image = String(item.image || item.image_url || 'https://via.placeholder.com/96x96?text=Bio');
                    const name = String(item.name || item.product_name || 'Product');
                    const model = String(item.model || item.sku || 'N/A');
                    let productId = item.productId;
                    let variantId = item.variantId;

                    if (productId === undefined) {
                        productId = item.product_id;
                    }

                    if (variantId === undefined) {
                        variantId = item.product_variant_id;
                    }

                    productId = Number(productId || 0);
                    const variantValue = variantId === null || variantId === '' ? 'null' : String(Number(variantId));
                    return `
                        <div class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-3 py-3">
                            <div class="h-16 w-16 shrink-0 overflow-hidden rounded-2xl bg-slate-100">
                                <img src="${image}" alt="${name}" class="h-full w-full object-cover">
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-900">${name}</p>
                                <p class="mt-0.5 text-xs text-slate-500">${model}</p>
                                <div class="mt-2 flex items-center gap-3">
                                    <div class="flex items-center rounded-lg border border-slate-200 bg-white">
                                        <button type="button" class="flex h-7 w-7 items-center justify-center text-slate-500 transition hover:bg-slate-100 hover:text-slate-900" onclick="window.checkoutUpdateItem(${productId}, ${variantValue}, ${decreaseQuantity});">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" /></svg>
                                        </button>
                                        <input type="number" readonly class="w-8 border-x border-slate-200 bg-transparent text-center text-xs font-semibold text-slate-900 outline-none" value="${quantity}">
                                        <button type="button" class="flex h-7 w-7 items-center justify-center text-slate-500 transition hover:bg-slate-100 hover:text-slate-900" onclick="window.checkoutUpdateItem(${productId}, ${variantValue}, ${increaseQuantity});">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                                        </button>
                                    </div>
                                    <button type="button" class="text-xs font-medium text-slate-400 underline decoration-slate-300 underline-offset-2 transition hover:text-rose-600 hover:decoration-rose-300" onclick="window.checkoutRemoveItem(${productId}, ${variantValue});">
                                        Remove
                                    </button>
                                </div>
                            </div>
                            <div class="flex flex-col items-end justify-between self-stretch">
                                <span class="text-sm font-semibold text-primary-700">${formatInr(subtotal)}</span>
                            </div>
                        </div>
                    `;
                };

                /* ── render totals ── */
                const render = function () {
                    const items = getCheckoutItems();
                    if (summaryItems) summaryItems.innerHTML = '';

                    if (!items.length) {
                        if (emptyState) emptyState.classList.remove('hidden');
                        if (summaryItems) summaryItems.classList.add('hidden');
                        hideCouponDiscountRow();
                        if (subtotalEl) subtotalEl.innerHTML = 'Rs. 0.00';
                        if (taxEl)      taxEl.innerHTML      = 'Rs. 0.00';
                        if (totalEl)    totalEl.innerHTML    = 'Rs. 0.00';
                        syncReOrderItemsField();
                        return;
                    }

                    if (emptyState) emptyState.classList.add('hidden');
                    if (summaryItems) summaryItems.classList.remove('hidden');

                    var subtotal = 0;
                    var tax = 0;
                    var total = 0;
                    items.forEach(function (item) {
                        subtotal += getLineSubtotal(item);
                        tax += getLineTax(item);
                        total += getLineTotal(item);
                        if (summaryItems) summaryItems.insertAdjacentHTML('beforeend', renderSummaryRow(item));
                    });

                    // Step 1: start by showing the normal live totals from the current items.
                    var displaySubtotal = subtotal;
                    var displayTax = tax;
                    var displayTotal = total;

                    // Step 2: switch to the coupon preview totals only when the current code is still the applied one.
                    if (
                        activeCouponPreview
                        && activeCouponPreview.is_coupon_applied
                        && String(activeCouponPreview.coupon_code || '') === currentCouponCode()
                    ) {
                        var couponDiscountAmount = Number(activeCouponPreview.coupon_discount_amount || 0);
                        var couponSubtotalAmount = Number(activeCouponPreview.order_subtotal_amount || 0);
                        var couponTaxAmount = Number(activeCouponPreview.order_tax_amount || 0);
                        var couponTotalAmount = Number(activeCouponPreview.order_total_amount || 0);

                        displaySubtotal = couponSubtotalAmount + couponDiscountAmount;
                        displayTax = couponTaxAmount;
                        displayTotal = couponTotalAmount;

                        showCouponDiscountRow(couponDiscountAmount);
                    } else {
                        hideCouponDiscountRow();
                    }

                    if (subtotalEl) subtotalEl.innerHTML = formatInr(displaySubtotal);
                    if (taxEl) taxEl.innerHTML = formatInr(displayTax);
                    if (totalEl) totalEl.innerHTML = formatInr(displayTotal);
                    syncReOrderItemsField();
                };

                /* ── address selection ── */
                const setActiveAddress = function (card) {
                    addressCards.forEach(function (node) {
                        const selected = node === card;
                        node.dataset.selected = selected ? 'true' : 'false';
                        node.classList.toggle('border-primary-200', selected);
                        node.classList.toggle('bg-primary-50', selected);
                        node.classList.toggle('border-slate-200', !selected);
                        node.classList.toggle('bg-white', !selected);
                    });
                    if (selectedAddressSourceStateField) selectedAddressSourceStateField.value = 'existing';
                    if (selectedAddressIdStateField) selectedAddressIdStateField.value = card.dataset.addressId || '';
                    syncAddAddressButtonState(false);
                    closeAddressForm();
                };

                /* ── payment card sync ── */
                const syncPaymentCards = function () {
                    paymentCards.forEach(function (card) {
                        const input = card.querySelector('input[type="radio"]');
                        if (!input) return;
                        card.classList.toggle('border-primary-200', input.checked);
                        card.classList.toggle('bg-primary-50', input.checked);
                        card.classList.toggle('border-slate-200', !input.checked);
                        card.classList.toggle('bg-white', !input.checked);
                    });
                    const active = paymentInputs.find(function (i) { return i.checked; });
                    if (poUploadPanel) {
                        poUploadPanel.classList.toggle('hidden', !(active && active.value === 'po'));
                    }
                };

                /* ── events ── */
                addressCards.forEach(function (card) {
                    card.addEventListener('click', function () {
                        setActiveAddress(card);
                    });
                });

                paymentInputs.forEach(function (input) {
                    input.addEventListener('change', syncPaymentCards);
                });

                if (couponInput) {
                    couponInput.addEventListener('input', function () {
                        // Step 1: keep the entered coupon uppercase in both fields.
                        couponInput.value = (couponInput.value || '').toUpperCase();
                        syncCouponCodeField();

                        // Step 2: clear the current coupon result when the user edits the code.
                        liveCouponCode = '';
                        latestCouponRequestNumber += 1;
                        clearCouponPreview();
                        showCouponMessage(defaultCouponMessage, false);

                        if (typeof render === 'function') {
                            render();
                        }
                    });
                }

                if (couponApplyBtn) {
                    couponApplyBtn.addEventListener('click', function () {
                        // Step 1: validate the current coupon with the latest checkout items.
                        validateCouponForCheckout(true);
                    });
                }

                /* ── GSTIN auto-uppercase ── */
                var gstinInput = document.getElementById('gstinInput');
                var panInput   = document.getElementById('panInput');
                if (gstinInput) gstinInput.addEventListener('input', function () { this.value = this.value.toUpperCase(); });
                if (panInput)   panInput.addEventListener('input',   function () { this.value = this.value.toUpperCase(); });

                /* ── init ── */
                if (isReOrderCheckout) {
                    render();
                }

                if (!isReOrderCheckout && window.CartStore) {
                    window.CartStore.subscribe(render);
                }
                if (selectedAddressSourceStateField && selectedAddressSourceStateField.value === 'new') {
                    activateNewAddressSelection(addForm && !addForm.classList.contains('hidden'));
                } else {
                    syncAddAddressButtonState(false);
                }
                syncPaymentCards();
            });
        </script>
    @endpush
@endsection

@push('scripts')
    @if (!is_null($initialCart ?? null))
        <script>
            window.__BIOGENIX_PAGE_CART__ = @json($initialCart);
        </script>
    @endif
@endpush

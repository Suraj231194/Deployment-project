@extends('layouts.app')

@section('title', 'Cart')

@section('content')
    @php
        $previousUrl = url()->previous();
        $currentUrl = url()->current();
        $currentHost = parse_url(url()->to('/'), PHP_URL_HOST);
        $previousHost = $previousUrl ? parse_url($previousUrl, PHP_URL_HOST) : null;
        $backUrl = filled($previousUrl) && $previousUrl !== $currentUrl && (! $previousHost || $previousHost === $currentHost)
            ? $previousUrl
            : route('products.index');
        $pageWrapClass = 'mx-auto w-full max-w-none px-3 py-4 sm:px-6 sm:py-6 lg:px-8 xl:px-10';
        $backLinkClass = 'mb-4 inline-flex items-center gap-2 text-sm font-semibold text-slate-600 transition hover:text-slate-900';
        $heroClass = 'rounded-[28px] border border-slate-200 bg-[linear-gradient(135deg,#ffffff_0%,#f4f7fb_58%,#dbeafe_100%)] p-4 shadow-sm sm:p-6 md:rounded-[32px] md:p-8';
        $eyebrowClass = 'text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400';
        $titleClass = 'mt-3 text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl md:text-4xl';
        $leadClass = 'mt-3 text-sm leading-7 text-slate-600 md:text-base';
        $chipSuccessClass = 'inline-flex items-center gap-2 rounded-full border border-primary-200 bg-primary-50 px-3 py-1.5 text-sm font-semibold text-primary-600';
        $buttonSecondaryClass = 'inline-flex h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50';
        $buttonPrimaryClass = 'inline-flex h-14 items-center justify-center rounded-2xl bg-primary-600 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700';
        $layoutGridClass = 'mt-6 grid gap-5 xl:mt-8 xl:grid-cols-[minmax(0,1fr)_24rem] xl:gap-6';
        $cardClass = 'rounded-[24px] border border-slate-200 bg-white p-4 shadow-sm sm:p-6 md:rounded-[28px] md:p-8';
        $summaryCardClass = 'rounded-[24px] border border-slate-200 bg-white p-4 shadow-sm sm:p-6 md:rounded-[28px] md:p-8 xl:sticky xl:top-6';
        $sectionTitleClass = 'text-xl font-semibold text-slate-950';
        $sectionCopyClass = 'mt-1 text-sm leading-6 text-slate-500';
        $iconTilePrimaryClass = 'inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-primary-50 text-primary-700';
        $iconTileSuccessClass = 'inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-primary-50 text-primary-600';
        $helpCardClass = 'rounded-[24px] border border-slate-200 bg-white p-4 shadow-sm sm:p-5 md:rounded-[28px]';
    @endphp

    {{-- Free Shipping Progress Bar Threshold (Rs.) --}}
    @php $freeShippingThreshold = 2000; @endphp

    <div class="{{ $pageWrapClass }}">
        <div>
            <a href="{{ $backUrl }}" class="{{ $backLinkClass }}">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m15 18-6-6 6-6"></path>
                </svg>
                <span>Back</span>
            </a>

            {{-- Hero --}}
            <section class="{{ $heroClass }}">
                <div class="flex flex-col gap-6 xl:flex-row xl:items-center xl:justify-between">
                    <div class="max-w-3xl">
                        <p class="{{ $eyebrowClass }}">Cart Overview</p>
                        <h1 class="{{ $titleClass }}">Review your scientific procurement cart</h1>
                        <p class="{{ $leadClass }}">
                            Validate quantities, compare line totals, and continue to checkout using the same shared design system as the catalog and product detail flows.
                        </p>
                    </div>
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <div class="{{ $chipSuccessClass }}">
                            <span class="inline-flex h-2.5 w-2.5 rounded-full bg-primary-600"></span>
                            Secure cart sync across pages
                        </div>
                        <a href="{{ route('products.index') }}" class="{{ $buttonSecondaryClass }} w-full sm:w-auto">Continue Shopping</a>
                    </div>
                </div>
            </section>

            {{-- -------------------------------------------------------- --}}
            {{-- FREE SHIPPING PROGRESS BAR --}}
            {{-- -------------------------------------------------------- --}}
            <div id="shippingProgressWrap" class="mt-5 overflow-hidden rounded-[22px] border border-primary-200 bg-gradient-to-r from-emerald-50 to-green-50 px-5 py-4">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-primary-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                            </svg>
                        </span>
                        <div>
                            <p id="shippingProgressMsg" class="text-sm font-semibold text-emerald-800">Loading...</p>
                            <p class="text-xs text-primary-600">Free same-day delivery on orders above <span class="font-bold">Rs. {{ number_format($freeShippingThreshold) }}</span></p>
                        </div>
                    </div>
                    <span id="shippingProgressBadge" class="inline-flex w-fit items-center rounded-full bg-primary-600 px-3 py-1 text-xs font-bold text-white">Calculating...</span>
                </div>
                {{-- Bar --}}
                <div
                    id="shippingProgressBar"
                    class="mt-3 flex gap-1"
                    aria-valuenow="0"
                    aria-valuemin="0"
                    aria-valuemax="{{ $freeShippingThreshold }}"
                    role="progressbar"
                    aria-label="Free shipping progress"
                >
                    @for ($segment = 1; $segment <= 12; $segment++)
                        <span data-progress-segment class="h-2.5 flex-1 rounded-full bg-emerald-200 transition-all duration-500"></span>
                    @endfor
                </div>
            </div>

            <div class="{{ $layoutGridClass }}">

                {{-- -------------------------------------------------------- --}}
                {{-- CART ITEMS COLUMN --}}
                {{-- -------------------------------------------------------- --}}
                <div class="space-y-5">
                    <section class="{{ $cardClass }}">
                        <div class="flex flex-col gap-4 border-b border-slate-100 pb-5 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h2 class="{{ $sectionTitleClass }}">Cart Items</h2>
                                <p class="{{ $sectionCopyClass }}">All quantities stay editable before you continue to checkout.</p>
                            </div>
                            <div class="inline-flex items-center rounded-full border border-primary-100 bg-primary-50 px-3 py-1.5 text-sm font-semibold text-primary-700">
                                <span id="cartItemCount">0 items</span>
                            </div>
                        </div>

                        {{-- Cart items list --}}
                        <div id="cartItemsList" class="mt-4 space-y-3"></div>

                        {{-- -------------------------------------------------------- --}}
                        {{-- ANIMATED EMPTY CART ILLUSTRATION --}}
                        {{-- -------------------------------------------------------- --}}
                        <div id="cartEmptyState" class="hidden flex-col items-center py-12 text-center">
                            {{-- Animated SVG Illustration --}}
                            <div class="relative mx-auto mb-6 flex h-40 w-40 items-center justify-center">
                                {{-- Background circle --}}
                                <div class="absolute inset-0 rounded-full bg-slate-100"></div>
                                {{-- Floating dots (CSS animation via inline style) --}}
                                <span class="absolute left-3 top-3 h-3 w-3 rounded-full bg-primary-200 animate-bounce [animation-duration:2.2s]"></span>
                                <span class="absolute right-4 top-6 h-2 w-2 rounded-full bg-primary-300 animate-bounce [animation-duration:2.8s] [animation-delay:0.4s]"></span>
                                <span class="absolute bottom-5 left-6 h-2 w-2 rounded-full bg-emerald-300 animate-bounce [animation-duration:2.5s] [animation-delay:0.8s]"></span>

                                <svg class="relative z-10 h-20 w-20 animate-bounce text-slate-300 [animation-duration:3s]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.3">
                                    <circle cx="9" cy="21" r="1.6" stroke-width="1.5"/>
                                    <circle cx="17" cy="21" r="1.6" stroke-width="1.5"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                                </svg>
                            </div>

                            <h3 class="text-xl font-bold text-slate-900">Your cart is empty</h3>
                            <p class="mt-2 max-w-xs text-sm leading-7 text-slate-500">
                                Add diagnostic products, reagents, or instruments from the catalog to build your procurement order.
                            </p>

                            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                                <a href="{{ route('products.index') }}" class="{{ $buttonPrimaryClass }} gap-2">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    Browse Products
                                </a>
                                <a href="{{ route('quotation.create') }}" class="{{ $buttonSecondaryClass }}">Generate Quote</a>
                            </div>

                            {{-- Wishlist / Saved Items count teaser --}}
                            <p id="savedForLaterTeaser" class="mt-5 hidden text-sm font-medium text-primary-700">
                                You have <span id="savedCountInEmpty"></span> saved item(s) below
                            </p>
                        </div>
                    </section>

                    {{-- -------------------------------------------------------- --}}
                    {{-- SAVED FOR LATER SECTION --}}
                    {{-- -------------------------------------------------------- --}}
                    <section id="savedForLaterSection" class="hidden {{ $cardClass }}">
                        <div class="flex flex-col gap-3 border-b border-slate-100 pb-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-secondary-50 text-secondary-700">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z"/>
                                    </svg>
                                </span>
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-950">Saved for Later</h3>
                                    <p class="text-xs text-slate-500">Move back to cart when ready to order</p>
                                </div>
                            </div>
                            <span id="savedItemCount" class="inline-flex items-center rounded-full border border-amber-200 bg-secondary-50 px-3 py-1 text-xs font-semibold text-secondary-700">0 items</span>
                        </div>
                        <div id="savedForLaterList" class="mt-4 space-y-3"></div>
                    </section>
                </div>

                {{-- -------------------------------------------------------- --}}
                {{-- ORDER SUMMARY COLUMN --}}
                {{-- -------------------------------------------------------- --}}
                <div class="space-y-5">
                    <section class="{{ $summaryCardClass }}">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h3 class="{{ $sectionTitleClass }}">Order Summary</h3>
                                <p class="{{ $sectionCopyClass }}">Mirrors the purchase summary used across the storefront.</p>
                            </div>
                            <div class="{{ $iconTilePrimaryClass }}">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                    <path d="M3 7h18"></path><path d="M7 3v4"></path><path d="M17 3v4"></path><path d="M6 12h4"></path><path d="M6 16h6"></path><rect x="3" y="5" width="18" height="16" rx="2"></rect>
                                </svg>
                            </div>
                        </div>

                        <div id="cartSummaryItems" class="mt-4 space-y-2.5"></div>

                        <div class="mt-4 space-y-3 border-t border-slate-100 pt-4 text-sm text-slate-600">
                            <div class="flex items-center justify-between">
                                <span>Subtotal</span>
                                <span id="cartSummarySubtotal" class="font-medium text-slate-900">Rs. 0.00</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Shipping</span>
                                <span class="font-semibold text-primary-600">FREE</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>GST (18%)</span>
                                <span id="cartSummaryTax" class="font-medium text-slate-900">Rs. 0.00</span>
                            </div>
                            <div class="flex items-center justify-between border-t border-slate-200 pt-3 text-base font-bold text-secondary-600">
                                <span>Estimated Total</span>
                                <span id="cartSummaryTotal">Rs. 0.00</span>
                            </div>
                        </div>

                        <a id="cartCheckoutButton" href="{{ route('checkout.page') }}" class="{{ $buttonPrimaryClass }} mt-5 flex w-full gap-2">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path>
                            </svg>
                            Proceed to Checkout
                        </a>

                        <p class="mt-3 text-xs leading-6 text-slate-500">Continue to shipping, address selection, and payment review.</p>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <x-ui.status-badge type="cart" value="secure_enterprise_checkout" label="Secure enterprise checkout" />
                            <x-ui.status-badge type="cart" value="gst_ready_invoices" label="GST-ready invoices" />
                            <x-ui.status-badge type="cart" value="cold_chain_dispatch_support" label="Cold-chain dispatch support" />
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
                                <p class="mt-1 text-sm leading-6 text-slate-500">Contact our bioscience support team for procurement assistance and delivery scheduling.</p>
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
                const cartList       = document.getElementById('cartItemsList');
                const emptyState     = document.getElementById('cartEmptyState');
                const summaryItems   = document.getElementById('cartSummaryItems');
                const subtotalEl     = document.getElementById('cartSummarySubtotal');
                const taxEl          = document.getElementById('cartSummaryTax');
                const totalEl        = document.getElementById('cartSummaryTotal');
                const itemCount      = document.getElementById('cartItemCount');
                const checkoutButton = document.getElementById('cartCheckoutButton');

                /* -- Free Shipping Progress -- */
                const FREE_THRESHOLD       = {{ $freeShippingThreshold }};
                const progressBar          = document.getElementById('shippingProgressBar');
                const progressSegments     = progressBar ? Array.from(progressBar.querySelectorAll('[data-progress-segment]')) : [];
                const progressMsg          = document.getElementById('shippingProgressMsg');
                const progressBadge        = document.getElementById('shippingProgressBadge');

                /* -- Saved For Later -- */
                const savedSection         = document.getElementById('savedForLaterSection');
                const savedList            = document.getElementById('savedForLaterList');
                const savedItemCount       = document.getElementById('savedItemCount');
                const savedForLaterTeaser  = document.getElementById('savedForLaterTeaser');
                const savedCountInEmpty    = document.getElementById('savedCountInEmpty');
                const SAVED_KEY            = 'biogenix_saved_later';

                function loadSaved() {
                    try { return JSON.parse(localStorage.getItem(SAVED_KEY) || '[]'); } catch (e) { return []; }
                }
                function saveSaved(items) {
                    localStorage.setItem(SAVED_KEY, JSON.stringify(items));
                }

                if (!window.CartStore || !cartList || !summaryItems) return;

                /* -- formatInr -- */
                const formatInr = function (value) {
                    const n = Number(value);
                    if (!Number.isFinite(n)) return 'Rs. 0.00';
                    return 'Rs. ' + n.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                };

                /* -- parseVariant -- */
                const parseVariant = function (value) {
                    const s = String(value || '').trim();
                    return s === '' ? null : Number(s);
                };

                /* -- Update shipping progress bar -- */
                function updateShippingProgress(subtotal) {
                    if (!progressBar || !progressMsg || !progressBadge) return;
                    const pct     = Math.min(100, (subtotal / FREE_THRESHOLD) * 100);
                    const remaining = Math.max(0, FREE_THRESHOLD - subtotal);
                    progressBar.setAttribute('aria-valuenow', Math.round(pct));

                    const activeSegmentCount = Math.max(0, Math.ceil((pct / 100) * progressSegments.length));
                    const unlocked = remaining <= 0;

                    progressSegments.forEach(function (segment, index) {
                        const active = index < activeSegmentCount;
                        segment.classList.toggle('bg-primary-600', active && unlocked);
                        segment.classList.toggle('bg-primary-500', active && !unlocked);
                        segment.classList.toggle('bg-emerald-200', !active);
                    });

                    if (unlocked) {
                        progressMsg.textContent = 'You\'ve unlocked FREE same-day delivery!';
                        progressBadge.textContent = 'FREE Delivery';
                        progressBadge.className = 'inline-flex w-fit items-center rounded-full bg-primary-600 px-3 py-1 text-xs font-bold text-white';
                    } else {
                        const fmt = remaining.toLocaleString('en-IN', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
                        progressMsg.textContent = 'Add Rs. ' + fmt + ' more for FREE delivery';
                        progressBadge.textContent = Math.round(pct) + '% there';
                        progressBadge.className = 'inline-flex w-fit items-center rounded-full bg-slate-700 px-3 py-1 text-xs font-bold text-white';
                    }
                }

                /* -- Render Line Card (with Save for Later button) -- */
                const getLineSubtotal = function (item) {
                    // Step 1: prefer the backend subtotal when the current cart line came from the authenticated cart.
                    if (Number.isFinite(Number(item.lineSubtotal))) {
                        return Number(item.lineSubtotal);
                    }

                    // Step 2: keep the existing guest subtotal fallback for pre-login browsing.
                    return Number(item.unitPrice || 0) * Math.max(1, Number(item.quantity || 1));
                };

                const getLineTax = function (item) {
                    // Step 1: prefer the backend tax when the current cart line came from the authenticated cart.
                    if (Number.isFinite(Number(item.taxAmount))) {
                        return Number(item.taxAmount);
                    }

                    // Step 2: keep the existing guest GST fallback for pre-login browsing.
                    return getLineSubtotal(item) * 0.18;
                };

                const getLineTotal = function (item) {
                    // Step 1: prefer the backend total when the current cart line came from the authenticated cart.
                    if (Number.isFinite(Number(item.lineTotal))) {
                        return Number(item.lineTotal);
                    }

                    // Step 2: keep the existing guest total fallback for pre-login browsing.
                    return getLineSubtotal(item) + getLineTax(item);
                };

                const renderLineCard = function (item) {
                    const quantity  = Math.max(1, Number(item.quantity || 1));
                    const unitPrice = Number(item.unitPrice || 0);
                    const subtotal  = getLineSubtotal(item);
                    const image     = String(item.image || 'https://via.placeholder.com/220x220?text=Biogenix');
                    const model     = String(item.model || 'N/A');
                    const name      = String(item.name || 'Product');
                    const productId = Number(item.productId || 0);
                    const variantId = item.variantId === null || item.variantId === undefined ? '' : String(item.variantId);

                    return `
                        <article class="rounded-[24px] border border-slate-200 bg-white p-4 shadow-sm sm:rounded-[28px] sm:p-5">
                            <div class="flex flex-col gap-4 sm:gap-5 lg:flex-row lg:items-start">
                                <div class="h-24 w-24 shrink-0 overflow-hidden rounded-3xl bg-slate-100 sm:h-28 sm:w-28">
                                    <img src="${image}" alt="${name}" class="h-full w-full object-cover">
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                                        <div class="min-w-0">
                                            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">Biogenix</p>
                                            <h3 class="mt-1 break-words text-lg font-semibold leading-tight text-slate-950 sm:text-xl">${name}</h3>
                                            <p class="mt-1.5 text-sm font-medium text-slate-500">Model No: ${model}</p>
                                            <div class="mt-3 flex flex-wrap gap-2">
                                                <span class="inline-flex items-center rounded-full border border-primary-100 bg-primary-50 px-3 py-1 text-xs font-semibold text-primary-700">Dispatch 24-48h</span>
                                                <span class="inline-flex items-center rounded-full border border-primary-200 bg-primary-50 px-3 py-1 text-xs font-semibold text-primary-600">Procurement ready</span>
                                            </div>
                                        </div>
                                        <div class="text-left sm:text-right">
                                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Item Subtotal</p>
                                            <p class="mt-2 text-xl font-bold leading-none text-primary-700 sm:text-2xl">${formatInr(subtotal)}</p>
                                            <p class="mt-2 text-sm text-slate-500">Unit price ${formatInr(unitPrice)}</p>
                                        </div>
                                    </div>

                                    <div class="mt-5 flex flex-col gap-4 border-t border-slate-100 pt-5 sm:flex-row sm:items-center sm:justify-between">
                                        <div class="inline-flex w-full items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 p-1 sm:w-auto sm:justify-normal">
                                            <button type="button"
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-xl text-lg font-semibold text-slate-700 transition hover:bg-white hover:text-primary-700"
                                                data-quantity-button data-direction="-1"
                                                data-product-id="${productId}" data-variant-id="${variantId}"
                                                aria-label="Decrease quantity">-</button>
                                            <span class="inline-flex min-w-[44px] items-center justify-center px-3 text-base font-semibold text-slate-900">${quantity}</span>
                                            <button type="button"
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-xl text-lg font-semibold text-slate-700 transition hover:bg-white hover:text-primary-700"
                                                data-quantity-button data-direction="1"
                                                data-product-id="${productId}" data-variant-id="${variantId}"
                                                aria-label="Increase quantity">+</button>
                                        </div>

                                        <div class="flex w-full flex-wrap items-center gap-2.5 sm:w-auto">
                                            {{-- Save for Later button --}}
                                            <button type="button"
                                                class="inline-flex h-10 items-center gap-1.5 justify-center rounded-xl border border-amber-300 bg-secondary-50 px-3.5 text-sm font-semibold text-secondary-700 transition hover:bg-amber-100"
                                                data-save-later-button
                                                data-product-id="${productId}" data-variant-id="${variantId}">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z"/>
                                                </svg>
                                                Save Later
                                            </button>
                                            <button type="button"
                                                class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                                                data-remove-button
                                                data-product-id="${productId}" data-variant-id="${variantId}">
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    `;
                };

                /* -- Render Saved Item Card -- */
                const renderSavedCard = function (item) {
                    const image     = String(item.image || 'https://via.placeholder.com/96x96?text=Bio');
                    const name      = String(item.name || 'Product');
                    const model     = String(item.model || 'N/A');
                    const unitPrice = Number(item.unitPrice || 0);
                    const productId = Number(item.productId || 0);
                    const variantId = item.variantId === null || item.variantId === undefined ? '' : String(item.variantId);

                    return `
                        <div class="flex items-center gap-4 rounded-2xl border border-slate-200 bg-slate-50 p-3">
                            <div class="h-14 w-14 shrink-0 overflow-hidden rounded-2xl bg-slate-100">
                                <img src="${image}" alt="${name}" class="h-full w-full object-cover">
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-900">${name}</p>
                                <p class="mt-0.5 text-xs text-slate-500">Model: ${model} · ${formatInr(unitPrice)}</p>
                            </div>
                            <div class="flex shrink-0 gap-2">
                                <button type="button"
                                    class="inline-flex h-9 items-center justify-center rounded-xl bg-primary-600 px-3.5 text-xs font-semibold text-white transition hover:bg-primary-700"
                                    data-move-to-cart-button
                                    data-product-id="${productId}" data-variant-id="${variantId}">
                                    Add to Cart
                                </button>
                                <button type="button"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-300 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-rose-600"
                                    data-remove-saved-button
                                    data-product-id="${productId}" data-variant-id="${variantId}"
                                    aria-label="Remove from saved">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    `;
                };

                /* -- Render Summary Row -- */
                const renderSummaryRow = function (item) {
                    const quantity = Math.max(1, Number(item.quantity || 1));
                    const total    = getLineTotal(item);
                    const image    = String(item.image || 'https://via.placeholder.com/96x96?text=Bio');
                    const name     = String(item.name || 'Product');
                    return `
                        <div class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-3 py-3">
                            <div class="h-14 w-14 shrink-0 overflow-hidden rounded-2xl bg-slate-100">
                                <img src="${image}" alt="${name}" class="h-full w-full object-cover">
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-900">${name}</p>
                                <p class="mt-1 text-xs text-slate-500">Qty: ${quantity}</p>
                            </div>
                            <span class="text-sm font-semibold text-primary-700">${formatInr(total)}</span>
                        </div>
                    `;
                };

                /* -- Render Saved Section -- */
                function renderSaved() {
                    const saved = loadSaved();
                    if (!savedList || !savedSection || !savedItemCount) return;

                    if (!saved.length) {
                        savedSection.classList.add('hidden');
                        if (savedForLaterTeaser) savedForLaterTeaser.classList.add('hidden');
                        return;
                    }

                    savedSection.classList.remove('hidden');
                    savedList.innerHTML = '';
                    saved.forEach(function (item) {
                        savedList.insertAdjacentHTML('beforeend', renderSavedCard(item));
                    });
                    savedItemCount.textContent = saved.length + (saved.length === 1 ? ' item' : ' items');

                    /* Show teaser in empty cart state */
                    if (savedForLaterTeaser && savedCountInEmpty) {
                        savedCountInEmpty.textContent = saved.length;
                        savedForLaterTeaser.classList.remove('hidden');
                    }

                    /* Bind move-to-cart & remove-saved */
                    savedList.querySelectorAll('[data-move-to-cart-button]').forEach(function (btn) {
                        btn.addEventListener('click', function () {
                            const pid = Number(btn.dataset.productId || 0);
                            const vid = parseVariant(btn.dataset.variantId);
                            const saved = loadSaved();
                            const item  = saved.find(function (x) { return Number(x.productId) === pid && (x.variantId ?? null) === vid; });
                            if (!item) return;
                            window.CartStore.addItem(Object.assign({}, item, { quantity: 1 }));
                            saveSaved(saved.filter(function (x) { return !(Number(x.productId) === pid && (x.variantId ?? null) === vid); }));
                            renderSaved();
                        });
                    });

                    savedList.querySelectorAll('[data-remove-saved-button]').forEach(function (btn) {
                        btn.addEventListener('click', function () {
                            const pid = Number(btn.dataset.productId || 0);
                            const vid = parseVariant(btn.dataset.variantId);
                            saveSaved(loadSaved().filter(function (x) { return !(Number(x.productId) === pid && (x.variantId ?? null) === vid); }));
                            renderSaved();
                        });
                    });
                }

                /* -- Bind Cart Actions -- */
                const bindActions = function () {
                    /* Quantity +/- */
                    document.querySelectorAll('[data-quantity-button]').forEach(function (btn) {
                        btn.addEventListener('click', function () {
                            const pid  = Number(btn.dataset.productId || 0);
                            const vid  = parseVariant(btn.dataset.variantId);
                            const dir  = Number(btn.dataset.direction || 0);
                            const item = window.CartStore.getItems().find(function (x) {
                                return Number(x.productId || 0) === pid && (x.variantId ?? null) === vid;
                            });
                            if (!item) return;
                            const nextQty = Number(item.quantity || 1) + dir;
                            if (nextQty <= 0) {
                                window.CartStore.removeItem(pid, vid);
                            } else {
                                window.CartStore.updateQuantity(pid, vid, nextQty);
                            }
                        });
                    });

                    /* Remove */
                    document.querySelectorAll('[data-remove-button]').forEach(function (btn) {
                        btn.addEventListener('click', function () {
                            window.CartStore.removeItem(Number(btn.dataset.productId || 0), parseVariant(btn.dataset.variantId));
                        });
                    });

                    /* Save for Later */
                    document.querySelectorAll('[data-save-later-button]').forEach(function (btn) {
                        btn.addEventListener('click', function () {
                            const pid  = Number(btn.dataset.productId || 0);
                            const vid  = parseVariant(btn.dataset.variantId);
                            const item = window.CartStore.getItems().find(function (x) {
                                return Number(x.productId || 0) === pid && (x.variantId ?? null) === vid;
                            });
                            if (!item) return;
                            const saved = loadSaved();
                            const already = saved.some(function (x) { return Number(x.productId) === pid && (x.variantId ?? null) === vid; });
                            if (!already) saveSaved(saved.concat([Object.assign({}, item)]));
                            window.CartStore.removeItem(pid, vid);
                            renderSaved();
                        });
                    });
                };

                /* -- Main render -- */
                const render = function () {
                    const items      = window.CartStore.getItems();
                    const totalUnits = items.reduce(function (s, x) { return s + Math.max(1, Number(x.quantity || 1)); }, 0);

                    cartList.innerHTML     = '';
                    summaryItems.innerHTML = '';

                    if (!items.length) {
                        emptyState.classList.remove('hidden');
                        emptyState.classList.add('flex');
                        if (itemCount) itemCount.textContent = '0 items';
                        if (subtotalEl) subtotalEl.innerHTML = 'Rs. 0.00';
                        if (taxEl)      taxEl.innerHTML      = 'Rs. 0.00';
                        if (totalEl)    totalEl.innerHTML    = 'Rs. 0.00';
                        if (checkoutButton) checkoutButton.classList.add('opacity-70');
                        updateShippingProgress(0);
                        renderSaved();
                        return;
                    }

                    emptyState.classList.add('hidden');
                    emptyState.classList.remove('flex');
                    if (checkoutButton) checkoutButton.classList.remove('opacity-70');

                    var subtotal = 0;
                    var tax = 0;
                    var total = 0;
                    items.forEach(function (item) {
                        subtotal += getLineSubtotal(item);
                        tax += getLineTax(item);
                        total += getLineTotal(item);
                        cartList.insertAdjacentHTML('beforeend', renderLineCard(item));
                        summaryItems.insertAdjacentHTML('beforeend', renderSummaryRow(item));
                    });

                    if (itemCount)  itemCount.textContent      = totalUnits + (totalUnits === 1 ? ' item' : ' items');
                    if (subtotalEl) subtotalEl.innerHTML        = formatInr(subtotal);
                    if (taxEl)      taxEl.innerHTML             = formatInr(tax);
                    if (totalEl)    totalEl.innerHTML           = formatInr(total);

                    updateShippingProgress(subtotal);
                    bindActions();
                    renderSaved();
                };

                window.CartStore.subscribe(render);
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


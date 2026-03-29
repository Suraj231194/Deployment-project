@extends('layouts.app')

@section('title', 'Product Details')

@section('content')
@php
    use Illuminate\Support\Str;

    $productExists = isset($product) && $product;
@endphp

@if (! $productExists)
    <div class="w-screen bg-primary-50/20 py-8 md:py-10 [margin-left:calc(50%-50vw)] [margin-right:calc(50%-50vw)]">
        <div class="mx-auto w-full px-4 py-6 md:px-6 md:py-8">
            <x-ui.empty-state
                icon="product"
                title="Product unavailable"
                description="This product detail page could not be loaded right now. Return to the catalog and try again."
                :action-href="route('products.index')"
                action-label="Back to Catalog"
                class="mx-auto max-w-2xl rounded-[28px] border border-slate-200 bg-white px-8 py-14 shadow-sm"
            />
        </div>
    </div>
@else
    @php
        $formatInr = function (float|int|null $amount, int $decimals = 2): string {
            if ($amount === null) {
                return 'Request Pricing';
            }

            $negative = $amount < 0;
            $amount = abs((float) $amount);
            $formatted = number_format($amount, $decimals, '.', '');
            [$integerPart, $fractionPart] = array_pad(explode('.', $formatted), 2, '00');

            if (strlen($integerPart) > 3) {
                $lastThree = substr($integerPart, -3);
                $remaining = substr($integerPart, 0, -3);
                $remaining = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $remaining);
                $integerPart = $remaining . ',' . $lastThree;
            }

            return ($negative ? '-' : '') . '<span class="text-base font-medium opacity-60">Rs.</span> ' . $integerPart . ($decimals > 0 ? '.' . $fractionPart : '');
        };

        $productTitle = trim((string) ($product->name ?? 'Product Details'));
        $brandLabel = trim((string) ($product->brand ?? 'Biogenix'));
        $categoryLabel = trim((string) ($product->category_name ?? 'Laboratory Equipment'));
        $applicationLabel = trim((string) ($product->subcategory_name ?? 'Clinical Diagnostics'));
        $modelLabel = trim((string) ($product->visible_variant_sku ?? $product->sku ?? 'N/A'));
        $imageUrl = asset($product->image_path ?: 'upload/icons/logo.jpg');
        $galleryImages = collect([
            ['label' => 'Main View', 'src' => $imageUrl],
            ['label' => 'Pack View', 'src' => asset('upload/products/image1.jpg')],
            ['label' => 'Bench View', 'src' => asset('upload/products/image2.jpg')],
            ['label' => 'Workflow', 'src' => asset('upload/products/image3.jpg')],
        ]);
        $currentPrice = $product->visible_price !== null ? (float) $product->visible_price : null;
        // Step 1: keep the saved base price ready so the detail page can show the real MRP from backend data.
        $basePrice = $product->visible_base_price !== null ? (float) $product->visible_base_price : $currentPrice;
        $savedDiscountAmount = round((float) ($product->visible_discount_amount ?? 0), 2);
        $showMrpStrikethrough = $basePrice !== null && $currentPrice !== null && $basePrice > $currentPrice;
        $reviewCount = max(28, ((int) ($product->id ?? 1) * 9) + 24);
        $ratingValue = number_format(4.7 + ((((int) ($product->id ?? 1)) % 3) * 0.1), 1);
        $primaryBadge = filled($applicationLabel) ? $applicationLabel : 'Premium Series';
        $secondaryBadge = ((int) ($product->id ?? 1) % 2 === 0) ? 'Clinical Ready' : 'Best Seller';
        $stockStatus = trim((string) ($product->stock_status ?? 'Out of Stock'));
        $quoteUrl = route('quotation.create', ['product_id' => $product->id]);
        $cartVariantId = $product->visible_variant_id ?? null;
        $loginUrl = route('login');
        // Step 1: read the business-owned overview directly from the product row for the detail section.
        $productOverview = trim((string) ($product->product_overview ?? ''));
        $previousUrl = url()->previous();
        $currentUrl = url()->current();
        $currentHost = parse_url(url()->to('/'), PHP_URL_HOST);
        $previousHost = $previousUrl ? parse_url($previousUrl, PHP_URL_HOST) : null;
        $backUrl = filled($previousUrl) && $previousUrl !== $currentUrl && (! $previousHost || $previousHost === $currentHost)
            ? $previousUrl
            : route('products.index');
        $shippingHighlights = ['Ships in 24-48 hours', 'Validated packaging', 'Priority support available'];
        $trustSignals = ['Secure enterprise checkout', 'GST-ready commercial invoice', 'Cold-chain dispatch support'];
        // Step 2: prepare the saved visible-variant technical specifications for the detail section.
        $technicalSpecificationRows = collect($product->technical_specification_json ?? [])
            ->map(fn ($value, $label) => [
                'label' => trim((string) $label),
                'value' => trim((string) $value),
            ])
            ->filter(fn (array $row) => $row['label'] !== '' && $row['value'] !== '')
            ->values();
        // Step 3: map backend technical resource rows into the detail-page card data used by the download section.
        $technicalResources = collect($product->technical_resources ?? [])
            ->map(function ($resource) use ($product) {
                $resourceType = Str::lower(trim((string) ($resource->resource_type ?? 'document')));

                $resourcePresentation = match ($resourceType) {
                    'certificate_of_analysis' => ['meta' => 'Batch-linked quality document', 'icon' => 'clipboard'],
                    'safety_data_sheet' => ['meta' => 'Handling and compliance reference', 'icon' => 'shield'],
                    'user_manual' => ['meta' => 'Installation and usage guide', 'icon' => 'book'],
                    'maintenance_schedule' => ['meta' => 'Standard care checklist', 'icon' => 'calendar'],
                    default => ['meta' => 'Technical download for this product', 'icon' => 'book'],
                };

                // Step 4: keep the UI ready-to-render by attaching one standard download route per saved document.
                return [
                    'id' => (int) ($resource->id ?? 0),
                    'title' => trim((string) ($resource->title ?? 'Technical Document')),
                    'meta' => trim((string) ($resource->description ?? '')) ?: $resourcePresentation['meta'],
                    'icon' => $resourcePresentation['icon'],
                    'href' => route('products.technical-resources.download', [
                        'productId' => $product->id,
                        'resourceId' => $resource->id,
                    ]),
                ];
            })
            ->filter(fn (array $resource) => $resource['id'] > 0 && $resource['title'] !== '')
            ->values();
        $primaryTechnicalResource = $technicalResources->first();
        // Step 3: use the live database-driven pricing ladder instead of hardcoded bulk pricing rows.
        $bulkTierRows = collect($product->bulk_price_tiers ?? [])->values();
        if ($bulkTierRows->isEmpty()) {
            $bulkTierRows = collect([
                ['label' => 'Standard Price', 'discount' => 'Current Price', 'price' => $currentPrice, 'min' => 1, 'max' => null, 'discount_value' => 0],
            ]);
        }
        $selectedTierRow = $bulkTierRows->first();
        $bestBulkTier = $bulkTierRows->sortByDesc('discount_value')->first();
        $relatedProducts = collect($related_products ?? [])->filter();
        $compactCardClass = 'rounded-[var(--ui-radius-card)] border border-slate-200/80 bg-white/95 p-4 shadow-[var(--ui-shadow-card)] backdrop-blur md:p-5 glass-card';
        $sectionCardClass = 'rounded-[var(--ui-radius-card)] border border-slate-200/80 bg-white/95 p-5 shadow-[var(--ui-shadow-card)] backdrop-blur md:p-6 glass-card';
        $purchaseCardClass = 'rounded-[var(--ui-radius-card)] border border-primary-100/70 bg-gradient-to-br from-white via-primary-50/65 to-white p-5 shadow-[var(--ui-shadow-card)] backdrop-blur md:p-6 xl:sticky xl:top-6 glass-card';
        $featurePanelClass = 'mt-3 rounded-3xl border border-primary-100/70 bg-primary-50/70 p-4 md:p-5';
        $iconTilePrimaryClass = 'inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-primary-50 text-primary-700';
        $primaryButtonClass = 'inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-2xl bg-primary-600 px-4 text-sm font-semibold text-white shadow-lg shadow-primary-600/20 transition hover:-translate-y-px hover:bg-primary-700';
        $secondaryButtonClass = 'inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-2xl border border-slate-300 bg-white/95 px-4 text-sm font-semibold text-slate-700 shadow-[var(--ui-shadow-soft)] transition hover:-translate-y-px hover:bg-slate-50';
        $qtyPickerClass = 'inline-flex h-12 w-full items-center justify-between rounded-3xl border border-slate-200/80 bg-white/90 p-1 shadow-[var(--ui-shadow-soft)]';
        $qtyButtonClass = 'inline-flex h-8 w-8 items-center justify-center rounded-full border border-primary-100 bg-white text-lg font-semibold text-primary-700 transition hover:bg-primary-50 hover:text-primary-800';
        $estimateClass = 'rounded-3xl border border-slate-200/80 bg-slate-50/85 px-4 py-3';
        $actionsClass = 'grid gap-3 sm:grid-cols-2';
        $inlineChipClass = 'inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600';
        $sectionHeadingClass = 'text-xl font-semibold text-slate-950';

        // Step 1: extract Min, Max, and Lot Size from the product with standard fallback logic.
        $minQuantity = max(1, (int) ($product->visible_min_order_quantity ?? 1));
        $maxQuantity = ($product->visible_max_order_quantity ?? null) === null
            ? null
            : max($minQuantity, (int) $product->visible_max_order_quantity);
        $lotSize = max(1, (int) ($product->visible_lot_size ?? 1));

        // Step 2: read the initial quantity from the query parameter, ensuring it respects the Min/Max/Lot rules.
        $requestedQuantity = (int) request('quantity', $minQuantity);
        if ($requestedQuantity < $minQuantity) {
            $requestedQuantity = $minQuantity;
        } elseif ($maxQuantity !== null && $requestedQuantity > $maxQuantity) {
            $requestedQuantity = $maxQuantity;
        }

        // Adjust requested quantity to align with lot size if needed
        if ($lotSize > 1 && ($requestedQuantity - $minQuantity) % $lotSize !== 0) {
            $diff = ($requestedQuantity - $minQuantity) % $lotSize;
            $requestedQuantity = $requestedQuantity - $diff;
        }
    @endphp

    <div class="w-screen bg-gradient-to-b from-white via-primary-50/20 to-white py-0 md:mt-0 md:py-0 [margin-left:calc(50%-50vw)] [margin-right:calc(50%-50vw)]">
        <div id="uiToastHost" class="pointer-events-none fixed inset-x-0 bottom-6 z-[95] flex flex-col items-center gap-3 px-4" aria-live="polite" aria-atomic="true"></div>
        <div class="mx-auto w-full px-4 pt-1 pb-8 md:px-6 md:pt-2 md:pb-10">
            <a href="{{ $backUrl }}" class="mb-4 inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200/80 bg-white/95 text-slate-700 no-underline shadow-[var(--ui-shadow-soft)] transition duration-200 hover:-translate-y-px hover:border-primary-100 hover:bg-primary-50 hover:text-primary-700">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m15 18-6-6 6-6"></path>
                </svg>
            </a>

            <!-- <div class="flex flex-wrap items-center gap-2 text-sm font-medium text-slate-400">
                <a href="{{ route('home') }}" class="text-inherit no-underline hover:text-slate-700">Home</a>
                <span>/</span>
                <a href="{{ route('products.index') }}" class="text-inherit no-underline hover:text-slate-700">Catalog</a>
                <span>/</span>
                <span>{{ $categoryLabel }}</span>
                <span>/</span>
                <span>{{ $applicationLabel }}</span>
                <span>/</span>
                <span class="text-slate-700">{{ $productTitle }}</span>
            </div> -->

            <section class="mt-4 grid gap-5 xl:grid-cols-[minmax(0,0.92fr)_minmax(0,1.08fr)] xl:items-start">
                <div class="space-y-3 self-start">
                    <div class="{{ $compactCardClass }}">
                        <div class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-white via-primary-50/60 to-slate-50">
                            <img id="catalogProductMainImage" src="{{ $galleryImages->first()['src'] }}" alt="{{ $productTitle }}" data-gallery-images='@json($galleryImages->pluck('src')->values()->all())' class="h-80 w-full cursor-zoom-in object-cover transition duration-500 group-hover:scale-[1.04] sm:h-96 xl:h-[32rem]" loading="lazy" decoding="async">
                            
                            {{-- Gallery Navigation Arrows --}}
                            @if ($galleryImages->count() > 1)
                                <button id="productPrevImage" type="button" aria-label="Previous image"
                                    class="absolute left-4 top-1/2 z-10 -translate-y-1/2 inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/20 bg-white/10 text-white backdrop-blur-md shadow-xl transition hover:-translate-y-[calc(50%+2px)] hover:bg-white/20">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6" />
                                    </svg>
                                </button>
                                <button id="productNextImage" type="button" aria-label="Next image"
                                    class="absolute right-4 top-1/2 z-10 -translate-y-1/2 inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/20 bg-white/10 text-white backdrop-blur-md shadow-xl transition hover:-translate-y-[calc(50%+2px)] hover:bg-white/20">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6" />
                                    </svg>
                                </button>
                            @endif

                            <button id="productImageZoomBtn" type="button" class="absolute right-4 top-4 inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/80 bg-white/92 text-slate-600 shadow-sm transition hover:-translate-y-0.5 hover:text-primary-700" aria-label="Zoom image">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="7"></circle>
                                    <path d="m20 20-3.5-3.5"></path>
                                    <path d="M11 8v6"></path>
                                    <path d="M8 11h6"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="space-y-4 self-start">
                    <div class="space-y-3.5">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div class="flex flex-wrap items-center gap-2">
                                <x-ui.status-badge type="product" :value="$primaryBadge" :label="$primaryBadge" />
                                <x-ui.status-badge type="product" :value="$secondaryBadge" :label="$secondaryBadge" />
                            </div>
                            {{-- ══ Share + Wishlist Buttons ══ --}}
                            <div class="flex items-center gap-2">
                                  
                                {{-- Share --}}
                                <div class="relative" id="shareParent">
                                    <button type="button" id="shareToggleBtn" class="inline-flex h-10 items-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm font-semibold text-slate-600 transition hover:border-primary-200 hover:bg-primary-50 hover:text-primary-700" aria-label="Share product">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/>
                                            <path d="m8.59 13.51 6.83 3.98M15.41 6.51l-6.82 3.98"/>
                                        </svg>
                                        <span class="hidden sm:inline">Share</span>
                                    </button>
                                    <div id="shareDropdown" class="invisible absolute right-0 top-[calc(100%+6px)] z-50 w-56 rounded-2xl border border-slate-200 bg-white p-2 opacity-0 shadow-xl transition-all duration-200">
                                        <button type="button" data-share="copy"    class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50"><svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>Copy Link</button>
                                        <button type="button" data-share="whatsapp" class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-primary-50 hover:text-primary-600"><svg class="h-4 w-4 text-primary-600" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12.05 21.75h-.004a9.72 9.72 0 0 1-4.99-1.37l-.358-.212-3.713.974.992-3.623-.233-.37A9.86 9.86 0 0 1 2.25 12.05C2.25 6.65 6.65 2.25 12.05 2.25S21.85 6.65 21.85 12.05 17.45 21.75 12.05 21.75zM12.05.75C5.82.75.75 5.82.75 12.05a11.3 11.3 0 0 0 1.535 5.688L.75 23.25l5.666-1.487A11.3 11.3 0 0 0 12.05 23.25c6.23 0 11.3-5.07 11.3-11.3S18.28.75 12.05.75z"/></svg>WhatsApp</button>
                                        <button type="button" data-share="email"    class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-primary-50 hover:text-primary-600"><svg class="h-4 w-4 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>Email</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <h1 class="font-display text-3xl font-bold tracking-tight text-slate-950 md:text-4xl leading-tight">{{ $productTitle }}</h1>
                            <div class="space-y-1 text-sm font-medium text-slate-500">
                                <p>Model No: {{ $modelLabel }} | Professional Grade Biotech System</p>
                            </div>
                            <p class="text-sm leading-7 text-slate-600 md:text-base">{{ $product->description ?: 'Professional scientific product engineered for institutional procurement and precision workflows.' }}</p>
                        </div>

                        <div class="flex flex-col gap-2.5">
                            @php
                                $filteredHighlights = collect($shippingHighlights)
                                    ->filter(fn($h) => trim($h) !== 'Priority support available')
                                    ->values();
                            @endphp
                            
                            <div class="flex flex-wrap items-center gap-2">
                                <x-ui.status-badge type="cart" value="Priority support available" label="Priority support available" dot />
                                <x-ui.status-badge type="product" :value="$stockStatus" :label="$stockStatus" dot />
                                @if ($filteredHighlights->isNotEmpty())
                                    <x-ui.status-badge type="cart" :value="$filteredHighlights[0]" :label="$filteredHighlights[0]" dot />
                                @endif
                            </div>
                            
                            @if ($filteredHighlights->count() > 1)
                            <div class="flex flex-wrap items-center gap-2">
                                @for ($i = 1; $i < $filteredHighlights->count(); $i++)
                                    <x-ui.status-badge type="cart" :value="$filteredHighlights[$i]" :label="$filteredHighlights[$i]" dot />
                                @endfor
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="{{ $purchaseCardClass }}">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Market Retail Price (MRP)</p>
                                <div class="mt-3 flex flex-wrap items-baseline gap-3">
                                    <span class="text-xl font-extrabold tracking-tight text-primary-700">{!! $formatInr($currentPrice) !!}</span>
                                    @if ($showMrpStrikethrough)
                                        <span class="text-sm font-medium text-slate-400 line-through">{!! $formatInr($basePrice) !!}</span>
                                    @endif
                                </div>
                                <p class="mt-2 text-sm font-medium text-slate-500">
                                    @if ($savedDiscountAmount > 0)
                                        You save {!! $formatInr($savedDiscountAmount) !!} on the current visible price.
                                    @else
                                        Current visible price is ready for checkout and quotation.
                                    @endif
                                </p>
                            </div>
                        </div>
                        

                        <div class="mt-4 space-y-3">
                            <div class="max-w-48 space-y-3">
                                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Quantity</p>
                                <div class="{{ $qtyPickerClass }}"
                                    data-detail-quantity-control
                                    data-min-quantity="{{ $minQuantity }}"
                                    data-max-quantity="{{ $maxQuantity ?? '' }}"
                                    data-lot-size="{{ $lotSize }}"
                                >
                                    <button type="button" class="{{ $qtyButtonClass }}" data-qty-button data-direction="-1">-</button>
                                    <span id="catalogQuantityValue" class="text-base font-medium text-slate-900 transition duration-150">{{ $requestedQuantity }}</span>
                                    <button type="button" class="{{ $qtyButtonClass }}" data-qty-button data-direction="1">+</button>
                                </div>
                                <div class="mt-2.5 flex items-center gap-3">
                                    <p class="whitespace-nowrap text-[11.5px] font-medium text-slate-400">
                                        Min: <span class="text-slate-600">{{ $minQuantity }}</span>
                                        @if ($maxQuantity !== null) &bull; Max: <span class="text-slate-600">{{ number_format($maxQuantity) }}</span> @endif
                                        @if ($lotSize > 1) &bull; Lot: <span class="text-slate-600">{{ $lotSize }}</span> @endif
                                    </p>
                                </div>
                            </div>

                                

                            <div class="{{ $actionsClass }}">
                                {{-- Step 1: keep the core storefront purchase actions together inside the pricing card. --}}
                                @guest
                                    <a href="{{ $loginUrl }}" class="{{ $primaryButtonClass }} js-add-to-cart hover-lift" data-product-id="{{ $product->id }}" data-variant-id="{{ $cartVariantId }}" data-product-name="{{ e($productTitle) }}">
                                        <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="9" cy="20" r="1"></circle>
                                            <circle cx="18" cy="20" r="1"></circle>
                                            <path d="M3 4h2l2.4 10.5a1 1 0 0 0 1 .8h8.9a1 1 0 0 0 1-.8L21 7H7"></path>
                                        </svg>
                                        <span>Add to Cart</span>
                                    </a>
                                @else
                                    <button type="button" class="{{ $primaryButtonClass }} js-add-to-cart hover-lift" data-product-id="{{ $product->id }}" data-variant-id="{{ $cartVariantId }}" data-product-name="{{ e($productTitle) }}">
                                        <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="9" cy="20" r="1"></circle>
                                            <circle cx="18" cy="20" r="1"></circle>
                                            <path d="M3 4h2l2.4 10.5a1 1 0 0 0 1 .8h8.9a1 1 0 0 0 1-.8L21 7H7"></path>
                                        </svg>
                                        <span>Add to Cart</span>
                                    </button>
                                @endguest

                                {{-- Step 2: let the buyer move straight into checkout while keeping the existing cart items together. --}}
                                <form method="POST" action="{{ auth()->check() ? route('checkout.buy-now') : route('guest.checkout.buy-now') }}">
                                    @csrf

                                    {{-- Step 3: submit the selected product and current quantity so checkout includes this choice immediately. --}}
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="product_variant_id" value="{{ $cartVariantId }}">
                                    <input type="hidden" name="quantity" id="productDetailBuyNowQuantity" value="{{ $requestedQuantity }}">

                                    {{-- Step 4: keep the immediate checkout action as one standard controller-backed submit. --}}
                                    <button type="submit" class="inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-2xl bg-secondary-600 px-4 text-sm font-bold uppercase tracking-wide text-primary-950 shadow-lg shadow-secondary-600/20 transition hover:-translate-y-px hover:bg-secondary-500 hover-lift">
                                        <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M5 12h14"></path>
                                            <path d="m12 5 7 7-7 7"></path>
                                        </svg>
                                        <span>Buy Now</span>
                                    </button>
                                </form>
                            </div>
                        </div>

                               

                        {{-- ══ Pincode / Delivery Check ══ --}}
                        <!-- <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="mb-2 flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <svg class="h-3.5 w-3.5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                                Check Delivery Availability
                            </p>
                            <div class="flex gap-2">
                                <input
                                    id="pincodeInput"
                                    type="text"
                                    inputmode="numeric"
                                    maxlength="6"
                                    placeholder="Enter 6-digit pincode"
                                    class="min-w-0 flex-1 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-medium text-slate-900 placeholder-slate-400 outline-none transition focus:border-primary-400 focus:ring-2 focus:ring-primary-600/15"
                                >
                                <button type="button" id="pincodeCheckBtn" class="inline-flex h-10 shrink-0 items-center justify-center rounded-xl bg-primary-600 px-4 text-sm font-semibold text-white transition hover:bg-primary-700">
                                    Check
                                </button>
                            </div>
                            <p id="pincodeResult" class="mt-2 min-h-[1.1rem] text-xs font-medium text-slate-500"></p>
                        </div> -->

                       
                    </div>

                </div>
            </section>

            {{-- ═══════════════════════════════════════════════════════ --}}
            {{-- STICKY TAB NAVIGATION BAR --}}
            {{-- ═══════════════════════════════════════════════════════ --}}


            <section id="sectionBulkPricing" class="mt-5 scroll-mt-16">
                <div class="{{ $sectionCardClass }}">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h2 class="{{ $sectionHeadingClass }}">Bulk Tier Pricing</h2>
                        <x-ui.status-badge id="bulkTierHint" type="cart" value="best_value_tier" :label="$bestBulkTier ? 'Best value on ' . $bestBulkTier['label'] : 'Current price available'" />
                    </div>
                    <div class="mt-5 overflow-hidden rounded-3xl border border-slate-200">
                        <div class="grid grid-cols-3 bg-slate-50 px-5 py-3 table-label">
                            <span>Quantity</span>
                            <span>Discount</span>
                            <span class="text-right">Price/Unit</span>
                        </div>
                        @foreach ($bulkTierRows as $tier)
                            <div
                                class="{{ $loop->last ? 'bg-primary-50/70' : ($loop->odd ? 'bg-white' : 'bg-slate-50/60') }} grid cursor-pointer grid-cols-3 border-t border-slate-200 px-5 py-4 text-sm text-slate-700 transition"
                                data-bulk-tier-row
                                data-min="{{ (int) ($tier['min'] ?? 1) }}"
                                @if ($tier['max'] !== null) data-max="{{ (int) $tier['max'] }}" @endif
                                data-price="{{ $tier['price'] !== null ? (float) $tier['price'] : '' }}"
                                data-label="{{ e((string) $tier['label']) }}"
                                data-discount="{{ e((string) $tier['discount']) }}"
                            >
                                <span class="{{ $loop->last ? 'font-semibold text-slate-900' : '' }}">{{ $tier['label'] }}</span>
                                <span class="{{ $loop->last ? 'font-semibold text-primary-600' : 'text-slate-600' }}">{{ $tier['discount'] }}</span>
                                <span class="text-right font-semibold text-slate-900">{!! $formatInr($tier['price']) !!}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section id="sectionOverview" class="mt-5 scroll-mt-16 grid gap-5 xl:items-start xl:grid-cols-[minmax(0,1fr)_340px]">
                <div class="{{ $sectionCardClass }}">
                    <h2 class="{{ $sectionHeadingClass }}">Product Overview</h2>
                    <div class="mt-5 space-y-5 text-sm leading-7 text-slate-600 md:text-base">
                        {{-- Step 2: render the saved product overview exactly from database content instead of page-level hardcoded copy. --}}
                        <p>{!! nl2br(e($productOverview)) !!}</p>


                    </div>
                </div>

                <div id="sectionResources" class="{{ $sectionCardClass }} scroll-mt-16">
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="{{ $sectionHeadingClass }}">Technical Brochure</h3>
                        <x-ui.status-badge type="product" value="resource_count" :label="count($technicalResources) . ' files'" />
                    </div>
                    <div class="mt-5 space-y-3">
                        @forelse ($technicalResources as $resource)
                            <a href="{{ $resource['href'] }}" data-download-label="{{ e((string) $resource['title']) }}" class="js-download-resource group flex items-center justify-between rounded-3xl border border-slate-200 bg-slate-50 px-4 py-4 no-underline transition duration-200 hover:-translate-y-0.5 hover:border-primary-100 hover:bg-white hover:shadow-md">
                                <div class="flex items-start gap-3">
                                    <span class="{{ $iconTilePrimaryClass }} mt-0.5 bg-white">
                                        @if ($resource['icon'] === 'clipboard')
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="3" width="6" height="4" rx="1"></rect><path d="M9 5H7a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"></path><path d="M9 12h6"></path><path d="M9 16h4"></path></svg>
                                        @elseif ($resource['icon'] === 'shield')
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3 5 6v6c0 5 3.5 8 7 9 3.5-1 7-4 7-9V6l-7-3Z"></path><path d="m9.5 12 1.8 1.8 3.2-3.6"></path></svg>
                                        @elseif ($resource['icon'] === 'book')
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"></path></svg>
                                        @else
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect x="3" y="6" width="18" height="15" rx="2"></rect><path d="M3 10h18"></path></svg>
                                        @endif
                                    </span>
                                    <div>
                                        <p class="text-sm font-medium text-slate-900">{{ $resource['title'] }}</p>
                                        <p class="mt-1 text-sm font-medium text-slate-500">{{ $resource['meta'] }}</p>
                                    </div>
                                </div>
                                <svg class="h-4 w-4 text-slate-400 transition group-hover:text-primary-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v12"></path><path d="m7 10 5 5 5-5"></path><path d="M5 21h14"></path></svg>
                            </a>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-5 text-sm font-medium leading-7 text-slate-500">
                                Technical documents will appear here after the product team uploads them for this item.
                            </div>
                        @endforelse
                    </div>
                </div>

                @if ($technicalSpecificationRows->isNotEmpty())
                    <div id="sectionSpecs" class="{{ $sectionCardClass }} scroll-mt-16">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <h2 class="{{ $sectionHeadingClass }}">Technical Specifications</h2>
                            <x-ui.status-badge type="product" value="validated_configuration" label="Validated configuration" />
                        </div>
                        <div class="mt-6 grid gap-4 sm:grid-cols-2">
                            {{-- Step 3: render the saved visible-variant technical specs directly from the database payload. --}}
                            @foreach ($technicalSpecificationRows as $row)
                                <div class="rounded-3xl border border-slate-200 bg-slate-50 px-5 py-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ $row['label'] }}</p>
                                    <p class="mt-3 text-base font-medium leading-7 text-slate-900">{{ $row['value'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif


            </section>

            <section id="sectionRelated" class="mt-6 scroll-mt-16 pb-2">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <h2 class="{{ $sectionHeadingClass }}">Frequently Bought Together</h2>
                    <div class="flex items-center gap-2">
                        <button type="button" id="relatedPrev" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:bg-slate-50 disabled:opacity-40" aria-label="Previous">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                        </button>
                        <button type="button" id="relatedNext" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:bg-slate-50 disabled:opacity-40" aria-label="Next">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                        </button>
                        <a href="{{ route('products.index') }}" class="text-sm font-semibold text-primary-700 transition hover:text-primary-800">View All</a>
                    </div>
                </div>

                @if ($relatedProducts->isNotEmpty())
                    <div class="relative mt-4 overflow-hidden">
                        <div id="relatedCarouselTrack" class="flex gap-4 overflow-x-auto scroll-smooth pb-2 [-webkit-overflow-scrolling:touch] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                            @foreach ($relatedProducts as $relatedProduct)
                                @php
                                    $relatedImage = asset($relatedProduct->primaryImage?->file_path ?: 'upload/icons/logo.jpg');
                                    $relatedPrice = $relatedProduct->visible_price !== null ? (float) $relatedProduct->visible_price : null;
                                    $relatedReviews = 38 + (((int) ($relatedProduct->id ?? 1)) * 3);
                                @endphp
                                <article class="group w-[280px] shrink-0 overflow-hidden rounded-[var(--ui-radius-card)] border border-slate-200 bg-white shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-xl snap-start">
                                    <div class="overflow-hidden">
                                        <img src="{{ $relatedImage }}" alt="{{ $relatedProduct->name }}" class="h-[200px] w-full object-cover transition duration-300 group-hover:scale-[1.04]" loading="lazy" decoding="async">
                                    </div>
                                    <div class="space-y-3 px-4 pb-5 pt-4">

                                        <h3 class="text-base font-semibold leading-6 text-slate-950">{{ Str::limit((string) ($relatedProduct->name ?? 'Related Product'), 52) }}</h3>
                                        <p class="text-sm leading-6 text-slate-500">{{ $relatedProduct->brand ?? 'Biogenix' }}</p>
                                        <div class="flex items-center justify-between gap-3">
                                            <p class="text-lg font-extrabold tracking-tight text-primary-700">{!! $formatInr($relatedPrice) !!}</p>
                                            <a href="{{ route('products.productDetails', $relatedProduct->id) }}" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">View</a>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="mt-6 rounded-[28px] border border-dashed border-slate-300 bg-white px-6 py-12 text-center shadow-sm">
                        <p class="text-base font-medium text-slate-700">Related products will appear here as order history and category matching data becomes available.</p>
                    </div>
                @endif
            </section>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- STICKY ADD-TO-CART BAR (mobile / scroll-down) --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div id="stickyAddToCartBar" class="fixed inset-x-0 bottom-0 z-50 translate-y-full border-t border-slate-200 bg-white/95 px-4 py-3 shadow-[0_-4px_24px_rgba(26,30,26,0.08)] backdrop-blur transition-transform duration-300 xl:hidden">
        <div class="mx-auto flex max-w-xl items-center gap-3">
            <img src="{{ $imageUrl }}" alt="{{ $productTitle }}" class="h-11 w-11 shrink-0 rounded-xl border border-slate-100 object-cover">
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-semibold text-slate-900">{{ $productTitle }}</p>
                <p class="text-base font-extrabold text-primary-700">{!! $formatInr($currentPrice) !!}</p>
            </div>
            <button type="button" id="stickyAddToCartBtn" class="js-add-to-cart inline-flex h-11 shrink-0 items-center gap-1.5 rounded-xl bg-primary-600 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700" data-product-id="{{ $product->id }}" data-variant-id="{{ $cartVariantId }}" data-product-name="{{ e($productTitle) }}">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/><path d="M3 4h2l2.4 10.5a1 1 0 0 0 1 .8h8.9a1 1 0 0 0 1-.8L21 7H7"/></svg>
                Add to Cart
            </button>
        </div>
    </div>

    <div id="productImageLightbox" class="fixed inset-0 z-[100] flex items-center justify-center p-5 opacity-0 pointer-events-none transition-opacity duration-200" aria-hidden="true">
        <button type="button" class="absolute inset-0 border-0 bg-slate-950/60 backdrop-blur-xl" data-lightbox-close aria-label="Close image preview"></button>
        <div class="relative w-full max-w-[980px] overflow-hidden rounded-[28px] border border-white/20 bg-white/95 shadow-[0_36px_110px_rgba(26,30,26,0.25)]" role="dialog" aria-modal="true" aria-label="Product image preview">
            <div class="flex items-center justify-between gap-3 border-b border-slate-200/90 px-4 py-3">
                <p id="productImageLightboxTitle" class="max-w-[70ch] overflow-hidden text-ellipsis whitespace-nowrap text-sm font-bold text-slate-900">{{ $productTitle }}</p>
                <button type="button" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-[13px] font-bold text-slate-700 transition hover:-translate-y-px hover:border-primary-200 hover:text-primary-700" data-lightbox-close>Close</button>
            </div>
            <img id="productImageLightboxImage" src="" alt="" class="max-h-[80vh] w-full object-contain bg-slate-50" loading="lazy" decoding="async">
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const mainImage = document.getElementById('catalogProductMainImage');
                const imageStage = mainImage ? mainImage.closest('.group') : null;
                const quantityValue = document.getElementById('catalogQuantityValue');
                const toastHost = document.getElementById('uiToastHost');
                const lightbox = document.getElementById('productImageLightbox');
                const lightboxImage = document.getElementById('productImageLightboxImage');
                const zoomButton = document.getElementById('productImageZoomBtn');
                const estimatedTotal = document.getElementById('detailEstimatedTotal');
                const tierLabel = document.getElementById('detailTierLabel');
                const tierDiscount = document.getElementById('detailTierDiscount');
                const bulkTierRows = Array.from(document.querySelectorAll('[data-bulk-tier-row]'));
                const bulkTierHint = document.getElementById('bulkTierHint');
                const buyNowQuantityInput = document.getElementById('productDetailBuyNowQuantity');
                const quantityControl = document.querySelector('[data-detail-quantity-control]');
                const minQuantity = Math.max(1, Number(quantityControl?.dataset.minQuantity || 1));
                const maxQuantityRaw = Number(quantityControl?.dataset.maxQuantity || 0);
                const maxQuantity = maxQuantityRaw > 0 ? maxQuantityRaw : null;
                const lotSize = Math.max(1, Number(quantityControl?.dataset.lotSize || 1));
                
                let quantity = Number(@json($requestedQuantity));

                const loginUrl = @json(route('login'));
                let gallerySources = [];
                let selectedGalleryIndex = 0;
                let previewGalleryIndex = 0;
                let hoverGalleryTimer = null;

                if (mainImage) {
                    try {
                        gallerySources = JSON.parse(mainImage.dataset.galleryImages || '[]');
                    } catch (error) {
                        gallerySources = [];
                    }
                }

                gallerySources = gallerySources.filter(function (value) {
                    return Boolean(String(value || '').trim());
                });

                if (!gallerySources.length && mainImage && mainImage.src) {
                    gallerySources = [mainImage.src];
                }

                const showToast = function (options) {
                    if (!toastHost) {
                        return;
                    }

                    const title = String(options && options.title ? options.title : 'Update');
                    const message = String(options && options.message ? options.message : '');
                    const variant = String(options && options.variant ? options.variant : 'info');
                    const primaryAction = options && options.primary ? options.primary : null;

                    const toast = document.createElement('div');
                    toast.className = 'pointer-events-auto flex w-full max-w-[560px] items-start gap-3 rounded-[18px] border border-white/80 bg-white/92 px-4 py-3 shadow-[0_28px_70px_rgba(15,23,42,0.12)] transition duration-200';

                    const icon = document.createElement('div');
                    icon.className = variant === 'warn'
                        ? 'inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-[14px] bg-orange-50 text-orange-600'
                        : 'inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-[14px] bg-primary-50 text-primary-600';
                    icon.innerHTML = variant === 'warn'
                        ? '<svg width=\"18\" height=\"18\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\"><path d=\"M12 9v4\"></path><path d=\"M12 17h.01\"></path><path d=\"M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z\"></path></svg>'
                        : '<svg width=\"18\" height=\"18\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\"><path d=\"M20 6 9 17l-5-5\"></path></svg>';

                    const body = document.createElement('div');
                    body.className = 'min-w-0 flex-1';

                    const titleEl = document.createElement('div');
                    titleEl.className = 'text-sm font-bold leading-[1.2] text-slate-900';
                    titleEl.textContent = title;

                    const messageEl = document.createElement('div');
                    messageEl.className = 'mt-1.5 text-[13px] font-medium leading-6 text-slate-500';
                    messageEl.textContent = message;

                    body.appendChild(titleEl);
                    if (message) {
                        body.appendChild(messageEl);
                    }

                    const actions = document.createElement('div');
                    actions.className = 'flex shrink-0 items-center gap-2';

                    if (primaryAction && primaryAction.href && primaryAction.label) {
                        const primary = document.createElement('a');
                        primary.className = 'rounded-xl bg-primary-600 px-3 py-2 text-[13px] font-bold text-white shadow-[0_14px_26px_rgba(26,77,46,0.2)] transition hover:-translate-y-px hover:bg-primary-700';
                        primary.href = String(primaryAction.href);
                        primary.textContent = String(primaryAction.label);
                        actions.appendChild(primary);
                    }

                    const dismiss = document.createElement('button');
                    dismiss.type = 'button';
                    dismiss.className = 'rounded-xl border border-slate-300 bg-white px-3 py-2 text-[13px] font-bold text-slate-700 transition hover:-translate-y-px hover:border-primary-200 hover:text-primary-700';
                    dismiss.textContent = 'Dismiss';
                    dismiss.addEventListener('click', function () {
                        toast.remove();
                    });
                    actions.appendChild(dismiss);

                    toast.appendChild(icon);
                    toast.appendChild(body);
                    toast.appendChild(actions);
                    toastHost.appendChild(toast);

                    window.setTimeout(function () {
                        toast.classList.add('translate-y-2', 'scale-[0.98]', 'opacity-0');
                        window.setTimeout(function () {
                            toast.remove();
                        }, 220);
                    }, Number(options && options.duration ? options.duration : 4200));
                };

                const formatInr = function (amount) {
                    const numeric = Number(amount);
                    if (!Number.isFinite(numeric)) {
                        return 'Request Pricing';
                    }

                    const fixed = (Math.round(numeric * 100) / 100).toFixed(2);
                    const parts = fixed.split('.');
                    let integerPart = parts[0];
                    const fractionPart = parts[1] || '00';

                    if (integerPart.length > 3) {
                        const lastThree = integerPart.slice(-3);
                        const rest = integerPart.slice(0, -3).replace(/\B(?=(\d{2})+(?!\d))/g, ',');
                        integerPart = rest + ',' + lastThree;
                    }

                    return 'Rs. ' + integerPart + '.' + fractionPart;
                };

                const resolveActiveTierRow = function (qty) {
                    const desired = Math.max(1, Number(qty) || 1);

                    return bulkTierRows.find(function (row) {
                        const min = Math.max(1, Number(row.dataset.min || 1));
                        const maxRaw = row.dataset.max;
                        const max = maxRaw === undefined ? null : Number(maxRaw);
                        if (!Number.isFinite(max)) {
                            return desired >= min;
                        }
                        return desired >= min && desired <= max;
                    }) || (bulkTierRows.length ? bulkTierRows[0] : null);
                };

                const syncTierPricing = function () {
                    const activeRow = resolveActiveTierRow(quantity);

                    bulkTierRows.forEach(function (row) {
                        if (row === activeRow) {
                            row.classList.add('bg-primary-50/80', 'ring-2', 'ring-inset', 'ring-primary-600/20');
                        } else {
                            row.classList.remove('bg-primary-50/80', 'ring-2', 'ring-inset', 'ring-primary-600/20');
                        }
                    });

                    if (!activeRow) {
                        return;
                    }

                    const unitPrice = String(activeRow.dataset.price || '').trim();
                    const labelText = String(activeRow.dataset.label || '').trim();
                    const discountText = String(activeRow.dataset.discount || '').trim();

                    if (tierLabel) {
                        tierLabel.textContent = labelText ? ('Tier: ' + labelText) : 'Tier';
                    }

                    if (tierDiscount) {
                        tierDiscount.textContent = discountText || '';
                    }

                    if (bulkTierHint) {
                        if (discountText && discountText !== 'None') {
                            bulkTierHint.textContent = labelText && labelText.includes('6') ? ('Best value applied: ' + discountText) : ('Discount active: ' + discountText);
                        } else {
                            bulkTierHint.textContent = 'Best value on 6+ units';
                        }
                    }

                    if (estimatedTotal) {
                        if (!unitPrice) {
                            estimatedTotal.textContent = 'Request Pricing';
                        } else {
                            estimatedTotal.textContent = formatInr(Number(unitPrice) * Math.max(1, quantity));
                        }
                    }
                };

                const syncSelectedQuantity = function (shouldAnimate) {
                    // Step 1: keep the visible quantity label aligned with the current product selection.
                    if (quantityValue) {
                        quantityValue.textContent = String(quantity);

                        if (shouldAnimate !== false) {
                            quantityValue.classList.add('scale-110');

                            window.setTimeout(function () {
                                quantityValue.classList.remove('scale-110');
                            }, 120);
                        }
                    }

                    // Step 2: keep the buy-now form aligned with the same selected quantity.
                    if (buyNowQuantityInput) {
                        buyNowQuantityInput.value = String(quantity);
                    }

                    // Step 3: refresh the tier pricing so the buyer sees the correct checkout estimate.
                    syncTierPricing();

                    // Step 4: disable buttons when they hit the boundaries.
                    const isAtMinimum = quantity <= minQuantity;
                    const isAtMaximum = maxQuantity !== null && quantity >= maxQuantity;

                    document.querySelectorAll('[data-qty-button][data-direction="-1"]').forEach(function (btn) {
                        btn.disabled = isAtMinimum;
                        btn.classList.toggle('opacity-40', isAtMinimum);
                        btn.classList.toggle('cursor-not-allowed', isAtMinimum);
                    });

                    document.querySelectorAll('[data-qty-button][data-direction="1"]').forEach(function (btn) {
                        btn.disabled = isAtMaximum;
                        btn.classList.toggle('opacity-40', isAtMaximum);
                        btn.classList.toggle('cursor-not-allowed', isAtMaximum);
                    });
                };

                syncSelectedQuantity(false);

                bulkTierRows.forEach(function (row) {
                    row.addEventListener('click', function () {
                        const min = Math.max(1, Number(row.dataset.min || 1));
                        quantity = min;
                        syncSelectedQuantity();
                    });
                });

                const openLightbox = function () {
                    if (!lightbox || !lightboxImage || !mainImage) {
                        return;
                    }

                    lightboxImage.src = mainImage.src;
                    lightboxImage.alt = mainImage.alt || 'Product image';
                    lightbox.classList.remove('pointer-events-none', 'opacity-0');
                    lightbox.classList.add('pointer-events-auto', 'opacity-100');
                    lightbox.setAttribute('aria-hidden', 'false');
                    document.body.classList.add('overflow-hidden');
                };

                const closeLightbox = function () {
                    if (!lightbox) {
                        return;
                    }

                    lightbox.classList.remove('pointer-events-auto', 'opacity-100');
                    lightbox.classList.add('pointer-events-none', 'opacity-0');
                    lightbox.setAttribute('aria-hidden', 'true');
                    document.body.classList.remove('overflow-hidden');

                    if (lightboxImage) {
                        lightboxImage.src = '';
                        lightboxImage.alt = '';
                    }
                };

                if (zoomButton) {
                    zoomButton.addEventListener('click', openLightbox);
                }

                if (mainImage) {
                    mainImage.addEventListener('click', openLightbox);
                }

                if (lightbox) {
                    lightbox.querySelectorAll('[data-lightbox-close]').forEach(function (node) {
                        node.addEventListener('click', closeLightbox);
                    });

                    document.addEventListener('keydown', function (event) {
                        if (event.key === 'Escape' && lightbox.classList.contains('opacity-100')) {
                            closeLightbox();
                        }
                    });
                }

                const setActiveGalleryImage = function (index, persistSelection) {
                    if (!mainImage || !gallerySources.length) {
                        return;
                    }

                    const safeIndex = ((index % gallerySources.length) + gallerySources.length) % gallerySources.length;
                    previewGalleryIndex = safeIndex;

                    if (persistSelection) {
                        selectedGalleryIndex = safeIndex;
                    }

                    mainImage.classList.add('opacity-60');
                    mainImage.src = gallerySources[safeIndex];
                    mainImage.alt = safeIndex === 0 ? @json($productTitle) : @json($productTitle) + ' alternate view ' + safeIndex;

                    window.setTimeout(function () {
                        mainImage.classList.remove('opacity-60');
                    }, 160);
                };

                const stopHoverGalleryRotation = function () {
                    if (hoverGalleryTimer) {
                        window.clearInterval(hoverGalleryTimer);
                        hoverGalleryTimer = null;
                    }

                    if (previewGalleryIndex !== selectedGalleryIndex) {
                        setActiveGalleryImage(selectedGalleryIndex, false);
                    }
                };

                const startHoverGalleryRotation = function () {
                    if (!imageStage || gallerySources.length < 2 || hoverGalleryTimer) {
                        return;
                    }

                    hoverGalleryTimer = window.setInterval(function () {
                        setActiveGalleryImage(previewGalleryIndex + 1, false);
                    }, 2000);
                };

                if (imageStage && gallerySources.length > 1) {
                    imageStage.addEventListener('mouseenter', function () {
                        previewGalleryIndex = selectedGalleryIndex;
                        startHoverGalleryRotation();
                    });

                    imageStage.addEventListener('mouseleave', function () {
                        stopHoverGalleryRotation();
                    });

                    const prevBtn = document.getElementById('productPrevImage');
                    const nextBtn = document.getElementById('productNextImage');

                    if (prevBtn) {
                        prevBtn.addEventListener('click', function (e) {
                            e.stopPropagation();
                            stopHoverGalleryRotation();
                            setActiveGalleryImage(selectedGalleryIndex - 1, true);
                        });
                    }

                    if (nextBtn) {
                        nextBtn.addEventListener('click', function (e) {
                            e.stopPropagation();
                            stopHoverGalleryRotation();
                            setActiveGalleryImage(selectedGalleryIndex + 1, true);
                        });
                    }
                }

                document.querySelectorAll('[data-qty-button]').forEach(function (button) {
                    button.addEventListener('click', function () {
                        const direction = Number(button.dataset.direction || 0);
                        if (direction < 0) {
                            quantity = Math.max(minQuantity, quantity - lotSize);
                        } else if (direction > 0) {
                            const nextQuantity = quantity + lotSize;
                            quantity = maxQuantity === null ? nextQuantity : Math.min(maxQuantity, nextQuantity);
                        }
                        syncSelectedQuantity();
                    });
                });

                const addToCartButtons = Array.from(document.querySelectorAll('.js-add-to-cart'));
                addToCartButtons.forEach(function (button) {
                    button.addEventListener('click', async function (event) {
                        const target = event.currentTarget;
                        const productName = String(target.dataset.productName || 'Product');

                        event.preventDefault();

                        if (target.dataset.loading === '1') {
                            return;
                        }

                        const productId = Number(target.dataset.productId || 0);
                        const variantIdRaw = String(target.dataset.variantId || '').trim();
                        const variantId = variantIdRaw ? Number(variantIdRaw) : null;

                        target.dataset.loading = '1';
                        target.setAttribute('aria-busy', 'true');
                        target.classList.add('opacity-80');

                        try {
                            // Step 1: let the shared cart store decide whether this add should stay local or go to the backend cart.
                            const result = await window.CartStore.addItem({
                                productId: productId,
                                variantId: variantId,
                                quantity: quantity,
                                unitPrice: Number(target.dataset.unitPrice || 0) || (Number('{{ $currentPrice ?? 0 }}') || 0),
                                name: productName,
                                model: target.dataset.model || '{{ $modelLabel }}',
                                image: lightboxImage ? lightboxImage.src || mainImage.src : (mainImage ? mainImage.src : ''),
                            });

                            // Step 2: keep the existing toast flow for backend validation and login issues.
                            if (!result || result.ok === false) {
                                throw result || { type: 'error', message: 'Unable to add product to cart.' };
                            }

                            showToast({
                                title: 'Added to cart',
                                message: productName + ' was added to your cart.',
                                variant: 'info',
                            });
                            if (typeof window.openCartSidebar === 'function') {
                                window.openCartSidebar();
                            }
                        } catch (error) {
                            const isAuthError = error && error.type === 'auth';
                            showToast({
                                title: isAuthError ? 'Login required' : 'Could not add to cart',
                                message: isAuthError ? 'Please login again to continue.' : String(error && error.message ? error.message : 'Please try again.'),
                                variant: 'warn',
                                primary: isAuthError ? { label: 'Login', href: loginUrl } : null,
                            });
                        } finally {
                            delete target.dataset.loading;
                            target.removeAttribute('aria-busy');
                            target.classList.remove('opacity-80');
                        }
                    });
                });

                document.querySelectorAll('.js-download-resource').forEach(function (link) {
                    link.addEventListener('click', function (event) {
                        const href = String(link.getAttribute('href') || '').trim();
                        const labelText = String(link.dataset.downloadLabel || 'File');

                        if (!href || href === '#') {
                            event.preventDefault();
                            showToast({
                                title: 'File unavailable',
                                message: labelText + ' is not available for this item yet.',
                                variant: 'warn',
                            });
                            return;
                        }

                        showToast({
                            title: 'Download started',
                            message: labelText + ' is opening in a new tab.',
                            variant: 'info',
                            duration: 2600,
                        });
                    });
                });

                /* ══════════════════════════════════════════════
                   SHARE DROPDOWN
                ══════════════════════════════════════════════ */
                var shareParent   = document.getElementById('shareParent');
                var shareToggle   = document.getElementById('shareToggleBtn');
                var shareDropdown = document.getElementById('shareDropdown');

                function openShare()  { if (shareDropdown) { shareDropdown.classList.remove('invisible','opacity-0'); shareDropdown.classList.add('opacity-100'); } }
                function closeShare() { if (shareDropdown) { shareDropdown.classList.add('invisible','opacity-0'); shareDropdown.classList.remove('opacity-100'); } }

                if (shareToggle && shareDropdown && shareParent) {
                    shareToggle.addEventListener('click', function (e) { e.stopPropagation(); shareDropdown.classList.contains('invisible') ? openShare() : closeShare(); });
                    document.addEventListener('click', function (e) { if (!shareParent.contains(e.target)) closeShare(); });
                    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeShare(); });
                }

                document.querySelectorAll('[data-share]').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        var url   = window.location.href;
                        var title = document.title;
                        var type  = btn.dataset.share;
                        if (type === 'copy') {
                            navigator.clipboard.writeText(url).then(function () {
                                showToast({ title: 'Link copied!', message: 'Product link has been copied to your clipboard.', variant: 'info', duration: 2400 });
                            });
                        } else if (type === 'whatsapp') {
                            window.open('https://wa.me/?text=' + encodeURIComponent(title + ' — ' + url), '_blank');
                        } else if (type === 'email') {
                            window.location.href = 'mailto:?subject=' + encodeURIComponent(title) + '&body=' + encodeURIComponent('Check out this product: ' + url);
                        }
                        closeShare();
                    });
                });

                /* ══════════════════════════════════════════════
                   WISHLIST BUTTON
                ══════════════════════════════════════════════ */
                var WISH_KEY   = 'biogenix_wishlist';
                var wishBtn    = document.getElementById('wishlistBtn');
                var wishIcon   = document.getElementById('wishlistIcon');
                var wishLabel  = document.getElementById('wishlistLabel');
                var productId  = Number(@json($product->id));

                function loadWishlist()   { try { return JSON.parse(localStorage.getItem(WISH_KEY) || '[]'); } catch(e) { return []; } }
                function saveWishlist(arr) { localStorage.setItem(WISH_KEY, JSON.stringify(arr)); }
                function isWished()       { return loadWishlist().some(function (x) { return x.id === productId; }); }

                function syncWishBtn() {
                    if (!wishBtn) return;
                    var active = isWished();
                    wishIcon.setAttribute('fill', active ? 'currentColor' : 'none');
                    wishBtn.classList.toggle('border-rose-300', active);
                    wishBtn.classList.toggle('bg-rose-50', active);
                    wishBtn.classList.toggle('text-rose-600', active);
                    wishBtn.classList.toggle('border-slate-200', !active);
                    wishBtn.classList.toggle('bg-slate-50', !active);
                    wishBtn.classList.toggle('text-slate-600', !active);
                    wishLabel.textContent = active ? 'Saved' : 'Save';
                }
                syncWishBtn();

                if (wishBtn) {
                    wishBtn.addEventListener('click', function () {
                        var list = loadWishlist();
                        if (isWished()) {
                            saveWishlist(list.filter(function (x) { return x.id !== productId; }));
                            showToast({ title: 'Removed from wishlist', message: @json($productTitle) + ' was removed.', variant: 'info', duration: 2000 });
                        } else {
                            list.push({ id: productId, name: @json($productTitle), image: @json($imageUrl), price: @json($currentPrice) });
                            saveWishlist(list);
                            showToast({ title: 'Saved to wishlist ♥', message: @json($productTitle) + ' was saved.', variant: 'info', duration: 2000 });
                        }
                        syncWishBtn();
                    });
                }

                /* ══════════════════════════════════════════════
                   PINCODE CHECK
                ══════════════════════════════════════════════ */
                var pincodeInput    = document.getElementById('pincodeInput');
                var pincodeCheckBtn = document.getElementById('pincodeCheckBtn');
                var pincodeResult   = document.getElementById('pincodeResult');

                if (pincodeCheckBtn && pincodeInput && pincodeResult) {
                    pincodeCheckBtn.addEventListener('click', function () {
                        var code = (pincodeInput.value || '').replace(/\D/g, '');
                        pincodeInput.value = code;
                        if (code.length !== 6) {
                            pincodeResult.textContent = 'Please enter a valid 6-digit pincode.';
                            pincodeResult.className = 'mt-2 min-h-[1.1rem] text-xs font-semibold text-rose-600';
                            return;
                        }
                        var prefix = Number(code.charAt(0));
                        if (prefix >= 2 && prefix <= 8) {
                            pincodeResult.innerHTML = '✓ <strong>Delivery available</strong> to ' + code + ' — Estimated 24-48 hours';
                            pincodeResult.className = 'mt-2 min-h-[1.1rem] text-xs font-semibold text-primary-600';
                        } else {
                            pincodeResult.textContent = '✗ Delivery not available for this pincode currently.';
                            pincodeResult.className = 'mt-2 min-h-[1.1rem] text-xs font-semibold text-rose-600';
                        }
                    });
                    pincodeInput.addEventListener('keydown', function (e) { if (e.key === 'Enter') pincodeCheckBtn.click(); });
                }

                /* ══════════════════════════════════════════════
                   STICKY TAB BAR
                ══════════════════════════════════════════════ */
                var tabBar    = document.getElementById('productTabBar');
                var tabBtns   = tabBar ? Array.from(tabBar.querySelectorAll('.product-tab-btn')) : [];
                var tabTargets = tabBtns.map(function (btn) { return document.getElementById(btn.dataset.tabTarget); }).filter(Boolean);

                tabBtns.forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        var target = document.getElementById(btn.dataset.tabTarget);
                        if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    });
                });

                /* highlight active tab on scroll */
                if (tabTargets.length) {
                    var onScroll = function () {
                        var scrollY = window.scrollY + 80;
                        var activeId = null;
                        tabTargets.forEach(function (sec) {
                            if (sec.offsetTop <= scrollY) activeId = sec.id;
                        });
                        tabBtns.forEach(function (btn) {
                            var isActive = btn.dataset.tabTarget === activeId;
                            btn.classList.toggle('border-primary-600', isActive);
                            btn.classList.toggle('text-primary-700', isActive);
                            btn.classList.toggle('border-transparent', !isActive);
                            btn.classList.toggle('text-slate-500', !isActive);
                        });
                    };
                    window.addEventListener('scroll', onScroll, { passive: true });
                    onScroll();
                }

                /* ══════════════════════════════════════════════
                   STICKY ADD-TO-CART BAR
                ══════════════════════════════════════════════ */
                var stickyBar = document.getElementById('stickyAddToCartBar');
                if (stickyBar) {
                    window.addEventListener('scroll', function () {
                        stickyBar.classList.toggle('translate-y-full', window.scrollY < 400);
                        stickyBar.classList.toggle('translate-y-0', window.scrollY >= 400);
                    }, { passive: true });
                }

                /* ══════════════════════════════════════════════
                   RELATED PRODUCTS CAROUSEL
                ══════════════════════════════════════════════ */
                var relatedTrack = document.getElementById('relatedCarouselTrack');
                var relatedPrev  = document.getElementById('relatedPrev');
                var relatedNext  = document.getElementById('relatedNext');
                var scrollAmt    = 300;

                if (relatedTrack && relatedPrev && relatedNext) {
                    relatedPrev.addEventListener('click', function () { relatedTrack.scrollBy({ left: -scrollAmt, behavior: 'smooth' }); });
                    relatedNext.addEventListener('click', function () { relatedTrack.scrollBy({ left: scrollAmt, behavior: 'smooth' }); });
                }

            });
        </script>
    @endpush

@endif
@endsection

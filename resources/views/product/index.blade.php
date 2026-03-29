@extends('layouts.app')

@section('title', 'Product Catalog')

@section('content')
@php
    use Illuminate\Support\Str;

    $productCollection = collect($products->items());
    $categoryOptions = $catalogOptions['categoryOptions'] ?? collect();
    $applicationOptions = $catalogOptions['applicationOptions'] ?? collect();
    $brandOptions = $catalogOptions['brandOptions'] ?? collect();
    $minPrice = (int) ($catalogOptions['minPrice'] ?? 150);
    $maxPrice = (int) ($catalogOptions['maxPrice'] ?? 2500);

    $selectedCategories = collect((array) request()->input('category_name', []))
        ->map(fn ($value) => trim((string) $value))
        ->filter()
        ->values();

    $selectedApplications = collect((array) request()->input('application_name', request()->input('subcategory_name', [])))
        ->map(fn ($value) => trim((string) $value))
        ->filter()
        ->values();

    $selectedBrands = collect((array) request()->input('brand_name', []))
        ->map(fn ($value) => trim((string) $value))
        ->filter()
        ->values();

    $promotedApplications = $selectedCategories
        ->filter(fn ($value) => ! $categoryOptions->has($value) && $applicationOptions->has($value))
        ->values();

    if ($promotedApplications->isNotEmpty()) {
        $selectedApplications = $selectedApplications
            ->merge($promotedApplications)
            ->unique()
            ->values();

        $selectedCategories = $selectedCategories
            ->reject(fn ($value) => $promotedApplications->contains($value))
            ->values();
    }

    $search = trim((string) request('search', request('search_text', request('search_value', ''))));
    $sort = trim((string) request('sort', 'relevant'));
    $selectedMaxPrice = request()->filled('max_price') ? min($maxPrice, (float) request('max_price')) : $maxPrice;
    $activeFilterCount = $selectedCategories->count()
        + $selectedApplications->count()
        + $selectedBrands->count()
        + ($search !== '' ? 1 : 0)
        + ($selectedMaxPrice < $maxPrice ? 1 : 0);
    $totalProducts = (int) $products->total();
    $productSummaryLabel = $totalProducts.' '.Str::plural('product', $totalProducts).' available';
    $filterSummaryLabel = $activeFilterCount === 1
        ? '1 active filter'
        : $activeFilterCount.' active filters';
    $formatInrPlain = function (float|int|null $amount, int $decimals = 0): string {
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

        return ($negative ? '-' : '') . 'Rs. ' . $integerPart . ($decimals > 0 ? '.' . $fractionPart : '');
    };

    $currentQuery = request()->query();

    $normalizeQueryArray = function ($value): array {
        $values = is_array($value) ? $value : [$value];

        return collect($values)
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->values()
            ->all();
    };

    $dropQueryKey = function (string $key) use ($currentQuery): string {
        $query = $currentQuery;
        unset($query[$key], $query['page']);

        return route('products.index', $query);
    };

    $dropQueryKeys = function (array $keys) use ($currentQuery): string {
        $query = $currentQuery;

        foreach ($keys as $key) {
            unset($query[$key]);
        }

        unset($query['page']);

        return route('products.index', $query);
    };

    $removeQueryArrayValue = function (string $key, string $value) use ($currentQuery, $normalizeQueryArray): string {
        $query = $currentQuery;
        $values = $normalizeQueryArray($query[$key] ?? []);
        $values = array_values(array_filter($values, fn ($item) => $item !== $value));

        if (count($values)) {
            $query[$key] = $values;
        } else {
            unset($query[$key]);
        }

        unset($query['page']);

        return route('products.index', $query);
    };

    $removeQueryArrayValueFromKeys = function (array $keys, string $value) use ($currentQuery, $normalizeQueryArray): string {
        $query = $currentQuery;

        foreach ($keys as $key) {
            if (! array_key_exists($key, $query)) {
                continue;
            }

            $values = $normalizeQueryArray($query[$key] ?? []);
            $values = array_values(array_filter($values, fn ($item) => $item !== $value));

            if (count($values)) {
                $query[$key] = $values;
            } else {
                unset($query[$key]);
            }
        }

        unset($query['page']);

        return route('products.index', $query);
    };

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

    $resolveVisualVariant = function ($product, int $index): string {
        $context = Str::lower(implode(' ', array_filter([
            $product->name ?? null,
            $product->category_name ?? null,
            $product->subcategory_name ?? null,
            $product->brand ?? null,
        ])));

        return match (true) {
            Str::contains($context, ['ecg', 'monitor', 'analyzer', 'hematology']) => 'machine',
            Str::contains($context, ['glove', 'plate']) => 'tray',
            Str::contains($context, ['syringe', 'needle', 'fish']) => 'tubes',
            Str::contains($context, ['centrifuge']) => 'centrifuge',
            Str::contains($context, ['microscope', 'gene']) => 'microscope',
            Str::contains($context, ['culture', 'petri']) => 'petri',
            default => ['vials', 'tubes', 'machine', 'tray', 'microscope', 'petri'][$index % 6],
        };
    };

    $badgeSets = [
        ['In Stock', 'Verified'],
        ['Clinical Ready', null],
        ['New Arrival', null],
        ['Best Seller', null],
        ['Institutional Fav', null],
        ['Legacy Support', null],
    ];
@endphp

<div class="mx-auto w-full max-w-none bg-gradient-to-b from-white via-primary-50/15 to-white md:mt-0">
    <div class="w-full max-w-none box-border px-4 sm:px-6 lg:px-8 xl:px-10">
        <div id="catalogMobileBackdrop" class="pointer-events-none fixed inset-0 z-[60] bg-primary-950/45 opacity-0 transition-opacity duration-200 xl:hidden" aria-hidden="true"></div>
        <div id="catalogLoadingOverlay" class="pointer-events-none fixed inset-0 z-[75] flex items-center justify-center bg-primary-950/18 px-4 opacity-0 transition-opacity duration-200" aria-hidden="true">
            <div class="w-full max-w-sm rounded-[var(--ui-radius-card)] border border-white/80 bg-white/95 p-6 shadow-[var(--ui-shadow-panel)] backdrop-blur">
                <div class="flex items-center gap-3">
                    <span class="h-10 w-10 animate-spin rounded-full border-4 border-primary-100 border-t-primary-600" aria-hidden="true"></span>
                    <p class="text-sm font-semibold text-slate-900">Updating results</p>
                </div>
                <p class="mt-3 text-sm leading-6 text-slate-500">Applying filters and refreshing product availability. This takes a moment.</p>
            </div>
        </div>
        <div id="uiToastHost" class="pointer-events-none fixed inset-x-0 bottom-6 z-[95] flex flex-col items-center gap-3 px-4" aria-live="polite" aria-atomic="true"></div>
        <form id="catalogFiltersForm" method="GET" action="{{ route('products.index') }}" class="space-y-4 md:space-y-5">
            <section class="relative z-10 w-full pt-2 md:pt-5">
                <div class="rounded-[var(--ui-radius-card)] border border-white/70 bg-gradient-to-br from-white via-primary-50/65 to-white p-5 shadow-[var(--ui-shadow-card)] backdrop-blur md:p-6 glass-card">
                    <div class="flex flex-col gap-4">
                        <div class="flex flex-col items-start gap-3 xl:flex-row xl:justify-between">
                            <div class="max-w-xl">
                                <h1 class="shrink-0 font-display text-2xl font-bold tracking-tight text-slate-950 md:text-3xl">Product Catalog</h1>
                                <p class="mt-2 text-sm leading-6 text-slate-600">
                                    Explore our comprehensive range of high-performance biotech reagents, IVD kits, and life science research tools engineered for precision.
                                </p>
                            </div>

                            <div class="grid w-full flex-1 gap-3 md:grid-cols-[1fr_minmax(180px,210px)] xl:max-w-[54rem]">
                                <label class="flex min-h-[2.5rem] flex-wrap items-stretch gap-2.5 rounded-xl border border-slate-200/80 bg-white/95 px-3 py-1 shadow-[var(--ui-shadow-soft)] backdrop-blur md:flex-nowrap md:items-center md:gap-3 md:px-3.5 md:py-1">
                                    <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="11" cy="11" r="7"></circle>
                                        <path d="m20 20-3.5-3.5"></path>
                                    </svg>
                                    <input
                                        type="search"
                                        name="search"
                                        value="{{ $search }}"
                                        placeholder="Search by SKU, product name, application, or brand..."
                                        class="min-w-0 flex-1 border-0 bg-transparent text-[13px] font-medium text-slate-900 outline-none placeholder:text-slate-400"
                                        aria-label="Search products"
                                    >
                                    <button type="submit" class="w-full rounded-lg bg-primary-600 px-4 py-1.5 text-[13px] font-semibold text-white shadow-lg shadow-primary-600/20 transition hover:-translate-y-px hover:bg-primary-700 hover:shadow-xl hover:shadow-primary-600/25 md:w-auto">Search</button>
                                </label>

                                <div class="relative flex min-h-[2.5rem] items-center justify-between gap-3 rounded-xl border border-slate-200/80 bg-white/95 px-4 py-1 shadow-[var(--ui-shadow-soft)] backdrop-blur" id="customSortDropdown">
                                    <span class="whitespace-nowrap text-[13px] font-medium text-slate-400">Sort by:</span>
                                    <select id="catalogSort" name="sort" class="hidden">
                                        <option value="relevant" @selected($sort === 'relevant')>Most Relevant</option>
                                        <option value="name_az" @selected($sort === 'name_az')>Name A-Z</option>
                                        <option value="price_low" @selected($sort === 'price_low')>Price Low to High</option>
                                        <option value="price_high" @selected($sort === 'price_high')>Price High to Low</option>
                                    </select>
                                    <button type="button" id="customSortButton" class="flex min-w-0 flex-1 items-center justify-between gap-3 border-0 bg-transparent pl-1 pr-2 text-sm font-semibold text-slate-800 outline-none">
                                        <span id="customSortLabel" class="truncate">
                                            @switch($sort)
                                                @case('name_az') Name A-Z @break
                                                @case('price_low') Price Low to High @break
                                                @case('price_high') Price High to Low @break
                                                @default Most Relevant
                                            @endswitch
                                        </span>
                                        <svg class="h-4 w-4 shrink-0 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <div id="customSortMenu" class="absolute right-0 top-full mt-2 hidden w-48 origin-top-right rounded-xl border border-slate-200 bg-white p-1.5 shadow-lg ring-1 ring-black ring-opacity-5 z-[50]">
                                        @php
                                            $options = [
                                                'relevant' => 'Most Relevant',
                                                'name_az' => 'Name A-Z',
                                                'price_low' => 'Price Low to High',
                                                'price_high' => 'Price High to Low',
                                            ];
                                        @endphp
                                        @foreach($options as $val => $label)
                                            <button type="button" data-sort-value="{{ $val }}" class="custom-sort-option flex w-full items-center rounded-lg px-3 py-2.5 text-sm font-semibold transition-colors {{ $sort === $val ? 'bg-primary-50 text-primary-700' : 'text-slate-700 hover:bg-primary-50 hover:text-primary-700' }}">
                                                {{ $label }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="grid items-start gap-5 pb-4 md:pb-5 xl:grid-cols-[18rem_minmax(0,1fr)] min-[1680px]:grid-cols-[18.5rem_minmax(0,1fr)]">
                <aside id="catalogSidebar" class="hidden min-w-0 space-y-5 rounded-[var(--ui-radius-card)] border border-slate-200/80 bg-white/95 p-4 shadow-[var(--ui-shadow-card)] backdrop-blur md:p-5 xl:sticky xl:top-6 xl:block glass-card">
                    <div class="flex items-center justify-between gap-3 xl:hidden">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">Filters</p>
                            <p class="mt-1 text-sm font-medium text-slate-500">Select multiple filters, then apply.</p>
                        </div>
                        <button type="button" id="catalogFiltersClose" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:border-primary-200 hover:text-primary-700" aria-label="Close filters">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M18 6 6 18"></path>
                                <path d="M6 6l12 12"></path>
                            </svg>
                            Close
                        </button>
                    </div>

                    @if ($activeFilterCount > 0)
                        <div class="flex items-center justify-between rounded-2xl bg-primary-50 px-4 py-3">
                            <span class="text-sm font-medium text-primary-700">{{ $filterSummaryLabel }}</span>
                            <a href="{{ route('products.index') }}" class="text-sm font-semibold text-primary-700 no-underline hover:text-primary-600">Reset</a>
                        </div>
                    @endif

                    <details class="border-t border-slate-200 pt-4 first:border-t-0 first:pt-0" open>
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-3 font-semibold text-slate-900 [&::-webkit-details-marker]:hidden">
                            <span class="text-sm font-semibold text-slate-900">Category</span>
                            <span class="flex items-center gap-3">
                                @if ($selectedCategories->isNotEmpty())
                                    <a href="{{ $dropQueryKey('category_name') }}" class="text-xs font-semibold text-primary-700 no-underline hover:text-primary-600">Reset</a>
                                @endif
                                <svg class="h-4 w-4 text-slate-400 transition" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"></path></svg>
                            </span>
                        </summary>
                        <div class="mt-4 space-y-3">
                            @forelse ($categoryOptions as $label => $count)
                                <label class="flex items-center justify-between gap-3 text-sm text-slate-800">
                                    <span class="flex items-center gap-3">
                                        <input class="catalog-auto h-4 w-4 rounded border-slate-300 text-primary-600" type="checkbox" name="category_name[]" value="{{ $label }}" @checked($selectedCategories->contains($label))>
                                        <span>{{ $label }}</span>
                                    </span>
                                    <span class="text-xs text-slate-400">{{ $count }}</span>
                                </label>
                            @empty
                                <p class="text-sm leading-6 text-slate-400">No categories available for the current catalog view.</p>
                            @endforelse
                        </div>
                    </details>

                    <details class="border-t border-slate-200 pt-4 first:border-t-0 first:pt-0" open>
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-3 font-semibold text-slate-900 [&::-webkit-details-marker]:hidden">
                            <span class="text-sm font-semibold text-slate-900">Sub-Category</span>
                            <span class="flex items-center gap-3">
                                @if ($selectedApplications->isNotEmpty())
                                    <a href="{{ $dropQueryKeys(['application_name', 'subcategory_name']) }}" class="text-xs font-semibold text-primary-700 no-underline hover:text-primary-600">Reset</a>
                                @endif
                                <svg class="h-4 w-4 text-slate-400 transition" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"></path></svg>
                            </span>
                        </summary>
                        <div class="mt-4 space-y-3">
                            @forelse ($applicationOptions as $label => $count)
                                <label class="flex items-center justify-between gap-3 text-sm text-slate-800">
                                    <span class="flex items-center gap-3">
                                        <input class="catalog-auto h-4 w-4 rounded border-slate-300 text-primary-600" type="checkbox" name="application_name[]" value="{{ $label }}" @checked($selectedApplications->contains($label))>
                                        <span>{{ $label }}</span>
                                    </span>
                                    <span class="text-xs text-slate-400">{{ $count }}</span>
                                </label>
                            @empty
                                <p class="text-sm leading-6 text-slate-400">No applications available for the current catalog view.</p>
                            @endforelse
                        </div>
                    </details>

                    <details class="border-t border-slate-200 pt-4 first:border-t-0 first:pt-0" open>
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-3 font-semibold text-slate-900 [&::-webkit-details-marker]:hidden">
                            <span class="text-sm font-semibold text-slate-900">Brand</span>
                            <span class="flex items-center gap-3">
                                @if ($selectedBrands->isNotEmpty())
                                    <a href="{{ $dropQueryKey('brand_name') }}" class="text-xs font-semibold text-primary-700 no-underline hover:text-primary-600">Reset</a>
                                @endif
                                <svg class="h-4 w-4 text-slate-400 transition" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"></path></svg>
                            </span>
                        </summary>
                        <div class="mt-4 space-y-3">
                            @forelse ($brandOptions as $label => $count)
                                <label class="flex items-center justify-between gap-3 text-sm text-slate-800">
                                    <span class="flex items-center gap-3">
                                        <input class="catalog-auto h-4 w-4 rounded border-slate-300 text-primary-600" type="checkbox" name="brand_name[]" value="{{ $label }}" @checked($selectedBrands->contains($label))>
                                        <span>{{ $label }}</span>
                                    </span>
                                    <span class="text-xs text-slate-400">{{ $count }}</span>
                                </label>
                            @empty
                                <p class="text-sm leading-6 text-slate-400">No brands available for the current catalog view.</p>
                            @endforelse
                        </div>
                    </details>

                    <details class="border-t border-slate-200 pt-4 first:border-t-0 first:pt-0" open>
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-3 font-semibold text-slate-900 [&::-webkit-details-marker]:hidden">
                            <span class="text-sm font-semibold text-slate-900">Price Range</span>
                            <span class="flex items-center gap-3">
                                <span id="catalogPriceLabel" class="text-xs font-semibold text-primary-700">{!! $formatInr($selectedMaxPrice, 0) !!}</span>
                                @if ($selectedMaxPrice < $maxPrice)
                                    <a href="{{ $dropQueryKey('max_price') }}" class="text-xs font-semibold text-primary-700 no-underline hover:text-primary-600">Reset</a>
                                @endif
                                <svg class="h-4 w-4 text-slate-400 transition" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"></path></svg>
                            </span>
                        </summary>
                        <div class="mt-4 px-1">
                            <input
                                id="catalogPriceRange"
                                type="range"
                                name="max_price"
                                min="{{ $minPrice }}"
                                max="{{ $maxPrice }}"
                                step="1"
                                value="{{ $selectedMaxPrice }}"
                                class="h-2 w-full cursor-pointer appearance-none rounded-full bg-slate-300 accent-primary-600"
                            >
                            <div class="mt-4 flex items-center justify-between text-xs font-semibold text-slate-700">
                                <span>{!! $formatInr($minPrice, 0) !!}</span>
                                <span>{!! $formatInr($maxPrice, 0) !!}</span>
                            </div>
                        </div>
                    </details>

                    <div class="flex flex-col gap-3 border-t border-slate-200 pt-4 xl:hidden">
                        <button type="button" id="catalogMobileApplyFilters" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-primary-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700">Apply Filters</button>
                        <a href="{{ route('products.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Reset Filters</a>
                    </div>
                </aside>

                <div class="grid min-w-0 content-start gap-4">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex flex-wrap items-center gap-3">
                            <div class="rounded-xl border border-slate-200 bg-white px-3.5 py-1.5 shadow-sm">
                                <p class="text-[12px] font-medium text-slate-500">{{ $productSummaryLabel }}</p>
                            </div>
                            @if ($search !== '')
                                <div class="rounded-2xl border border-primary-100 bg-primary-50 px-4 py-3 shadow-sm">
                                    <p class="text-sm font-medium text-primary-700">Search term: <span class="font-semibold">{{ $search }}</span></p>
                                </div>
                            @endif
                            @if ($activeFilterCount > 0)
                                <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                                    <p class="text-sm font-medium text-slate-500">{{ $filterSummaryLabel }}</p>
                                </div>
                            @endif
                        </div>

                        <div class="flex flex-wrap items-center gap-2.5">
                            <button type="button" id="catalogFiltersOpen" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:border-primary-200 hover:text-primary-700 xl:hidden" aria-label="Open filters">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 6h16"></path>
                                    <path d="M7 12h10"></path>
                                    <path d="M10 18h4"></path>
                                </svg>
                                Filters
                            </button>

                            @if ($search !== '' || $activeFilterCount > 0)
                                <a href="{{ route('products.index') }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                                    Reset Filters
                                </a>
                            @endif
                        </div>
                    </div>

                    @if ($search !== '' || $selectedMaxPrice < $maxPrice || $selectedCategories->isNotEmpty() || $selectedApplications->isNotEmpty() || $selectedBrands->isNotEmpty())
                        <div class="flex flex-wrap items-center gap-2">
                            @if ($search !== '')
                                <a href="{{ $dropQueryKeys(['search', 'search_text', 'search_value']) }}" class="inline-flex items-center gap-2 whitespace-nowrap rounded-full border border-slate-300/90 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 shadow-sm transition hover:-translate-y-px hover:border-primary-200 hover:text-primary-700" title="Remove search filter">
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-500">Search</span>
                                    <span class="text-slate-800">{{ Str::limit($search, 26) }}</span>
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"></path><path d="M6 6l12 12"></path></svg>
                                </a>
                            @endif

                            @if ($selectedMaxPrice < $maxPrice)
                                <a href="{{ $dropQueryKey('max_price') }}" class="inline-flex items-center gap-2 whitespace-nowrap rounded-full border border-slate-300/90 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 shadow-sm transition hover:-translate-y-px hover:border-primary-200 hover:text-primary-700" title="Remove max price filter">
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-500">Max Price</span>
                                    <span class="text-slate-800">{{ $formatInrPlain($selectedMaxPrice, 0) }}</span>
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"></path><path d="M6 6l12 12"></path></svg>
                                </a>
                            @endif

                            @foreach ($selectedCategories as $label)
                                <a href="{{ $removeQueryArrayValue('category_name', $label) }}" class="inline-flex items-center gap-2 whitespace-nowrap rounded-full border border-slate-300/90 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 shadow-sm transition hover:-translate-y-px hover:border-primary-200 hover:text-primary-700" title="Remove category filter">
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-500">Category</span>
                                    <span class="text-slate-800">{{ $label }}</span>
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"></path><path d="M6 6l12 12"></path></svg>
                                </a>
                            @endforeach

                            @foreach ($selectedApplications as $label)
                                <a href="{{ $removeQueryArrayValueFromKeys(['application_name', 'subcategory_name'], $label) }}" class="inline-flex items-center gap-2 whitespace-nowrap rounded-full border border-slate-300/90 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 shadow-sm transition hover:-translate-y-px hover:border-primary-200 hover:text-primary-700" title="Remove application filter">
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-500">Application</span>
                                    <span class="text-slate-800">{{ $label }}</span>
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"></path><path d="M6 6l12 12"></path></svg>
                                </a>
                            @endforeach

                            @foreach ($selectedBrands as $label)
                                <a href="{{ $removeQueryArrayValue('brand_name', $label) }}" class="inline-flex items-center gap-2 whitespace-nowrap rounded-full border border-slate-300/90 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 shadow-sm transition hover:-translate-y-px hover:border-primary-200 hover:text-primary-700" title="Remove brand filter">
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-500">Brand</span>
                                    <span class="text-slate-800">{{ $label }}</span>
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"></path><path d="M6 6l12 12"></path></svg>
                                </a>
                            @endforeach

                            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 whitespace-nowrap rounded-full border border-primary-100 bg-primary-50 px-3 py-1.5 text-xs font-semibold text-primary-700 shadow-sm transition hover:-translate-y-px hover:border-primary-200 hover:text-primary-700" title="Reset filters">
                                <span class="rounded-full bg-white/80 px-2 py-0.5 text-[11px] font-semibold text-primary-700">Reset</span>
                                <span>Filters</span>
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"></path><path d="M6 6l12 12"></path></svg>
                            </a>
                        </div>
                    @endif

                    @if ($productCollection->isEmpty())
                        <x-ui.empty-state
                            icon="product"
                            title="No products matched these filters"
                            description="Remove one or more filters to see more catalog items."
                            :action-href="route('products.index')"
                            action-label="Reset Filters"
                            class="rounded-[28px] border border-slate-200 bg-white px-6 py-16 shadow-sm"
                        />
                    @else
                        <div data-catalog-product-grid class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">
                            @foreach ($productCollection as $product)
                                @php
                                    $badgeRow = $badgeSets[$loop->index % count($badgeSets)];
                                    $price = $product->visible_price !== null ? (float) $product->visible_price : null;
                                    $mrpPrice = $product->visible_base_price !== null
                                        ? (float) $product->visible_base_price
                                        : ($price !== null ? round($price * 1.16, 2) : null);
                                    $showMrpPrice = $price !== null && $mrpPrice !== null && $mrpPrice > $price;
                                    $defaultQuantity = max(1, (int) ($product->visible_min_order_quantity ?? 1));
                                    $maxQuantity = ($product->visible_max_order_quantity ?? null) === null
                                        ? null
                                        : max($defaultQuantity, (int) $product->visible_max_order_quantity);
                                    $lotSize = max(1, (int) ($product->visible_lot_size ?? 1));
                                    $detailUrl = route('products.productDetails', $product->id);
                                    $variantId = $product->visible_variant_id ?? null;
                                    $imageUrl = filled($product->image_path ?? null) ? asset($product->image_path) : null;
                                    $catalogHoverImages = collect([
                                        $imageUrl,
                                        asset('upload/products/image1.jpg'),
                                        asset('upload/products/image2.jpg'),
                                        asset('upload/products/image3.jpg'),
                                    ])->filter()->unique()->values();
                                    $visualVariant = $resolveVisualVariant($product, $loop->index);
                                    $bulkSummary = $product->catalog_bulk_summary ?? null;
                                @endphp
                                <article data-catalog-product-card data-product-id="{{ $product->id }}" data-product-name="{{ Str::lower((string) $product->name) }}" data-product-sku="{{ Str::lower($product->visible_variant_sku ?? $product->sku ?? '') }}" data-product-category="{{ Str::lower($product->category_name ?? '') }}" data-product-subcategory="{{ Str::lower($product->subcategory_name ?? $product->application_name ?? '') }}" data-product-brand="{{ Str::lower($product->brand ?? '') }}" data-product-price="{{ $price ?? 0 }}" data-original-index="{{ $loop->index }}" class="group flex h-full flex-col overflow-hidden rounded-[var(--ui-radius-card)] border border-slate-200/80 bg-white/95 shadow-[var(--ui-shadow-card)] backdrop-blur transition duration-300 hover:-translate-y-1 hover:border-primary-100 hover:shadow-[var(--ui-shadow-panel)] hover-lift">
                                    <div class="relative px-3 pt-3">
                                        <div class="group/image relative overflow-hidden rounded-3xl bg-gradient-to-br from-primary-50/70 via-white to-slate-50" @if ($imageUrl) data-catalog-hover-card @endif>
                                            <a href="{{ $detailUrl }}" data-catalog-detail-link data-base-url="{{ $detailUrl }}" class="block cursor-pointer">
                                                @if ($imageUrl)
                                                    <img src="{{ $imageUrl }}" alt="{{ $product->name }}" data-catalog-hover-image data-hover-images='@json($catalogHoverImages->all())' data-hover-alt="{{ $product->name }}" class="h-[clamp(13.5rem,18vw,15rem)] w-full object-cover transition duration-300 group-hover/image:scale-[1.04]" loading="lazy" decoding="async">
                                                @else
                                                    @include('customer.partials.product-visual', ['variant' => $visualVariant, 'class' => 'h-[clamp(13.5rem,18vw,15rem)] w-full rounded-2xl transition duration-300 group-hover/image:scale-[1.04]'])
                                                @endif
                                            </a>
                                            {{-- Step 3: keep a single compact quantity stepper pinned inside the image itself so it never drops below the media block. --}}
                                            <div
                                                data-catalog-quantity-control
                                                data-product-id="{{ $product->id }}"
                                                data-min-quantity="{{ $defaultQuantity }}"
                                                data-max-quantity="{{ $maxQuantity ?? '' }}"
                                                data-lot-size="{{ $lotSize }}"
                                                class="absolute bottom-3 right-3 z-10 flex h-8 min-w-[5rem] items-center rounded-full border border-white/90 bg-white/95 px-1 shadow-[var(--ui-shadow-soft)] backdrop-blur-sm"
                                            >
                                                <button type="button" data-catalog-qty-button data-direction="-1" class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full border border-primary-100 bg-white text-[1rem] font-medium text-primary-700 shadow-sm transition hover:bg-primary-50">-</button>
                                                <span data-catalog-quantity-value class="inline-flex min-w-0 flex-1 items-center justify-center px-2 text-[0.82rem] font-semibold tracking-tight text-slate-900">{{ number_format($defaultQuantity) }}</span>
                                                <button type="button" data-catalog-qty-button data-direction="1" class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-primary-600 text-[1rem] font-medium text-white shadow-md shadow-primary-600/25 transition hover:bg-primary-700">+</button>
                                            </div>
                                        </div>
                                        <div class="absolute left-6 top-6 flex flex-col gap-2">
                                            @foreach ($badgeRow as $badgeIndex => $badgeLabel)
                                                @if ($badgeLabel)
                                                    <x-ui.status-badge type="product" :value="$badgeLabel" :label="$badgeLabel" class="shadow-sm" />
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="flex flex-1 flex-col gap-2.5 px-4 pb-4 pt-3.5">
                                        <div class="space-y-1.5">
                                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">{{ $product->brand ?? 'Biogenix' }}</p>
                                            <h3 class="font-display overflow-hidden text-[15px] font-semibold leading-snug text-slate-950 [display:-webkit-box] [-webkit-box-orient:vertical] [-webkit-line-clamp:2] transition-colors hover:text-primary-600">
                                                <a href="{{ $detailUrl }}" data-catalog-detail-link data-base-url="{{ $detailUrl }}">{{ Str::limit((string) $product->name, 58) }}</a>
                                            </h3>
                                            <div>
                                                <p class="text-[12px] text-slate-500">SKU: <span class="font-medium tracking-tight text-slate-700">{{ $product->visible_variant_sku ?? $product->sku ?? 'N/A' }}</span></p>
                                                <p class="mt-0.5 text-[11.5px] font-medium text-slate-400">
                                                    Min: <span class="text-slate-600">{{ $defaultQuantity }}</span>
                                                    @if (($product->visible_max_order_quantity ?? null) !== null) &bull; Max: <span class="text-slate-600">{{ number_format((int) $product->visible_max_order_quantity) }}</span> @endif
                                                    @if ($lotSize > 1) &bull; Lot: <span class="text-slate-600">{{ $lotSize }}</span> @endif
                                                </p>
                                            </div>
                                        </div>

                                        <div class="rounded-3xl border border-slate-200/70 bg-slate-50/90 px-3 py-2.5">
                                            <div class="flex items-end justify-between gap-3">
                                                <span class="text-sm font-semibold text-slate-500">Price:</span>
                                                <span class="text-xl font-extrabold tracking-tight text-primary-700">{!! $formatInr($price, 2) !!}</span>
                                            </div>
                                            @if ($showMrpPrice)
                                                <p class="mt-1 text-right text-sm font-medium text-slate-400 line-through">{!! $formatInr($mrpPrice, 2) !!}</p>
                                            @endif
                                        </div>

                                        @if ($bulkSummary)
                                            <div class="rounded-3xl border border-primary-100/80 bg-primary-50/70 px-3 py-2.5">
                                                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary-600">Bulk Offer</p>
                                                <p class="mt-1 text-xs font-semibold text-emerald-900">
                                                    {{ $bulkSummary['label'] ?? 'Bulk pricing available' }}
                                                    @if (filled($bulkSummary['discount'] ?? null))
                                                        | {{ $bulkSummary['discount'] }}
                                                    @endif
                                                </p>
                                            </div>
                                        @endif

                                        <div class="hidden space-y-2.5">
                                            <div class="flex min-h-[2.65rem] items-center justify-center overflow-hidden rounded-full border border-slate-200 bg-white px-3 shadow-[0_8px_20px_rgba(15,23,42,0.04)]">
                                                <div class="flex min-w-0 items-center gap-3 text-[0.95rem] font-medium text-slate-600">
                                                <span class="whitespace-nowrap">Min <span class="font-semibold text-slate-900">{{ number_format($defaultQuantity) }}</span></span>
                                                @if (($product->visible_max_order_quantity ?? null) !== null)
                                                    <span class="h-4 w-px bg-slate-200"></span>
                                                    <span class="whitespace-nowrap">Max <span class="font-semibold text-slate-900">{{ number_format((int) $product->visible_max_order_quantity) }}</span></span>
                                                @endif
                                                <span class="h-4 w-px bg-slate-200"></span>
                                                <span class="whitespace-nowrap">Lot <span class="font-semibold text-slate-900">{{ number_format($lotSize) }}</span></span>
                                                </div>
                                            </div>

                                            <div class="hidden min-w-0">
                                                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">Quantity</p>
                                                <p class="mt-1 text-xs font-medium text-slate-500">
                                                    Min {{ $defaultQuantity }}
                                                    @if ($lotSize > 1)
                                                        • Step {{ $lotSize }}
                                                    @endif
                                                </p>
                                            </div>

                                            {{-- Step 3: keep the quantity stepper compact and let it follow the real min, max, and lot-size rules coming from backend pricing. --}}
                                            <div
                                                data-catalog-quantity-control
                                                data-product-id="{{ $product->id }}"
                                                data-min-quantity="{{ $defaultQuantity }}"
                                                data-max-quantity="{{ $maxQuantity ?? '' }}"
                                                data-lot-size="{{ $lotSize }}"
                                                class="hidden h-[3.05rem] w-full items-center overflow-hidden rounded-full border border-slate-200 bg-white px-1.5 shadow-[0_10px_22px_rgba(15,23,42,0.05)]"
                                            >
                                                <button type="button" data-catalog-qty-button data-direction="-1" class="inline-flex h-full w-9 shrink-0 items-center justify-center text-[1.6rem] font-medium text-slate-300 transition">-</button>
                                                <div class="flex h-full min-w-0 flex-1 items-center rounded-full bg-slate-50 px-1.5">
                                                    <button type="button" data-catalog-qty-button data-direction="-1" class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-slate-200 bg-white text-[1.4rem] font-medium text-primary-600 shadow-[0_6px_14px_rgba(15,23,42,0.06)] transition">-</button>
                                                    <span data-catalog-quantity-value class="inline-flex min-w-0 flex-1 items-center justify-center px-3 text-[1.05rem] font-semibold tracking-tight text-slate-900">{{ number_format($defaultQuantity) }}</span>
                                                    <button type="button" data-catalog-qty-button data-direction="1" class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-primary-100 bg-primary-600 text-[1.4rem] font-medium text-white shadow-[0_10px_18px_rgba(26,77,46,0.2)] transition">+</button>
                                                </div>
                                                <button type="button" data-catalog-qty-button data-direction="1" class="inline-flex h-full w-9 shrink-0 items-center justify-center text-[1.6rem] font-medium text-slate-300 transition">+</button>
                                            </div>
                                        </div>

                                        <div data-catalog-action-group class="mt-auto flex w-full items-center gap-2">
                                            {{-- Buy Now --}}
                                             <div class="w-[70%]">
                                                <button type="submit" form="catalogBuyNowForm{{ $product->id }}" data-catalog-buy-now class="flex h-11 w-full min-w-0 items-center justify-center gap-2 whitespace-nowrap rounded-xl border border-transparent bg-orange-500 px-3 text-[13px] font-bold uppercase tracking-wide text-white shadow-md shadow-orange-500/25 transition hover:bg-orange-600 hover-lift glow-orange cursor-pointer">
                                                    <span>Buy Now</span>
                                                </button>
                                            </div>

                                            {{-- Add to Cart --}}
                                            <div class="w-[30%]">
                                                @guest
                                                    <a href="{{ route('login') }}" class="js-add-to-cart flex h-11 w-full min-w-0 items-center justify-center rounded-xl bg-primary-600 text-white shadow-lg shadow-primary-600/20 transition hover:bg-primary-700 hover:shadow-xl hover:shadow-primary-600/25 hover-lift cursor-pointer" title="Add to Cart" data-product-id="{{ $product->id }}" data-variant-id="{{ $variantId ?? '' }}" data-quantity="{{ $defaultQuantity }}" data-product-name="{{ e((string) ($product->name ?? '')) }}" data-unit-price="{{ $price }}" data-model="{{ $product->visible_variant_sku ?? $product->sku ?? 'N/A' }}" data-image="{{ $imageUrl }}">
                                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                            <circle cx="8" cy="20" r="1.5"></circle>
                                                            <circle cx="18" cy="20" r="1.5"></circle>
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10V6m-2 2h4"></path>
                                                        </svg>
                                                    </a>
                                                @else
                                                    <button type="button" class="js-add-to-cart flex h-11 w-full min-w-0 items-center justify-center rounded-xl bg-primary-600 text-white shadow-lg shadow-primary-600/20 transition hover:bg-primary-700 hover:shadow-xl hover:shadow-primary-600/25 hover-lift cursor-pointer" title="Add to Cart" data-product-id="{{ $product->id }}" data-variant-id="{{ $variantId ?? '' }}" data-quantity="{{ $defaultQuantity }}" data-product-name="{{ e((string) ($product->name ?? '')) }}" data-unit-price="{{ $price }}" data-model="{{ $product->visible_variant_sku ?? $product->sku ?? 'N/A' }}" data-image="{{ $imageUrl }}">
                                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                            <circle cx="8" cy="20" r="1.5"></circle>
                                                            <circle cx="18" cy="20" r="1.5"></circle>
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10V6m-2 2h4"></path>
                                                        </svg>
                                                    </button>
                                                @endguest
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif

                    <x-ui.pagination :paginator="$products" class="pt-4" />
                </div>
            </section>
        </form>
    </div>
</div>

@foreach ($productCollection as $product)
    {{-- Step 4: keep one dedicated MVC form per catalog product for the buy-now submit action. --}}
    <form id="catalogBuyNowForm{{ $product->id }}" method="POST" action="{{ auth()->check() ? route('checkout.buy-now') : route('guest.checkout.buy-now') }}" class="hidden">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <input type="hidden" name="product_variant_id" value="{{ $product->visible_variant_id ?? '' }}">
        {{-- Step 5: submit the catalog buy-now action with the real backend minimum order quantity so the buyer can move to checkout without quantity validation failure. --}}
        <input type="hidden" name="quantity" value="{{ max(1, (int) ($product->visible_min_order_quantity ?? 1)) }}" data-catalog-buy-now-quantity="{{ $product->id }}">
    </form>
@endforeach

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const root = document.documentElement;
            const form = document.getElementById('catalogFiltersForm');
            const range = document.getElementById('catalogPriceRange');
            const label = document.getElementById('catalogPriceLabel');
            const sort = document.getElementById('catalogSort');
            const openFilters = document.getElementById('catalogFiltersOpen');
            const closeFilters = document.getElementById('catalogFiltersClose');
            const backdrop = document.getElementById('catalogMobileBackdrop');
            const sidebar = document.getElementById('catalogSidebar');
            const loadingOverlay = document.getElementById('catalogLoadingOverlay');

            const isDesktop = function () {
                return window.matchMedia && window.matchMedia('(min-width: 1280px)').matches;
            };

            const setMobileFiltersOpen = function (open) {
                document.body.classList.toggle('overflow-hidden', Boolean(open));

                if (backdrop) {
                    backdrop.classList.toggle('pointer-events-auto', Boolean(open));
                    backdrop.classList.toggle('opacity-100', Boolean(open));
                    backdrop.classList.toggle('pointer-events-none', !open);
                    backdrop.classList.toggle('opacity-0', !open);
                }

                if (sidebar && !isDesktop()) {
                    sidebar.classList.toggle('hidden', !open);
                    sidebar.classList.toggle('fixed', Boolean(open));
                    sidebar.classList.toggle('inset-y-0', Boolean(open));
                    sidebar.classList.toggle('left-0', Boolean(open));
                    sidebar.classList.toggle('z-[70]', Boolean(open));
                    sidebar.classList.toggle('block', Boolean(open));
                    sidebar.classList.toggle('w-[min(92vw,24rem)]', Boolean(open));
                    sidebar.classList.toggle('overflow-y-auto', Boolean(open));
                    sidebar.classList.toggle('rounded-r-[28px]', Boolean(open));
                    sidebar.classList.toggle('rounded-l-none', Boolean(open));
                    sidebar.classList.toggle('shadow-[0_36px_90px_rgba(26,30,26,0.18)]', Boolean(open));
                }
            };

            if (openFilters) {
                openFilters.addEventListener('click', function () {
                    setMobileFiltersOpen(true);
                });
            }

            if (closeFilters) {
                closeFilters.addEventListener('click', function () {
                    setMobileFiltersOpen(false);
                });
            }

            if (backdrop) {
                backdrop.addEventListener('click', function () {
                    setMobileFiltersOpen(false);
                });
            }

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    setMobileFiltersOpen(false);
                }
            });

            const formatInr = function (value) {
                const stringValue = String(Math.round(Number(value)));
                if (stringValue.length <= 3) {
                    return 'Rs. ' + stringValue;
                }

                const lastThree = stringValue.slice(-3);
                const rest = stringValue.slice(0, -3).replace(/\B(?=(\d{2})+(?!\d))/g, ',');
                return 'Rs. ' + rest + ',' + lastThree;
            };

            if (range && label) {
                range.addEventListener('input', function () {
                    label.textContent = formatInr(range.value);
                });

                range.addEventListener('change', function () {
                    applyClientFilters();
                });
            }

            if (sort) {
                sort.addEventListener('change', function () {
                    applyClientFilters();
                });
            }

            const customSortDropdown = document.getElementById('customSortDropdown');
            const customSortButton = document.getElementById('customSortButton');
            const customSortMenu = document.getElementById('customSortMenu');
            const customSortOptions = document.querySelectorAll('.custom-sort-option');
            const customSortLabel = document.getElementById('customSortLabel');

            if (customSortButton && customSortMenu) {
                customSortButton.addEventListener('click', function (e) {
                    e.preventDefault();
                    customSortMenu.classList.toggle('hidden');
                });

                document.addEventListener('click', function(e) {
                    if (!customSortDropdown.contains(e.target)) {
                        customSortMenu.classList.add('hidden');
                    }
                });

                customSortOptions.forEach(option => {
                    option.addEventListener('click', function(e) {
                        e.preventDefault();
                        const val = this.getAttribute('data-sort-value');
                        const sortLabels = {
                            'relevant': 'Most Relevant',
                            'name_az': 'Name A-Z',
                            'price_low': 'Price Low to High',
                            'price_high': 'Price High to Low'
                        };
                        if (sort.value !== val) {
                            sort.value = val;
                            if (customSortLabel) {
                                customSortLabel.textContent = sortLabels[val] || val;
                            }
                            // Update active styling
                            customSortOptions.forEach(o => {
                                o.classList.remove('bg-primary-50', 'text-primary-700');
                                o.classList.add('text-slate-700');
                            });
                            this.classList.add('bg-primary-50', 'text-primary-700');
                            this.classList.remove('text-slate-700');
                            applyClientFilters();
                        }
                        customSortMenu.classList.add('hidden');
                    });
                });
            }

            document.querySelectorAll('.catalog-auto').forEach(function (input) {
                input.addEventListener('change', function () {
                    applyClientFilters();
                });
            });

            // ─── Client-side filter/sort engine ───
            const productGrid = document.querySelector('[data-catalog-product-grid]');
            const allCards = productGrid ? Array.from(productGrid.querySelectorAll('[data-catalog-product-card]')) : [];
            const productSummaryEl = document.querySelector('[data-catalog-product-grid]')?.closest('section')?.querySelector('.text-sm.font-medium.text-slate-500');

            function applyClientFilters() {
                // 1. Read checked categories
                const checkedCategories = Array.from(document.querySelectorAll('input[name="category_name[]"]'))
                    .filter(cb => cb.checked)
                    .map(cb => cb.value.toLowerCase());

                // 2. Read checked subcategories
                const checkedSubcategories = Array.from(document.querySelectorAll('input[name="application_name[]"]'))
                    .filter(cb => cb.checked)
                    .map(cb => cb.value.toLowerCase());

                // 3. Read checked brands
                const checkedBrands = Array.from(document.querySelectorAll('input[name="brand_name[]"]'))
                    .filter(cb => cb.checked)
                    .map(cb => cb.value.toLowerCase());

                // 4. Read max price
                const maxPriceValue = range ? parseFloat(range.value) : Infinity;
                const maxPriceMax = range ? parseFloat(range.max) : Infinity;

                // 5. Read sort
                const sortValue = sort ? sort.value : 'relevant';

                // 6. Filter cards
                let visibleCount = 0;
                allCards.forEach(function (card) {
                    const category = card.dataset.productCategory || '';
                    const subcategory = card.dataset.productSubcategory || '';
                    const brand = card.dataset.productBrand || '';
                    const price = parseFloat(card.dataset.productPrice) || 0;

                    const matchesCategory = checkedCategories.length === 0 || checkedCategories.includes(category);
                    const matchesSubcategory = checkedSubcategories.length === 0 || checkedSubcategories.includes(subcategory);
                    const matchesBrand = checkedBrands.length === 0 || checkedBrands.includes(brand);
                    const matchesPrice = maxPriceValue >= maxPriceMax || price <= maxPriceValue;

                    const isVisible = matchesCategory && matchesSubcategory && matchesBrand && matchesPrice;
                    card.style.display = isVisible ? '' : 'none';
                    card._isVisible = isVisible;
                    if (isVisible) visibleCount++;
                });

                // 7. Sort visible cards
                if (productGrid) {
                    const sorted = allCards.slice().sort(function (a, b) {
                        if (!a._isVisible && !b._isVisible) return 0;
                        if (!a._isVisible) return 1;
                        if (!b._isVisible) return -1;

                        if (sortValue === 'name_az') {
                            return (a.dataset.productName || '').localeCompare(b.dataset.productName || '');
                        } else if (sortValue === 'price_low') {
                            return (parseFloat(a.dataset.productPrice) || 0) - (parseFloat(b.dataset.productPrice) || 0);
                        } else if (sortValue === 'price_high') {
                            return (parseFloat(b.dataset.productPrice) || 0) - (parseFloat(a.dataset.productPrice) || 0);
                        }
                        return parseInt(a.dataset.originalIndex || '0') - parseInt(b.dataset.originalIndex || '0');
                    });

                    sorted.forEach(function (card) {
                        productGrid.appendChild(card);
                    });
                }

                // 8. Update counter
                if (productSummaryEl) {
                    const word = visibleCount === 1 ? 'product' : 'products';
                    productSummaryEl.textContent = visibleCount + ' ' + word + ' available';
                }

                // Hide/show empty state
                const emptyState = document.querySelector('[data-catalog-product-grid]')?.closest('.grid.min-w-0')?.querySelector('.rounded-\\[28px\\]');
                // We'll just let CSS handle it — hidden cards are display:none
            }

            // Mobile "Apply Filters" button
            const mobileApplyBtn = document.getElementById('catalogMobileApplyFilters');
            if (mobileApplyBtn) {
                mobileApplyBtn.addEventListener('click', function () {
                    applyClientFilters();
                    setMobileFiltersOpen(false);
                });
            }

            if (form) {
                form.addEventListener('submit', function (e) {
                    // Only allow submission for search (text input). Block filter-driven submissions.
                    const activeEl = document.activeElement;
                    const isSearchSubmit = activeEl && (activeEl.type === 'search' || activeEl.type === 'submit');
                    if (!isSearchSubmit) {
                        e.preventDefault();
                        return;
                    }
                    setMobileFiltersOpen(false);
                    if (loadingOverlay) {
                        loadingOverlay.classList.remove('pointer-events-none', 'opacity-0');
                        loadingOverlay.classList.add('pointer-events-auto', 'opacity-100');
                    }
                });
            }

            const toastHost = document.getElementById('uiToastHost');
            const loginUrl = @json(route('login'));
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
                    primary.className = 'rounded-xl bg-primary-600 px-3 py-2 text-[13px] font-bold text-white shadow-[0_14px_26px_rgba(35,131,235,0.2)] transition hover:-translate-y-px hover:bg-primary-700';
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

            document.querySelectorAll('[data-catalog-hover-card]').forEach(function (card) {
                const image = card.querySelector('[data-catalog-hover-image]');
                if (!image) {
                    return;
                }

                let galleryImages = [];

                try {
                    galleryImages = JSON.parse(image.dataset.hoverImages || '[]');
                } catch (error) {
                    galleryImages = [];
                }

                galleryImages = galleryImages.filter(function (value) {
                    return Boolean(String(value || '').trim());
                });

                if (galleryImages.length < 2) {
                    return;
                }

                const baseAlt = String(image.dataset.hoverAlt || image.alt || 'Product image');
                let activeIndex = 0;
                let hoverTimer = null;
                let fadeTimer = null;

                const clearFadeTimer = function () {
                    if (fadeTimer) {
                        window.clearTimeout(fadeTimer);
                        fadeTimer = null;
                    }
                };

                const updateCardImage = function (index) {
                    activeIndex = ((index % galleryImages.length) + galleryImages.length) % galleryImages.length;
                    clearFadeTimer();
                    image.classList.add('opacity-70');
                    image.src = galleryImages[activeIndex];
                    image.alt = activeIndex === 0 ? baseAlt : baseAlt + ' alternate view ' + activeIndex;

                    fadeTimer = window.setTimeout(function () {
                        image.classList.remove('opacity-70');
                    }, 180);
                };

                const stopHoverRotation = function () {
                    if (hoverTimer) {
                        window.clearInterval(hoverTimer);
                        hoverTimer = null;
                    }

                    if (activeIndex !== 0) {
                        updateCardImage(0);
                    }
                };

                card.addEventListener('mouseenter', function () {
                    if (hoverTimer) {
                        return;
                    }

                    hoverTimer = window.setInterval(function () {
                        updateCardImage(activeIndex + 1);
                    }, 2000);
                });

                card.addEventListener('mouseleave', function () {
                    stopHoverRotation();
                });
            });

            document.querySelectorAll('[data-catalog-quantity-control]').forEach(function (quantityControl) {
                const productId = String(quantityControl.dataset.productId || '');
                const minQuantity = Math.max(1, Number(quantityControl.dataset.minQuantity || 1));
                const maxQuantityRaw = Number(quantityControl.dataset.maxQuantity || 0);
                const maxQuantity = maxQuantityRaw > 0 ? maxQuantityRaw : null;
                const lotSize = Math.max(1, Number(quantityControl.dataset.lotSize || 1));
                const quantityValue = quantityControl.querySelector('[data-catalog-quantity-value]');
                const productCard = quantityControl.closest('[data-catalog-product-card]');
                const decreaseButtons = quantityControl.querySelectorAll('[data-catalog-qty-button][data-direction="-1"]');
                const increaseButtons = quantityControl.querySelectorAll('[data-catalog-qty-button][data-direction="1"]');
                let quantity = minQuantity;

                const syncSelectedQuantity = function () {
                    // Step 4: keep the visible quantity, add-to-cart buttons, and buy-now form aligned with the same selected quantity.
                    if (maxQuantity !== null) {
                        quantity = Math.min(quantity, maxQuantity);
                    }

                    if (quantityValue) {
                        quantityValue.textContent = new Intl.NumberFormat('en-IN').format(quantity);
                    }

                    if (productCard) {
                        productCard.querySelectorAll('.js-add-to-cart').forEach(function (button) {
                            button.dataset.quantity = String(quantity);
                        });

                        // Step 5: update all product detail links within this card to include the current quantity.
                        productCard.querySelectorAll('[data-catalog-detail-link]').forEach(function (link) {
                            const baseUrl = link.dataset.baseUrl || link.getAttribute('href').split('?')[0];
                            const url = new URL(baseUrl, window.location.origin);
                            url.searchParams.set('quantity', String(quantity));
                            link.href = url.toString();
                        });
                    }

                    const buyNowQuantityInput = document.querySelector('[data-catalog-buy-now-quantity="' + productId + '"]');
                    if (buyNowQuantityInput) {
                        buyNowQuantityInput.value = String(quantity);
                    }

                    const isAtMinimum = quantity <= minQuantity;
                    const isAtMaximum = maxQuantity !== null && quantity >= maxQuantity;

                    decreaseButtons.forEach(function (button) {
                        button.disabled = isAtMinimum;
                        button.classList.toggle('opacity-40', isAtMinimum);
                        button.classList.toggle('cursor-not-allowed', isAtMinimum);
                    });

                    increaseButtons.forEach(function (button) {
                        button.disabled = isAtMaximum;
                        button.classList.toggle('opacity-40', isAtMaximum);
                        button.classList.toggle('cursor-not-allowed', isAtMaximum);
                    });
                };

                syncSelectedQuantity();

                quantityControl.querySelectorAll('[data-catalog-qty-button]').forEach(function (button) {
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
            });

            document.querySelectorAll('.js-add-to-cart').forEach(function (button) {
                button.addEventListener('click', async function (event) {
                    const target = event.currentTarget;
                    const quantity = Math.max(1, Number(target.dataset.quantity || 1));
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
                            unitPrice: Number(target.dataset.unitPrice || 0),
                            name: productName,
                            model: target.dataset.model || '',
                            image: target.dataset.image || '',
                        });

                        // Step 2: keep the existing error handling simple for session and validation problems.
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

            window.addEventListener('resize', function () {
                if (isDesktop()) {
                    setMobileFiltersOpen(false);
                }
            });
        });
    </script>
@endpush
@endsection

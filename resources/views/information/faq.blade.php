@extends('layouts.app')

@section('title', 'Frequently Asked Questions')

@section('content')
@php
    $inputClass = 'h-11 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:ring-2 focus:ring-primary-600/40';
    $faqCategories = [
        ['key' => 'product-info', 'label' => 'Product Info'],
        ['key' => 'ordering-process', 'label' => 'Ordering Process'],
        ['key' => 'delivery-payment', 'label' => 'Delivery & Payment'],
    ];
    $hasDefaultOpen = $faqs->contains(fn ($faq) => (bool) $faq->is_default_open);
@endphp

<div class="min-h-screen bg-slate-50">
    <section class="relative overflow-hidden bg-primary-800 py-16 text-white md:py-24">
        <img src="{{ asset('upload/corousel/image4.jpg') }}" alt="FAQ Background" class="absolute inset-0 h-full w-full object-cover opacity-20" loading="lazy" decoding="async">
        <div class="absolute inset-0 bg-gradient-to-t from-primary-800/95 via-primary-800/70 to-primary-600/30"></div>
        <div class="relative z-10 mx-auto w-full max-w-none px-4 text-center sm:px-6 lg:px-8 xl:px-10">
            <h1 class="mx-auto max-w-4xl font-display text-4xl font-bold tracking-tight text-secondary-600 md:text-5xl lg:text-6xl">Frequently Asked Questions</h1>
            <p class="mx-auto mt-6 max-w-2xl text-base leading-8 text-secondary-600 md:text-lg">Got questions about products, ordering, or delivery? We&apos;ve got answers.</p>
        </div>
    </section>

    <section class="relative z-20 -mt-8">
        <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-8 xl:px-10">
            <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm md:p-8">
                <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
                    <div class="md:col-span-3">
                        <label for="faqSearch" class="mb-2 block text-sm font-semibold text-slate-700">Search FAQs</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            </span>
                            <input id="faqSearch" type="text" class="{{ $inputClass }} pl-10" placeholder="e.g. shipping time...">
                        </div>
                    </div>
                    <div class="mt-2 md:col-span-3">
                        <label class="mb-3 block text-sm font-semibold text-slate-700">Filter by Category</label>
                        <div id="faqFilterTabs" class="flex flex-wrap gap-2">
                            <button type="button" data-filter="all" class="rounded-full bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition">All Categories</button>
                            @foreach ($faqCategories as $faqCategory)
                                <button type="button" data-filter="{{ $faqCategory['key'] }}" class="rounded-full bg-slate-100 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-200">
                                    {{ $faqCategory['label'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-12">
        <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-8 xl:px-10">
            @if ($faqs->count())
                <div id="faqAccordion" class="space-y-6">
                    @foreach ($faqs as $faq)
                        @php
                            $categoryKey = \Illuminate\Support\Str::slug($faq->category);
                            $isOpen = (bool) $faq->is_default_open || (! $hasDefaultOpen && $loop->first);
                            $searchText = strtolower($faq->category . ' ' . $faq->question . ' ' . $faq->answer);
                        @endphp
                        <div
                            class="rounded-2xl border border-slate-200 bg-white p-2 shadow-sm transition hover:border-primary-200"
                            data-faq-item
                            data-faq-category="{{ $categoryKey }}"
                            data-faq-search-text="{{ $searchText }}"
                        >
                            <x-accordion :title="$faq->question" :open="$isOpen">
                                <p class="text-slate-600">{{ $faq->answer }}</p>
                            </x-accordion>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900">FAQ content is not available yet</h3>
                    <p class="mt-2 text-sm text-slate-500">Please check back shortly while our team publishes the latest help content.</p>
                </div>
            @endif

            <div id="faqEmptyState" class="mt-8 hidden rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-900">No results found</h3>
                <p class="mt-2 text-sm text-slate-500">We couldn&apos;t find any FAQs matching your search or filter. Try adjusting your keywords.</p>
                <div class="mt-6">
                    <a href="{{ route('contact') }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Contact Support Instead</a>
                </div>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('faqSearch');
        const tabs = document.querySelectorAll('#faqFilterTabs button');
        const items = Array.from(document.querySelectorAll('[data-faq-item]'));
        const emptyState = document.getElementById('faqEmptyState');
        let currentFilter = 'all';

        function normalize(value) {
            return (value || '').toLowerCase().trim();
        }

        function applyFilter() {
            const searchTerm = normalize(searchInput ? searchInput.value : '');
            let visibleCount = 0;

            items.forEach(function (item) {
                const category = item.getAttribute('data-faq-category') || '';
                const textForSearch = (item.getAttribute('data-faq-search-text') || '').toLowerCase();
                const categoryMatch = currentFilter === 'all' || currentFilter === category;
                const searchMatch = !searchTerm || textForSearch.includes(searchTerm);
                const visible = categoryMatch && searchMatch;

                item.classList.toggle('hidden', !visible);
                if (visible) {
                    visibleCount++;
                }
            });

            if (emptyState) {
                emptyState.classList.toggle('hidden', visibleCount !== 0);
            }
        }

        if (searchInput) {
            searchInput.addEventListener('input', applyFilter);
        }

        tabs.forEach(tab => {
            tab.addEventListener('click', function () {
                tabs.forEach(t => {
                    t.classList.remove('bg-primary-600', 'text-white');
                    t.classList.add('bg-slate-100', 'text-slate-600', 'hover:bg-slate-200');
                });

                this.classList.remove('bg-slate-100', 'text-slate-600', 'hover:bg-slate-200');
                this.classList.add('bg-primary-600', 'text-white');

                currentFilter = this.getAttribute('data-filter');
                applyFilter();
            });
        });

        applyFilter();
    });
</script>
@endpush
@endsection

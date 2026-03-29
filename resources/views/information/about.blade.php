@extends('layouts.app')

@section('title', 'About Us')

@section('content')
@php
    $cardClass = 'group relative overflow-hidden rounded-[var(--ui-radius-card)] border border-slate-200/80 bg-white/95 p-6 shadow-[var(--ui-shadow-card)] backdrop-blur transition-all duration-300 hover:-translate-y-1.5 hover:border-primary-100 hover:shadow-[var(--ui-shadow-panel)]';
    $accentCardClass = 'group relative overflow-hidden rounded-[var(--ui-radius-card)] border border-primary-100/70 bg-gradient-to-br from-white via-primary-50/60 to-white p-5 shadow-[var(--ui-shadow-card)] backdrop-blur md:p-7';
    $titleClass = 'font-display text-4xl font-bold tracking-tight text-secondary-600 md:text-5xl lg:text-6xl';
    $copyClass = 'mt-6 max-w-2xl text-base leading-8 text-secondary-600 md:text-lg';
    $primaryIconClass = 'mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-primary-50 text-primary-700 transition-colors group-hover:bg-primary-600 group-hover:text-white';
@endphp

<div class="min-h-screen bg-gradient-to-b from-white via-primary-50/20 to-white">
    <!-- Premium Hero Section -->
    <section class="relative overflow-hidden bg-primary-800 py-16 text-white md:py-24">
        <img src="{{ asset('upload/corousel/image4.jpg') }}" alt="Biogenix company profile" class="absolute inset-0 h-full w-full object-cover opacity-20" loading="lazy" decoding="async">
        <div class="absolute inset-0 bg-gradient-to-t from-primary-800/95 via-primary-800/70 to-primary-600/30"></div>
        <div class="relative z-10 mx-auto w-full max-w-none px-4 text-center sm:px-6 lg:px-8 xl:px-10">
            <h1 class="mx-auto max-w-5xl {{ $titleClass }}">
                Driving trustworthy diagnostics access across India.
            </h1>
            <p class="mx-auto {{ $copyClass }}">
                Biogenix blends product quality, responsive service, and healthcare domain expertise to support institutions, labs, and care providers.
            </p>
        </div>
    </section>

    <!-- Vision/Mission/Values -->
    <section class="relative overflow-hidden pt-14 pb-6 md:pt-20 md:pb-8">
        <div class="absolute inset-0 -z-10 origin-top-left -skew-y-3 bg-primary-100/35"></div>
        <div class="mx-auto w-full max-w-none px-4 sm:px-6 lg:px-8 xl:px-10">
            <x-ui.section-heading title="Our Philosophy" subtitle="Vision, Mission, and Values driving our long-term healthcare impact." center title-class="text-[2rem] font-extrabold md:text-[2.35rem]" />
            <div class="mt-10 grid grid-cols-1 gap-6 lg:grid-cols-3">
                <article class="{{ $cardClass }} p-7 md:p-8">
                    <div class="absolute top-0 right-0 p-5 opacity-20 transition-opacity group-hover:opacity-30">
                        <svg class="h-20 w-20 text-primary-600" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                    </div>
                    <h3 class="font-display text-2xl font-bold text-slate-950 relative z-10">Vision</h3>
                    <p class="relative z-10 mt-3 text-base leading-relaxed text-slate-600">To become the most trusted diagnostics partner for institutions and communities across India.</p>
                </article>
                <article class="{{ $accentCardClass }}">
                    <div class="absolute top-0 right-0 p-5 opacity-20 transition-opacity group-hover:opacity-30">
                        <svg class="h-20 w-20 text-primary-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 0l-2 2a1 1 0 101.414 1.414L8 10.414l1.293 1.293a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    </div>
                    <h3 class="font-display text-2xl font-bold text-slate-950 relative z-10">Mission</h3>
                    <p class="relative z-10 mt-3 text-base leading-relaxed text-slate-600">Deliver high-quality diagnostics products with dependable support and transparent fulfillment workflows.</p>
                </article>
                <article class="{{ $cardClass }} p-7 md:p-8">
                    <div class="absolute top-0 right-0 p-5 opacity-20 transition-opacity group-hover:opacity-30">
                        <svg class="h-20 w-20 text-primary-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A120.1 120.1 0 0010 1C5.586 1 1.732 3.943.458 8s5.128 7 9.542 7c4.414 0 8.268-2.943 9.542-7-1.274-4.057-5.064-7-9.542-7zm-4.502 6.002a2 2 0 11-4 0 2 2 0 014 0zm8.004 0a2 2 0 11-4 0 2 2 0 014 0zm-4.002 6.002a2 2 0 11-4 0 2 2 0 014 0h-4a2 2 0 114 0zM15 17h-2v2h2v-2zm-4 0h-2v2h2v-2zm-4 0H5v2h2v-2z" clip-rule="evenodd"/></svg>
                    </div>
                    <h3 class="font-display text-2xl font-bold text-slate-950 relative z-10">Core Values</h3>
                    <ul class="relative z-10 mt-3 space-y-2.5">
                        @foreach (['Clinical reliability', 'Customer commitment', 'Execution excellence', 'Continuous improvement'] as $val)
                            <li class="flex items-center text-slate-600">
                                <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-full bg-primary-100 text-primary-700">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                </span>
                                {{ $val }}
                            </li>
                        @endforeach
                    </ul>
                </article>
            </div>
        </div>
    </section>

    <!-- Modern Journey Timeline -->
    <section class="bg-gradient-to-b from-white via-primary-50/10 to-white pt-6 pb-16 md:pt-8 md:pb-24">
        <div class="mx-auto w-full max-w-5xl px-4 sm:px-6 lg:px-8 xl:px-10">
            <x-ui.section-heading title="Our Journey" subtitle="A focused path from regional diagnostic support to scalable healthcare operations." center title-class="text-[2rem] font-extrabold md:text-[2.35rem]" />
            
            <div class="mt-16 space-y-12">
                @foreach ([
                    ['year' => '2017', 'title' => 'Foundation', 'desc' => 'Biogenix launched with a mission to improve access to reliable diagnostic products in North India.', 'year_class' => 'text-primary-600', 'node_class' => 'bg-primary-500'],
                    ['year' => '2020', 'title' => 'Portfolio Expansion', 'desc' => 'Added advanced instruments, reagents, and consumables for wider clinical workflows.', 'year_class' => 'text-primary-600', 'node_class' => 'bg-primary-500'],
                    ['year' => '2023', 'title' => 'Digital Enablement', 'desc' => 'Introduced online catalog and quotation workflows for B2B/B2C customer journeys.', 'year_class' => 'text-primary-600', 'node_class' => 'bg-primary-500'],
                    ['year' => '2026', 'title' => 'Scale & Compliance', 'desc' => 'Strengthened quality controls, partner onboarding, and logistics-led fulfillment execution.', 'year_class' => 'text-primary-700', 'node_class' => 'bg-primary-600'],
                ] as $idx => $milestone)
                    <div class="relative flex flex-col md:flex-row items-center group">
                        <!-- Desktop Line -->
                        @if(!$loop->last)
                            <div class="hidden md:block absolute left-1/2 top-16 bottom-[-3rem] w-px bg-slate-200 -translate-x-1/2"></div>
                        @endif

                        <!-- Spacer for right-side items -->
                        @if($idx % 2 != 0)
                            <div class="hidden md:block md:w-1/2"></div>
                        @endif
                        
                        <div class="w-full md:w-1/2 {{ $idx % 2 == 0 ? 'md:text-right md:pr-12' : 'md:pl-12 mt-4 md:mt-0' }}">
                            <div class="rounded-[var(--ui-radius-card)] border border-slate-200/80 bg-white/95 p-6 shadow-[var(--ui-shadow-card)] backdrop-blur transition-all duration-700 opacity-0 translate-y-8 scroll-reveal-item hover:-translate-y-1 hover:border-primary-100 hover:shadow-[var(--ui-shadow-panel)]">
                                <p class="text-3xl font-bold tracking-tight {{ $milestone['year_class'] }}">{{ $milestone['year'] }}</p>
                                <h3 class="font-display mt-2 text-xl font-bold text-slate-950">{{ $milestone['title'] }}</h3>
                                <p class="mt-3 text-base text-slate-600">{{ $milestone['desc'] }}</p>
                            </div>
                        </div>

                        <!-- Spacer for left-side items -->
                        @if($idx % 2 == 0)
                            <div class="hidden md:block md:w-1/2"></div>
                        @endif
                        
                        <!-- Center Node -->
                        <div class="absolute left-1/2 z-10 hidden h-12 w-12 -translate-x-1/2 items-center justify-center rounded-full border-4 border-white text-white shadow-[var(--ui-shadow-card)] transform transition-all duration-700 opacity-0 scale-50 scroll-reveal-node group-hover:scale-125 md:flex {{ $milestone['node_class'] }}">
                             <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </div>
                        
                        <!-- Mobile spacing -->
                        <div class="md:hidden h-8 w-px bg-slate-200 my-2"></div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Leadership / Quality Split -->
    <section class="relative overflow-hidden bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 py-12 text-white md:py-20">
        <!-- Abstract Bg -->
        <div class="absolute top-0 right-0 -mr-48 -mt-48 h-96 w-96 rounded-full bg-white opacity-10 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -ml-48 -mb-48 h-96 w-96 rounded-full bg-white opacity-10 blur-3xl"></div>
        
        <div class="relative z-10 mx-auto flex w-full max-w-4xl flex-col items-center px-4 text-center sm:px-6 lg:px-8">
            <h2 class="font-display text-3xl font-bold tracking-tight md:text-5xl">Quality & Compliance</h2>
            <p class="mt-5 max-w-2xl text-lg leading-8 text-white/90">Biogenix follows robust quality and compliance practices to ensure safe and consistent product delivery across all lines.</p>
            <div class="mt-9 grid w-full grid-cols-2 gap-4 md:grid-cols-4">
                @foreach (['ISO 13485', 'GMP Aligned', 'QA Audited', 'CDSCO Ready'] as $badge)
                    <div class="inline-flex cursor-default items-center justify-center gap-2.5 rounded-2xl border border-secondary-700/20 bg-secondary-600 px-6 py-3 text-sm font-semibold text-primary-900 shadow-[var(--ui-shadow-card)] transition md:text-base">
                        {{ $badge }}
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Leadership Team -->
    <section class="bg-gradient-to-b from-white via-primary-50/10 to-white py-12 md:py-20">
        <div class="mx-auto w-full max-w-none px-4 sm:px-6 lg:px-8 xl:px-10">
            <x-ui.section-heading title="Leadership Team" subtitle="Cross-functional leaders driving product quality, customer outcomes, and operational excellence." center title-class="text-[2rem] font-extrabold md:text-[2.35rem]" />
            
            <div class="mt-9 grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach ([
                    ['name' => 'Arjun Mehta', 'role' => 'Managing Director', 'copy' => 'Leads strategic growth, governance, and partnerships.'],
                    ['name' => 'Neha Srivastava', 'role' => 'Head - Clinical Solutions', 'copy' => 'Guides product fit and technical diagnostics enablement.'],
                    ['name' => 'Rahul Verma', 'role' => 'Head - Operations', 'copy' => 'Owns inventory planning, logistics, and service continuity.'],
                ] as $leader)
                <article class="group relative mx-auto w-full max-w-[29rem] overflow-hidden rounded-[var(--ui-radius-card)] border border-slate-200/80 bg-white/95 p-5 text-center shadow-[var(--ui-shadow-card)] backdrop-blur transition-all hover:-translate-y-1.5 hover:border-primary-100 hover:shadow-[var(--ui-shadow-panel)] md:p-6">
                        <div class="absolute inset-0 -z-0 translate-y-full bg-gradient-to-b from-primary-50/40 to-white transition-transform duration-300 group-hover:translate-y-0"></div>
                        <div class="relative z-10 flex flex-col items-center">
                            <div class="mb-3 h-[4.75rem] w-[4.75rem] overflow-hidden rounded-full shadow-lg shadow-primary-600/30 ring-[3px] ring-white">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($leader['name']) }}&background=ea580c&color=fff&size=200&bold=true" alt="{{ $leader['name'] }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110">
                            </div>
                            <h3 class="font-display text-2xl font-bold text-slate-950">{{ $leader['name'] }}</h3>
                            <p class="mt-2 inline-block rounded-full border border-primary-100 bg-primary-50 px-3.5 py-0.5 text-sm font-bold text-primary-700">{{ $leader['role'] }}</p>
                            <p class="mt-3 text-[0.95rem] text-slate-600">{{ $leader['copy'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    if (entry.target.classList.contains('scroll-reveal-item')) {
                        entry.target.classList.remove('opacity-0', 'translate-y-8');
                        entry.target.classList.add('opacity-100', 'translate-y-0');
                    }
                    if (entry.target.classList.contains('scroll-reveal-node')) {
                        entry.target.classList.remove('opacity-0', 'scale-50');
                        entry.target.classList.add('opacity-100', 'scale-100');
                    }
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.2 });

        document.querySelectorAll('.scroll-reveal-item, .scroll-reveal-node').forEach(el => observer.observe(el));
    });
</script>
@endpush
@endsection

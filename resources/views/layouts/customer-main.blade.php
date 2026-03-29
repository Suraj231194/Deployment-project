@extends('layouts.app')

@php
    $portal = auth()->user()?->user_type ?? request('user_type', request('portal', 'b2c'));
    $portal = $portal === 'b2b' ? 'b2b' : 'b2c';
    $isMinimal = trim($__env->yieldContent('customer_minimal')) === 'minimal';
    $title = trim($__env->yieldContent('customer_title', 'Customer Workspace'));
    $description = trim($__env->yieldContent('customer_description'));
@endphp

@section('content')
    @if (! $isMinimal)
        <div class="mx-auto w-full max-w-none px-4 py-4 sm:px-6 md:py-6 lg:px-8 xl:px-10">
            <x-ui.breadcrumb />
            <section class="mb-6 rounded-[2rem] border border-slate-200/80 bg-[radial-gradient(circle_at_top_right,rgba(47,143,255,0.18),transparent_26%),linear-gradient(135deg,#ffffff_0%,#f8fbff_55%,#eef5fd_100%)] p-6 shadow-[0_20px_60px_rgba(15,23,42,0.08)] md:mb-8 md:p-8">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div class="max-w-3xl">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">
                            {{ $portal === 'b2b' ? 'B2B Customer Workspace' : 'B2C Customer Workspace' }}
                        </p>
                        <h1 class="mt-3 text-3xl font-bold tracking-tight text-slate-950 md:text-4xl">
                            {{ $title }}
                        </h1>
                        @if ($description !== '')
                            <p class="mt-3 text-sm leading-7 text-slate-500 md:text-base">
                                {{ $description }}
                            </p>
                        @endif
                    </div>

                    @if (trim($__env->yieldContent('customer_actions')))
                        <div class="flex flex-wrap items-center gap-3">
                            @yield('customer_actions')
                        </div>
                    @endif
                </div>
            </section>
        </div>
    @endif

    <div>
        @yield('customer_content')
    </div>

    {{-- Loading skeleton overlay for AJAX transitions --}}
    <div id="customer-loading-skeleton" class="hidden absolute inset-0 z-10">
        <div class="space-y-6 animate-pulse p-1">
            <div class="h-8 bg-slate-200/60 rounded-xl w-2/5"></div>
            <div class="h-5 bg-slate-200/40 rounded-lg w-3/5"></div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                <div class="h-[130px] bg-slate-200/50 rounded-2xl"></div>
                <div class="h-[130px] bg-slate-200/50 rounded-2xl"></div>
                <div class="h-[130px] bg-slate-200/50 rounded-2xl hidden sm:block"></div>
                <div class="h-[130px] bg-slate-200/50 rounded-2xl hidden sm:block"></div>
            </div>
            <div class="h-[300px] bg-slate-200/40 rounded-2xl mt-4"></div>
        </div>
    </div>

    <style>
        @keyframes customerShimmer { 0% { background-position: -200% 0; } 100% { background-position: 200% 0; } }
        #customer-loading-skeleton .animate-pulse > div {
            background: linear-gradient(90deg, #e2e8f0 25%, #f1f5f9 50%, #e2e8f0 75%);
            background-size: 200% 100%; animation: customerShimmer 1.5s ease-in-out infinite;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const mainContent = document.getElementById('customer-main-content');
            const loadingSkeleton = document.getElementById('customer-loading-skeleton');
            const sidebar = document.getElementById('customer-sidebar');

            // Only initialize if sidebar and main content exist (i.e. we're on a workspace page)
            if (!mainContent || !sidebar) return;

            // ─── Reusable AJAX page loader ───
            const loadPage = async (url) => {
                // Show skeleton
                mainContent.style.position = 'relative';
                mainContent.classList.add('opacity-40');
                mainContent.style.transition = 'opacity 0.2s ease';
                loadingSkeleton.classList.remove('hidden');
                mainContent.prepend(loadingSkeleton);

                try {
                    const response = await fetch(url);
                    if (!response.ok) throw new Error('Network response was not ok');

                    const html = await response.text();
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');

                    const newContent = doc.getElementById('customer-main-content');
                    if (newContent) {
                        mainContent.innerHTML = newContent.innerHTML;

                        // Force execution of inline scripts
                        const scripts = mainContent.querySelectorAll('script');
                        scripts.forEach(oldScript => {
                            const newScript = document.createElement('script');
                            Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                            newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                            oldScript.parentNode.replaceChild(newScript, oldScript);
                        });

                        window.history.pushState({}, '', url);
                        document.title = doc.title;

                        // Smooth scroll to top
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                } catch (error) {
                    console.error('Customer SPA: failed to load page:', error);
                    window.location.href = url;
                } finally {
                    loadingSkeleton.classList.add('hidden');
                    mainContent.classList.remove('opacity-40');
                    mainContent.style.position = '';
                }
            };

            // ─── Update sidebar active state (admin dashboard style) ───
            const updateSidebarActive = (activeKey) => {
                sidebar.querySelectorAll('nav a[data-key]').forEach(link => {
                    const isActive = link.dataset.key === activeKey;

                    // Remove all state classes
                    link.classList.remove('bg-primary-600', 'text-white', 'font-bold');
                    link.classList.remove('text-slate-500', 'hover:bg-slate-50', 'hover:text-slate-800');

                    // Remove old active indicator
                    const indicator = link.querySelector('div.bg-white.rounded-r-md');
                    if (indicator) indicator.remove();

                    const svg = link.querySelector('svg');

                    if (isActive) {
                        link.classList.add('bg-primary-600', 'text-white', 'font-bold');
                        if (svg) { svg.classList.remove('text-slate-400'); svg.classList.add('text-indigo-200'); svg.setAttribute('stroke-width', '2.5'); }
                        // Add active indicator bar
                        const indicatorDiv = document.createElement('div');
                        indicatorDiv.className = 'absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-white rounded-r-md';
                        link.prepend(indicatorDiv);
                    } else {
                        link.classList.add('text-slate-500', 'hover:bg-slate-50', 'hover:text-slate-800');
                        if (svg) { svg.classList.remove('text-indigo-200'); svg.classList.add('text-slate-400'); svg.setAttribute('stroke-width', '2'); }
                    }
                });
            };

            // ─── Click interceptor for sidebar links ───
            sidebar.addEventListener('click', async (e) => {
                const link = e.target.closest('a[data-key]');
                if (!link || !link.href || link.getAttribute('href') === '#') return;

                const url = new URL(link.href);
                if (url.origin !== window.location.origin) return;

                e.preventDefault();

                // Update sidebar active state immediately
                updateSidebarActive(link.dataset.key);

                await loadPage(link.href);
            });

            // ─── Handle back/forward with AJAX ───
            window.addEventListener('popstate', async () => {
                await loadPage(window.location.href);

                // Determine the active key from URL
                const path = window.location.pathname;
                let activeKey = 'profile';
                if (path.includes('/addresses')) activeKey = 'addresses';
                else if (path.includes('/orders') || path.includes('/order')) activeKey = 'orders';
                else if (path.includes('/support')) activeKey = 'support';
                updateSidebarActive(activeKey);
            });
        });
    </script>
@endsection

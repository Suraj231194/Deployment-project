<!DOCTYPE html>
<html lang="en">
@php
    $authUser = auth()->user();
    $portal = $authUser?->user_type ?? request('user_type', request('portal', 'b2b'));
    $portal = $portal === 'b2c' ? 'b2c' : 'b2b';
    $searchValue = trim((string) $__env->yieldContent('storefront_search', request('search_text', request('search', ''))));
    $searchValue = $searchValue !== '' ? $searchValue : 'Search by SKU, Product Name, or Application...';
    $navLinkBaseClass = 'inline-flex items-center rounded-full px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900';
    $navLinkActiveClass = 'bg-primary-950 text-white hover:bg-primary-900 hover:text-white';
    $profileHref = $authUser
        ? route('customer.profile.preview', ['user_type' => $portal])
        : route('customer.profile.preview', ['user_type' => $portal]);
    $companyLinks = [
        ['label' => 'About Us', 'href' => route('about')],
        ['label' => 'Book Meeting', 'href' => route('book-meeting')],
        ['label' => 'Catalog', 'href' => route('products.index')],
        ['label' => 'Contact', 'href' => route('contact')],
    ];
    $resourceLinks = [
        ['label' => 'Technical Support', 'href' => route('contact')],
        ['label' => 'FAQ', 'href' => route('faq')],
        ['label' => 'Generate Quote', 'href' => route('quotation.create')],
        ['label' => 'Privacy', 'href' => route('privacy')],
    ];
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'Biogenix — Browse precision diagnostics, reagents, lab instruments, and life science research tools. Trusted by hospitals and laboratories across India.')">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Biogenix')</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('upload/icons/logo.jpg') }}?v=20260309">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Sora:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased">
    <div class="min-h-screen">
        <header class="border-b border-slate-200 bg-white/95 backdrop-blur">
            <div class="mx-auto flex w-full max-w-none items-center gap-6 px-4 py-4 sm:px-6 lg:px-8 xl:px-10">
                <a href="{{ route('home') }}" class="flex items-center gap-3 no-underline">
                    <div class="relative h-11 w-11 rounded-2xl bg-primary-600 shadow-sm">
                        <span class="absolute left-2.5 top-2.5 h-2.5 w-2.5 rounded-sm bg-white"></span>
                        <span class="absolute bottom-2.5 right-2.5 h-2.5 w-2.5 rounded-sm bg-white"></span>
                    </div>
                    <span class="text-xl font-semibold tracking-tight text-slate-950">Biogenix</span>
                </a>

                <form action="{{ route('products.index') }}" method="GET" class="hidden min-w-0 flex-1 lg:block">
                    <div class="relative max-w-xl">
                        <input
                            type="text"
                            name="search_text"
                            value="{{ $searchValue === 'Search by SKU, Product Name, or Application...' ? '' : $searchValue }}"
                            placeholder="Search by SKU, Product Name, or Application..."
                            class="block h-11 w-full rounded-2xl border border-slate-200 bg-slate-50 pl-12 pr-4 text-sm text-slate-900 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10"
                        >
                        <svg class="absolute left-4 top-1/2 h-4.5 w-4.5 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="m20 20-3.5-3.5"></path>
                        </svg>
                    </div>
                </form>

                <nav class="hidden items-center gap-1 lg:flex">
                    @php
                        $navItems = [
                            ['label' => 'Products', 'href' => route('products.index')],
                            ['label' => 'Solutions', 'href' => route('about')],
                            ['label' => 'Support', 'href' => route('contact')],
                        ];
                        $activeNav = trim($__env->yieldContent('storefront_nav', 'Products'));
                    @endphp
                    @foreach ($navItems as $navItem)
                        <a href="{{ $navItem['href'] }}" class="{{ $navLinkBaseClass }} {{ $activeNav === $navItem['label'] ? $navLinkActiveClass : '' }}">
                            {{ $navItem['label'] }}
                        </a>
                    @endforeach
                </nav>

                <a href="{{ route('checkout.page') }}" class="relative hidden h-10 w-10 items-center justify-center rounded-full text-slate-700 no-underline lg:inline-flex">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="9" cy="20" r="1"></circle>
                        <circle cx="18" cy="20" r="1"></circle>
                        <path d="M3 4h2l2.4 10.2a1 1 0 0 0 1 .8h9.8a1 1 0 0 0 1-.8L21 7H7"></path>
                    </svg>
                    <span id="storefrontCartBadge" class="absolute -right-0.5 -top-0.5 hidden h-4 w-4 items-center justify-center rounded-full bg-primary-600 text-xs font-semibold text-white">0</span>
                </a>

                <a href="{{ $profileHref }}" class="hidden h-11 w-11 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 no-underline shadow-sm transition hover:-translate-y-0.5 hover:border-primary-200 hover:text-primary-700 hover:shadow-md lg:inline-flex" aria-label="{{ $authUser ? 'Open profile' : 'Open profile preview' }}">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-current">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21a8 8 0 1 0-16 0"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </span>
                </a>
            </div>
        </header>

        <main>
            @yield('content')
        </main>

        <footer class="border-t border-slate-200 bg-white">
            <div class="mx-auto grid w-full max-w-none gap-10 px-4 py-12 sm:px-6 lg:grid-cols-4 lg:px-8 xl:px-10">
                <div class="space-y-5">
                    <div class="flex items-center gap-3">
                        <div class="relative h-10 w-10 rounded-xl bg-primary-600">
                            <span class="absolute left-2.5 top-2.5 h-2.5 w-2.5 rounded-sm bg-white"></span>
                            <span class="absolute bottom-2.5 right-2.5 h-2.5 w-2.5 rounded-sm bg-white"></span>
                        </div>
                        <span class="text-xl font-semibold tracking-tight text-slate-950">Biogenix</span>
                    </div>
                    <p class="max-w-xs text-sm leading-7 text-slate-500">
                        Advancing global healthcare through precision diagnostics and innovative life science research tools.
                    </p>
                </div>

                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-800">Company</h3>
                    <ul class="mt-5 space-y-3 text-sm text-slate-500">
                        @foreach ($companyLinks as $link)
                            <li><a href="{{ $link['href'] }}" class="text-inherit no-underline transition hover:text-primary-700">{{ $link['label'] }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-800">Resources</h3>
                    <ul class="mt-5 space-y-3 text-sm text-slate-500">
                        @foreach ($resourceLinks as $link)
                            <li><a href="{{ $link['href'] }}" class="text-inherit no-underline transition hover:text-primary-700">{{ $link['label'] }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-800">Connect</h3>
                    <div class="mt-5 flex gap-3">
                        @foreach (['globe', 'mail', 'phone'] as $icon)
                            <span class="flex h-11 w-11 items-center justify-center rounded-full bg-slate-50 text-slate-700 shadow-sm">
                                @if ($icon === 'globe')
                                    <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"></circle><path d="M3 12h18"></path><path d="M12 3a15 15 0 0 1 0 18"></path><path d="M12 3a15 15 0 0 0 0 18"></path></svg>
                                @elseif ($icon === 'mail')
                                    <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="m4 7 8 6 8-6"></path></svg>
                                @else
                                    <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.9.35 1.78.68 2.61a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.47-1.25a2 2 0 0 1 2.11-.45c.83.33 1.71.56 2.61.68A2 2 0 0 1 22 16.92z"></path></svg>
                                @endif
                            </span>
                        @endforeach
                    </div>
                    <p class="mt-7 text-sm text-slate-400">&copy; 2026 Biogenix Life Sciences. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
    @stack('scripts')
</body>
</html>

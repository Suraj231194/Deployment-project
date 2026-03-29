@php
    $currentRoute = request()->route()?->getName();
    $authUser = auth()->user();
    $guestProfileType = request('user_type', request('portal', 'b2c')) === 'b2b' ? 'b2b' : 'b2c';
    $profileHref = $authUser
        ? route('customer.profile.preview', ['user_type' => $authUser->user_type === 'b2b' ? 'b2b' : 'b2c'])
        : route('customer.profile.preview', ['user_type' => $guestProfileType]);
    $accountTypeLabel = $authUser
        ? strtoupper($authUser->user_type === 'b2b' ? 'B2B Account' : 'Customer Account')
        : strtoupper($guestProfileType === 'b2b' ? 'B2B Preview' : 'Customer Preview');
    $navItems = [
        ['label' => 'Home', 'route' => 'home', 'href' => route('home')],
        ['label' => 'Products & Solutions', 'route' => 'products.index', 'href' => route('products.index')],
        ['label' => 'Generate Quote', 'route' => 'quotation.create', 'href' => route('quotation.create')],
        ['label' => 'PI', 'route' => 'pi-quotation.generate', 'href' => route('pi-quotation.generate')],
        ['label' => 'Book Meeting', 'route' => 'book-meeting', 'href' => route('book-meeting')],
        ['label' => 'About Us', 'route' => 'about', 'href' => route('about')],
        ['label' => 'Contact Us', 'route' => 'contact', 'href' => route('contact')],
    ];
    $mobileQuickActions = [
        ['label' => 'My Profile', 'href' => $profileHref, 'icon' => 'profile'],
        ['label' => 'View Cart', 'href' => '#', 'icon' => 'cart', 'onclick' => 'openCartSidebar'],
        ['label' => 'Generate Quote', 'href' => route('quotation.create'), 'icon' => 'quote'],
        ['label' => 'Support', 'href' => route('contact'), 'icon' => 'support'],
    ];
@endphp

<header class="glass-header sticky top-0 z-[100]">
    <style>
        /* Fluid navbar sizing that scales with viewport */
        .header-nav-link {
            padding: clamp(0.35rem, 0.45vw, 0.5rem) clamp(0.4rem, 0.65vw, 0.75rem);
            font-size: clamp(0.68rem, 0.72vw, 0.875rem);
        }
        #headerMainNav {
            gap: clamp(0rem, 0.18vw, 0.25rem);
        }
        @media (min-width: 1280px) and (max-width: 1535px) {
            #headerDesktopActions {
                gap: 0.5rem;
            }
            .header-nav-link {
                padding: 0.4rem 0.4rem;
                font-size: 0.74rem;
            }
            .header-auth-button {
                min-height: 2.5rem;
                padding-inline: 1rem;
                font-size: 0.8125rem;
            }
            .header-cart-button {
                gap: 0.45rem;
                padding: 0.55rem 0.8rem;
            }
            .header-cart-label {
                font-size: 0.8125rem;
            }
            .header-profile-button {
                height: 2.5rem;
                width: 2.5rem;
            }
        }
    </style>
    <div class="relative mx-auto flex min-h-[64px] w-full max-w-none items-center gap-4 px-4 py-1 sm:px-6 sm:py-1.5 xl:grid xl:grid-cols-[auto_minmax(0,1fr)_auto] xl:items-center xl:gap-4 xl:px-6 2xl:gap-6 2xl:px-10"
    style="background: radial-gradient(circle at 15% 20%, rgba(255, 106, 0, 0.08), transparent 24%),
            radial-gradient(circle at 88% 10%, rgba(26, 77, 46, 0.08), transparent 18%),
            linear-gradient(180deg, #ffffff 0%, #f9faf9 100%)"
    >
        <a href="{{ route('home') }}" class="shrink-0 xl:col-start-1">
            <img src="{{ asset('upload/icons/biogenixlogo5.PNG') }}" alt="Biogenix Logo" width="120" height="64" decoding="async" class="h-12 w-auto xl:h-14 2xl:h-16">
        </a>

        {{-- Mobile hamburger --}}
        <button
            class="ml-auto inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:bg-slate-50 xl:hidden"
            data-menu-toggle
            aria-label="Open navigation menu"
            aria-expanded="false"
            aria-controls="mobileMenuDrawer"
        >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        {{-- Desktop nav --}}
        <nav
            id="headerMainNav"
            class="hidden items-center justify-self-center xl:col-start-2 xl:flex xl:min-w-0"
            aria-label="Main Navigation"
        >
            @foreach ($navItems as $nav)
                <a
                    href="{{ $nav['href'] }}"
                    class="header-nav-link relative whitespace-nowrap rounded-lg font-semibold no-underline transition {{ $currentRoute === $nav['route'] ? 'bg-primary-50 text-primary-700 shadow-sm hover:text-primary-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
                >
                    {{ $nav['label'] }}
                </a>
            @endforeach
        </nav>

        {{-- Desktop auth & cart --}}
        <div id="headerDesktopActions" class="ml-auto hidden items-center gap-2 xl:col-start-3 xl:flex xl:justify-self-end">
            @auth
                <span class="hidden max-w-[12rem] truncate text-sm text-slate-600 2xl:inline-block 2xl:max-w-[14rem]">{{ auth()->user()->name }} ({{ strtoupper(auth()->user()->user_type) }})</span>
                <form method="POST" action="{{ route('logout') }}" class="inline-block">
                    @csrf
                    <button type="submit" id="logoutBtn" class="header-auth-button hover-lift inline-flex h-10 cursor-pointer items-center justify-center rounded-xl border border-slate-300 bg-white px-4 text-[13px] font-semibold text-slate-700 shadow-sm transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-700 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-500/20 2xl:h-11 2xl:px-5 2xl:text-sm">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" id="loginBtn" class="header-auth-button inline-flex h-10 items-center justify-center rounded-xl border border-primary-600 bg-primary-600 px-4 text-[13px] font-semibold text-white shadow-sm transition hover:bg-primary-700 2xl:h-11 2xl:px-5 2xl:text-sm">Login</a>
                <a href="{{ route('signup') }}" id="signupBtn" class="header-auth-button inline-flex h-10 items-center justify-center rounded-xl border border-primary-200 bg-white px-4 text-[13px] font-semibold text-primary-700 shadow-sm transition hover:bg-primary-50 hover:text-primary-800 hover:border-primary-300 2xl:h-11 2xl:px-5 2xl:text-sm">Sign Up</a>
            @endauth

            <button
                type="button"
                onclick="if(typeof openCartSidebar==='function')openCartSidebar()"
                class="header-cart-button inline-flex items-center gap-2 rounded-2xl border border-primary-100 bg-primary-50 px-3 py-2 text-primary-700 shadow-sm transition hover:-translate-y-0.5 hover:border-primary-200 hover:bg-primary-100 hover:text-primary-800 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-600/20 cursor-pointer 2xl:gap-2.5 2xl:px-3.5 2xl:py-2.5"
                aria-label="View cart"
            >
                <span class="relative inline-flex h-7 w-7 items-center justify-center text-inherit">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <circle cx="9" cy="21" r="1.4"></circle>
                        <circle cx="17" cy="21" r="1.4"></circle>
                        <path d="M5 6h2l1.4 8h12.1l1.5-6H8"></path>
                    </svg>
                    <span
                        data-cart-count
                        class="absolute -right-2 -top-1 hidden h-5 min-w-5 items-center justify-center rounded-full bg-rose-600 px-1.5 text-xs font-bold leading-none text-white shadow-sm"
                        aria-live="polite"
                        aria-atomic="true"
                    >0</span>
                </span>
                <span class="header-cart-label text-[13px] font-bold leading-none text-inherit 2xl:text-sm">Cart</span>
            </button>

            {{-- Profile icon --}}
            <a
                href="{{ $profileHref }}"
                class="header-profile-button hover-lift inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-primary-100 bg-primary-50 text-primary-700 no-underline shadow-sm transition hover:border-primary-200 hover:bg-primary-100 hover:text-primary-800 hover:shadow-md hover:no-underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-600/20 2xl:h-11 2xl:w-11"
                aria-label="{{ $authUser ? 'Open account profile' : 'Open account preview' }}"
                title="Profile"
            >
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                    <circle cx="12" cy="8" r="4"></circle>
                    <path d="M5.5 21a7.5 7.5 0 0113 0"></path>
                </svg>
            </a>
        </div>
    </div>
</header>

<div id="mobileMenuOverlay" class="fixed inset-0 z-[70] hidden xl:hidden" aria-hidden="true">
    <button id="mobileMenuBackdrop" type="button" class="absolute inset-0 bg-primary-950/45 opacity-0 backdrop-blur-sm transition duration-300" aria-label="Close mobile menu"></button>

    <aside
        id="mobileMenuDrawer"
        class="absolute inset-y-0 right-0 flex w-[min(92vw,25rem)] max-w-full translate-x-full flex-col bg-white shadow-[0_24px_80px_rgba(26,77,46,0.15)] transition duration-300 ease-out"
        role="dialog"
        aria-modal="true"
        aria-labelledby="mobileMenuTitle"
    >
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
            <div class="flex items-center gap-3">
                <img src="{{ asset('upload/icons/logo.jpg') }}" alt="Biogenix Logo" width="40" height="40" decoding="async" class="h-10 w-10 rounded-2xl object-cover">
                <div>
                    <p id="mobileMenuTitle" class="text-base font-semibold tracking-tight text-primary-800">Biogenix Menu</p>
                    <p class="text-xs font-medium text-slate-400">Mobile navigation</p>
                </div>
            </div>

            <button
                id="mobileMenuClose"
                type="button"
                class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:bg-slate-50"
                aria-label="Close mobile menu"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="flex-1 space-y-4 overflow-y-auto px-4 py-4">
            <section class="rounded-[28px] border border-primary-100 bg-[linear-gradient(145deg,#f0faf4_0%,#d1f0dd_100%)] p-4 shadow-sm">
                <div class="flex items-start gap-3">
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-primary-700 shadow-sm">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <circle cx="12" cy="8" r="4"></circle>
                            <path d="M5.5 21a7.5 7.5 0 0113 0"></path>
                        </svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-base font-semibold text-slate-950">{{ $authUser?->name ?? 'Welcome to Biogenix' }}</p>
                        <p class="mt-1 truncate text-sm text-slate-500">{{ $authUser?->email ?? 'Browse products, quotes, and support from one place.' }}</p>
                        <span class="mt-3 inline-flex rounded-full bg-white/80 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-primary-700">
                            {{ $accountTypeLabel }}
                        </span>
                    </div>
                </div>

                <div class="mt-4 grid gap-2 sm:grid-cols-2">
                    @auth
                        <a href="{{ $profileHref }}" class="inline-flex h-11 items-center justify-center rounded-2xl bg-slate-950 px-4 text-sm font-semibold text-white no-underline transition hover:bg-primary-700 hover:text-white">
                            Open Profile
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="hover-lift inline-flex h-11 w-full cursor-pointer items-center justify-center rounded-2xl border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-700 hover:shadow-md">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex h-11 items-center justify-center rounded-2xl bg-primary-600 px-4 text-sm font-semibold text-white no-underline transition hover:bg-primary-700 hover:text-white">
                            Login
                        </a>
                        <a href="{{ route('signup') }}" class="inline-flex h-11 items-center justify-center rounded-2xl border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 no-underline shadow-sm transition hover:bg-slate-50 hover:text-slate-900">
                            Sign Up
                        </a>
                    @endauth
                </div>
            </section>

            <section class="rounded-[26px] border border-slate-200 bg-white p-3 shadow-sm">
                <div class="mb-3 flex items-center justify-between px-1">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Navigate</p>
                    <span class="text-xs font-medium text-slate-400">{{ count($navItems) }} links</span>
                </div>

                <div class="space-y-2">
                    @foreach ($navItems as $nav)
                        <a
                            href="{{ $nav['href'] }}"
                            class="flex items-center justify-between rounded-2xl px-3 py-3 text-sm font-semibold no-underline transition {{ $currentRoute === $nav['route'] ? 'bg-primary-50 text-primary-700 hover:text-primary-700' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}"
                        >
                            <span>{{ $nav['label'] }}</span>
                            <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="m9 6 6 6-6 6"></path>
                            </svg>
                        </a>
                    @endforeach
                </div>
            </section>

            <section class="rounded-[26px] border border-slate-200 bg-white p-3 shadow-sm">
                <div class="mb-3 flex items-center justify-between px-1">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Quick Actions</p>
                    <span class="text-xs font-medium text-slate-400">Fast access</span>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    @foreach ($mobileQuickActions as $action)
                        @if (isset($action['onclick']))
                            <button type="button" onclick="if(typeof openCartSidebar==='function'){openCartSidebar();}var cm=document.getElementById('mobileMenuClose');if(cm){cm.click();}" class="rounded-[22px] border border-slate-200 bg-slate-50 p-3 text-left transition hover:border-primary-200 hover:bg-primary-50 hover:text-slate-900 cursor-pointer">
                        @else
                            <a href="{{ $action['href'] }}" class="rounded-[22px] border border-slate-200 bg-slate-50 p-3 text-left no-underline transition hover:border-primary-200 hover:bg-primary-50 hover:text-slate-900">
                        @endif
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-white text-primary-700 shadow-sm">
                                @if ($action['icon'] === 'profile')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                        <circle cx="12" cy="8" r="4"></circle>
                                        <path d="M5.5 21a7.5 7.5 0 0113 0"></path>
                                    </svg>
                                @elseif ($action['icon'] === 'cart')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                        <circle cx="9" cy="20" r="1.4"></circle>
                                        <circle cx="17" cy="20" r="1.4"></circle>
                                        <path d="M5 6h2l1.4 8h12.1l1.5-6H8"></path>
                                    </svg>
                                @elseif ($action['icon'] === 'quote')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                        <path d="M3 5h18"></path>
                                        <path d="M7 3v4"></path>
                                        <path d="M17 3v4"></path>
                                        <rect x="4" y="7" width="16" height="13" rx="2"></rect>
                                        <path d="M8 11h8"></path>
                                    </svg>
                                @else
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                        <path d="M8 10h8"></path>
                                        <path d="M8 14h5"></path>
                                        <path d="M12 3c4.97 0 9 3.58 9 8 0 1.95-.78 3.74-2.07 5.16L20 21l-5.04-1.68A10.5 10.5 0 0 1 12 20c-4.97 0-9-3.58-9-8s4.03-9 9-9Z"></path>
                                    </svg>
                                @endif
                            </span>
                            <span class="mt-3 block text-sm font-semibold text-slate-900">{{ $action['label'] }}</span>
                        @if (isset($action['onclick']))
                            </button>
                        @else
                            </a>
                        @endif
                    @endforeach
                </div>
            </section>

            <section class="rounded-[26px] border border-slate-200 bg-slate-50 p-4 shadow-sm">
                <p class="text-sm font-semibold text-slate-950">Need procurement help?</p>
                <p class="mt-2 text-sm leading-6 text-slate-500">Talk to our team about availability, delivery windows, or commercial quotations.</p>
                <div class="mt-4 flex flex-col gap-2">
                    <a href="tel:+91180024643649" class="inline-flex h-11 items-center justify-center rounded-2xl border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 no-underline shadow-sm transition hover:bg-slate-100 hover:text-slate-900">
                        Call Support
                    </a>
                    <a href="mailto:support@biogenix.local" class="inline-flex h-11 items-center justify-center rounded-2xl bg-primary-600 px-4 text-sm font-semibold text-white no-underline transition hover:bg-primary-700 hover:text-white">
                        Email Support
                    </a>
                </div>
            </section>
        </div>
    </aside>
</div>

<script>
    window.CartStore = (function () {
        const shopperIsLoggedIn = @json(auth()->check());
        const accountCartShowUrl = @json(url('/cart/data'));
        const accountCartStoreUrl = @json(url('/cart/items'));
        const accountCartUpdateUrl = @json(url('/cart/items/__CART_ITEM__'));
        const accountCartDeleteUrl = @json(url('/cart/items/__CART_ITEM__'));
        const guestCartShowUrl = @json(url('/guest-cart/data'));
        const guestCartStoreUrl = @json(url('/guest-cart/items'));
        const guestCartUpdateUrl = @json(url('/guest-cart/items/__CART_ITEM__'));
        const guestCartDeleteUrl = @json(url('/guest-cart/items/__CART_ITEM__'));
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const listeners = [];
        let currentCartItems = [];
        let cartIsLoaded = false;
        let activeCartRequest = null;

        const normalizeQuantity = function (item) {
            const raw = item && (item.quantity ?? item.qty ?? item.count ?? 1);
            const parsed = Number(raw);
            return Number.isFinite(parsed) && parsed > 0 ? parsed : 1;
        };

        const getItems = function () {
            return currentCartItems.slice();
        };

        const notify = function (items = getItems()) {
            listeners.forEach(function (listener) {
                listener(items);
            });
        };

        const buildCurrentCartItem = function (item) {
            return {
                cartItemId: Number(item.id || 0),
                productId: Number(item.product_id || 0),
                variantId: item.product_variant_id == null ? null : Number(item.product_variant_id),
                quantity: normalizeQuantity(item),
                unitPrice: Number(item.unit_price || 0),
                unitTaxAmount: Number(item.unit_tax_amount || 0),
                unitPriceAfterGst: Number(item.unit_price_after_gst || 0),
                taxAmount: Number(item.tax_amount || 0),
                lineSubtotal: Number(item.line_subtotal || 0),
                lineTotal: Number(item.line_total || 0),
                discountAmount: Number(item.discount_amount || 0),
                currency: String(item.currency || 'INR'),
                priceType: item.price_type == null ? null : String(item.price_type),
                minOrderQuantity: Number(item.min_order_quantity || 1),
                maxOrderQuantity: item.max_order_quantity == null ? null : Number(item.max_order_quantity),
                lotSize: Number(item.lot_size || 1),
                name: String(item.product_name || 'Product'),
                model: String(item.sku || ''),
                image: String(item.image_url || ''),
            };
        };

        const applyCurrentCart = function (cart) {
            // Step 1: keep one shared in-memory cart so every storefront screen reads the same backend state.
            currentCartItems = Array.isArray(cart && cart.items) ? cart.items.map(buildCurrentCartItem) : [];
            cartIsLoaded = true;

            // Step 2: notify the active screens so the badge, sidebar, cart, and checkout stay in sync.
            notify(currentCartItems);

            return currentCartItems;
        };

        const buildRequestOptions = function (method, body) {
            // Step 1: send every cart request as JSON to the shared backend cart API.
            const options = {
                method: method,
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                },
            };

            // Step 2: include a JSON body only when the current cart action needs one.
            if (body !== undefined) {
                options.headers['Content-Type'] = 'application/json';
                options.body = JSON.stringify(body);
            }

            return options;
        };

        const requestCartEndpoint = async function (url, method, body) {
            // Step 1: call the backend cart endpoint and expect the standard cart controller JSON response.
            const response = await fetch(url, buildRequestOptions(method, body));
            const contentType = String(response.headers.get('content-type') || '');

            // Step 2: treat redirects and non-JSON replies as a login/session issue for the storefront.
            if (response.redirected || !contentType.includes('application/json')) {
                return { ok: false, type: 'auth', message: 'Please login again to continue.' };
            }

            // Step 3: read the payload once so callers can use either the updated cart or backend message.
            const data = await response.json().catch(function () {
                return null;
            });

            // Step 4: return a simple failure shape that every screen can understand.
            if (!response.ok) {
                return {
                    ok: false,
                    type: 'error',
                    message: data && data.message ? data.message : 'Unable to update cart right now.',
                    errors: data && data.errors ? data.errors : {},
                };
            }

            // Step 5: return the successful backend payload for the shared cart store to apply.
            return { ok: true, data: data };
        };

        const currentCartShowUrl = shopperIsLoggedIn ? accountCartShowUrl : guestCartShowUrl;
        const currentCartStoreUrl = shopperIsLoggedIn ? accountCartStoreUrl : guestCartStoreUrl;
        const currentCartUpdateUrl = shopperIsLoggedIn ? accountCartUpdateUrl : guestCartUpdateUrl;
        const currentCartDeleteUrl = shopperIsLoggedIn ? accountCartDeleteUrl : guestCartDeleteUrl;

        const ensureCartLoaded = function () {
            // Step 1: reuse the cart seed prepared by the controller when the current page already has it.
            if (!cartIsLoaded && Object.prototype.hasOwnProperty.call(window, '__BIOGENIX_PAGE_CART__')) {
                applyCurrentCart(window.__BIOGENIX_PAGE_CART__);
                return Promise.resolve({ ok: true, items: getItems() });
            }

            // Step 2: reuse the loaded cart so we do not make duplicate requests on every render.
            if (cartIsLoaded) {
                return Promise.resolve({ ok: true, items: getItems() });
            }

            // Step 3: reuse the in-flight request so multiple screens can wait for the same backend response.
            if (activeCartRequest) {
                return activeCartRequest;
            }

            // Step 4: load the current cart and cache it in memory for pages without a controller seed.
            activeCartRequest = requestCartEndpoint(currentCartShowUrl, 'GET').then(function (result) {
                if (result.ok) {
                    applyCurrentCart(result.data && result.data.cart ? result.data.cart : null);
                }

                return Object.assign({}, result, { items: getItems() });
            }).finally(function () {
                activeCartRequest = null;
            });

            return activeCartRequest;
        };

        const findCurrentCartItem = function (productId, variantId) {
            return currentCartItems.find(function (item) {
                return item.productId === productId && item.variantId === variantId;
            }) || null;
        };

        const getCount = function () {
            return getItems().reduce(function (sum, item) {
                return sum + normalizeQuantity(item);
            }, 0);
        };

        const addItem = function (newItem) {
            // Step 1: save the requested line into the active cart endpoint for the current shopper type.
            return requestCartEndpoint(currentCartStoreUrl, 'POST', {
                product_id: Number(newItem.productId || 0),
                product_variant_id: newItem.variantId == null ? null : Number(newItem.variantId),
                quantity: normalizeQuantity(newItem),
            }).then(function (result) {
                // Step 2: refresh the shared in-memory cart from the backend response after a successful add.
                if (result.ok) {
                    applyCurrentCart(result.data && result.data.cart ? result.data.cart : null);
                }

                return Object.assign({}, result, { items: getItems() });
            });
        };

        const updateQuantity = function (productId, variantId, quantity) {
            // Step 1: make sure the current cart is available before updating one line.
            return ensureCartLoaded().then(function (loadResult) {
                if (!loadResult.ok) {
                    return loadResult;
                }

                // Step 2: find the saved cart row that belongs to the selected product and variant.
                const target = findCurrentCartItem(productId, variantId);
                if (!target) {
                    return { ok: true, items: getItems() };
                }

                // Step 3: send the replacement quantity to the backend so validation stays server-driven.
                return requestCartEndpoint(
                    currentCartUpdateUrl.replace('__CART_ITEM__', String(target.cartItemId)),
                    'PATCH',
                    { quantity: Math.max(1, Number(quantity || 1)) }
                ).then(function (result) {
                    // Step 4: apply the refreshed backend cart so every open cart view stays accurate.
                    if (result.ok) {
                        applyCurrentCart(result.data && result.data.cart ? result.data.cart : null);
                    }

                    return Object.assign({}, result, { items: getItems() });
                });
            });
        };

        const removeItem = function (productId, variantId) {
            // Step 1: make sure the current cart is available before removing one line.
            return ensureCartLoaded().then(function (loadResult) {
                if (!loadResult.ok) {
                    return loadResult;
                }

                // Step 2: find the saved backend cart row that belongs to the selected product and variant.
                const target = findCurrentCartItem(productId, variantId);
                if (!target) {
                    return { ok: true, items: getItems() };
                }

                // Step 3: remove the selected line from the backend cart.
                return requestCartEndpoint(
                    currentCartDeleteUrl.replace('__CART_ITEM__', String(target.cartItemId)),
                    'DELETE'
                ).then(function (result) {
                    // Step 4: apply the refreshed backend cart so every open cart view stays in sync.
                    if (result.ok) {
                        applyCurrentCart(result.data && result.data.cart ? result.data.cart : null);
                    }

                    return Object.assign({}, result, { items: getItems() });
                });
            });
        };

        const clear = function () {
            return { ok: true, items: getItems() };
        };

        return {
            getItems: getItems,
            getCount: getCount,
            addItem: addItem,
            updateQuantity: updateQuantity,
            removeItem: removeItem,
            clear: clear,
            refresh: ensureCartLoaded,
            subscribe: function (listener) {
                // Step 1: register the listener so shared cart UI blocks can react to future cart updates.
                listeners.push(listener);
                listener(getItems());

                // Step 2: load the current cart after subscribe so the page gets the latest backend state.
                ensureCartLoaded();
            },
        };
    }());

    document.addEventListener('DOMContentLoaded', function () {
        // ─── Cart badge sync ───
        const cartCountBadges = Array.from(document.querySelectorAll('[data-cart-count]'));
        if (window.CartStore && cartCountBadges.length) {
            const syncCartBadge = function (items) {
                const total = items.reduce(function (sum, item) {
                    const raw = item && (item.quantity ?? item.qty ?? item.count ?? 1);
                    const parsed = Number(raw);
                    return sum + (Number.isFinite(parsed) && parsed > 0 ? parsed : 1);
                }, 0);
                cartCountBadges.forEach(function (cartCount) {
                    cartCount.textContent = total > 99 ? '99+' : String(total);
                    cartCount.classList.toggle('hidden', total <= 0);
                    cartCount.classList.toggle('inline-flex', total > 0);
                    cartCount.setAttribute('aria-label', total + ' items in cart');
                });
            };

            window.CartStore.subscribe(syncCartBadge);
            window.addEventListener('pageshow', function () {
                if (window.CartStore && typeof window.CartStore.refresh === 'function') {
                    window.CartStore.refresh();
                }

                syncCartBadge(window.CartStore.getItems());
            });
        }

        // ─── Improved mobile menu toggle ───
        const menuToggle = document.querySelector('[data-menu-toggle]');
        const menuOverlay = document.getElementById('mobileMenuOverlay');
        const menuDrawer = document.getElementById('mobileMenuDrawer');
        const menuBackdrop = document.getElementById('mobileMenuBackdrop');
        const menuClose = document.getElementById('mobileMenuClose');
        if (menuToggle && menuOverlay && menuDrawer && menuBackdrop && menuClose) {
            const mobileQuery = window.matchMedia('(max-width: 1279px)');

            const closeMenu = function () {
                menuBackdrop.classList.remove('opacity-100');
                menuBackdrop.classList.add('opacity-0');
                menuDrawer.classList.remove('translate-x-0');
                menuDrawer.classList.add('translate-x-full');
                menuToggle.setAttribute('aria-expanded', 'false');
                document.documentElement.classList.remove('overflow-hidden');
                document.body.classList.remove('overflow-hidden');
                window.setTimeout(function () {
                    if (!menuDrawer.classList.contains('translate-x-0')) {
                        menuOverlay.classList.add('hidden');
                    }
                }, 300);
            };

            const openMenu = function () {
                if (!mobileQuery.matches) {
                    return;
                }

                menuOverlay.classList.remove('hidden');
                menuToggle.setAttribute('aria-expanded', 'true');
                document.documentElement.classList.add('overflow-hidden');
                document.body.classList.add('overflow-hidden');
                window.requestAnimationFrame(function () {
                    menuBackdrop.classList.remove('opacity-0');
                    menuBackdrop.classList.add('opacity-100');
                    menuDrawer.classList.remove('translate-x-full');
                    menuDrawer.classList.add('translate-x-0');
                });
            };

            menuToggle.addEventListener('click', function () {
                if (menuOverlay.classList.contains('hidden')) {
                    openMenu();
                } else {
                    closeMenu();
                }
            });

            menuBackdrop.addEventListener('click', closeMenu);
            menuClose.addEventListener('click', closeMenu);

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && !menuOverlay.classList.contains('hidden')) {
                    closeMenu();
                }
            });

            menuDrawer.querySelectorAll('a').forEach(function (link) {
                link.addEventListener('click', function () {
                    if (mobileQuery.matches) {
                        closeMenu();
                    }
                });
            });

            const handleViewportChange = function () {
                if (!mobileQuery.matches) {
                    closeMenu();
                }
            };

            if (typeof mobileQuery.addEventListener === 'function') {
                mobileQuery.addEventListener('change', handleViewportChange);
            } else if (typeof mobileQuery.addListener === 'function') {
                mobileQuery.addListener(handleViewportChange);
            }

            menuBackdrop.classList.add('opacity-0');
            menuDrawer.classList.add('translate-x-full');
        }
    });
</script>

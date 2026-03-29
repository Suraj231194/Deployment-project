<div id="cartSidebarOverlay" class="pointer-events-none fixed inset-0 z-[100] hidden" aria-hidden="true">
    <button id="cartSidebarBackdrop" type="button" class="pointer-events-auto absolute inset-0 bg-slate-950/20 opacity-0 backdrop-blur-[1px] transition duration-300" aria-label="Close cart"></button>

    <aside
        id="cartSidebarDrawer"
        class="pointer-events-auto absolute inset-y-0 right-0 flex h-full w-full max-w-[26rem] translate-x-full flex-col overflow-hidden border-l border-slate-200 bg-white shadow-[-4px_0_24px_rgba(15,23,42,0.08)] transition duration-300 ease-[cubic-bezier(0.32,0.72,0,1)] sm:w-[26rem]"
        role="dialog"
        aria-modal="true"
        aria-labelledby="cartSidebarTitle"
    >
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
            <div class="flex items-center gap-3">
                <h2 id="cartSidebarTitle" class="text-lg font-bold text-slate-950">Your Cart</h2>
                <span id="cartSidebarBadge" class="inline-flex items-center justify-center rounded-full bg-primary-600 px-2.5 py-0.5 text-[10px] font-bold tracking-wider text-white">0 ITEMS</span>
            </div>
            <button
                id="cartSidebarClose"
                type="button"
                class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-700"
                aria-label="Close cart"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- <div class="mx-5 mt-3 flex shrink-0 items-center gap-3 rounded-2xl border border-primary-200 bg-[linear-gradient(135deg,#ecfdf5_0%,#d1fae5_100%)] px-4 py-3">
            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-primary-600 text-white">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                    <path d="M20 6 9 17l-5-5"></path>
                </svg>
            </span>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wide text-slate-900">Lucknow order detected</p>
                <p class="text-[10px] font-semibold text-primary-600">Eligible for Same-day Delivery!</p>
            </div>
        </div> -->

        <div id="cartSidebarItems" class="flex min-h-0 flex-1 flex-col gap-3 overflow-y-auto px-5 py-3"></div>

        <div id="cartSidebarEmpty" class="hidden flex-1 flex-col items-center justify-center px-5 py-10 text-center">
            <svg class="mx-auto h-12 w-12 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <circle cx="9" cy="21" r="1.4"></circle>
                <circle cx="17" cy="21" r="1.4"></circle>
                <path d="M5 6h2l1.4 8h12.1l1.5-6H8"></path>
            </svg>
            <p class="mt-4 text-sm font-semibold text-slate-700">Your cart is empty</p>
            <p class="mt-1 text-xs text-slate-500">Add products from the catalog to get started.</p>
            <a href="{{ route('products.index') }}" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-2xl bg-primary-600 px-5 text-sm font-semibold text-white no-underline shadow-md shadow-primary-600/30 transition hover:-translate-y-0.5 hover:text-white hover:shadow-md shadow-primary-600/30">
                Continue Shopping
            </a>
        </div>

        <div id="cartSidebarFooter" class="shrink-0 border-t border-slate-200 bg-[linear-gradient(180deg,rgba(255,255,255,0.94)_0%,#ffffff_24%)] px-5 pb-[calc(1.25rem+env(safe-area-inset-bottom,0px))] pt-5 shadow-[0_-18px_36px_rgba(15,23,42,0.06)]">
            <div class="space-y-2.5 text-sm">
                <div class="flex items-center justify-between text-slate-600">
                    <span>Subtotal</span>
                    <span id="cartSidebarSubtotal" class="font-semibold text-slate-900">Rs. 0.00</span>
                </div>
                <div class="flex items-center justify-between text-slate-600">
                    <span>GST (18%)</span>
                    <span id="cartSidebarTax" class="font-semibold text-slate-900">Rs. 0.00</span>
                </div>
                <div class="flex items-center justify-between text-slate-600">
                    <span>Delivery Charge</span>
                    <span class="font-bold text-primary-600">FREE</span>
                </div>
            </div>

            <div class="mt-4 border-t border-slate-200 pt-4">
                <div class="flex items-baseline justify-between">
                    <span class="text-sm font-bold text-secondary-600">Estimated Total</span>
                    <div class="text-right">
                        <span id="cartSidebarTotal" class="text-2xl font-extrabold tracking-tight text-secondary-600">Rs. 0.00</span>
                        <p class="mt-0.5 text-[10px] text-slate-400">Tax calculated for Other Product</p>
                    </div>
                </div>
            </div>

            <a
                id="cartSidebarCheckout"
                href="{{ route('checkout.page') }}"
                class="mt-4 inline-flex min-h-[3.25rem] w-full items-center justify-center rounded-2xl bg-primary-600 px-4 py-3 text-center text-sm font-bold text-white no-underline shadow-md shadow-primary-600/30 transition hover:-translate-y-0.5 hover:text-white hover:shadow-md shadow-primary-600/30 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-600/30"
            >
                Proceed to Checkout &rarr;
            </a>
        </div>
    </aside>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const overlay = document.getElementById('cartSidebarOverlay');
        const backdrop = document.getElementById('cartSidebarBackdrop');
        const drawer = document.getElementById('cartSidebarDrawer');
        const closeBtn = document.getElementById('cartSidebarClose');
        const itemsContainer = document.getElementById('cartSidebarItems');
        const emptyState = document.getElementById('cartSidebarEmpty');
        const footer = document.getElementById('cartSidebarFooter');
        const badge = document.getElementById('cartSidebarBadge');
        const subtotalEl = document.getElementById('cartSidebarSubtotal');
        const taxEl = document.getElementById('cartSidebarTax');
        const totalEl = document.getElementById('cartSidebarTotal');
        if (!overlay || !drawer || !itemsContainer || !badge || !subtotalEl || !taxEl || !totalEl) return;

        let previousHtmlOverflow = '';
        let previousBodyOverflow = '';
        let previousBodyPaddingRight = '';

        const syncPageScrollLock = function (open) {
            if (open) {
                previousHtmlOverflow = document.documentElement.style.overflow;
                previousBodyOverflow = document.body.style.overflow;
                previousBodyPaddingRight = document.body.style.paddingRight;

                const scrollbarWidth = Math.max(0, window.innerWidth - document.documentElement.clientWidth);

                document.documentElement.style.overflow = 'hidden';
                document.body.style.overflow = 'hidden';
                document.body.style.paddingRight = scrollbarWidth > 0 ? scrollbarWidth + 'px' : previousBodyPaddingRight;
                return;
            }

            document.documentElement.style.overflow = previousHtmlOverflow;
            document.body.style.overflow = previousBodyOverflow;
            document.body.style.paddingRight = previousBodyPaddingRight;
        };

        const revealSidebar = function () {
            if (!overlay.classList.contains('hidden')) return;
            overlay.classList.remove('hidden');
            overlay.setAttribute('aria-hidden', 'false');
            syncPageScrollLock(true);
            window.requestAnimationFrame(function () {
                drawer.classList.remove('translate-x-full');
                drawer.classList.add('translate-x-0');
                backdrop.classList.remove('opacity-0');
                backdrop.classList.add('opacity-100');
            });
        };

        const hideSidebar = function () {
            if (overlay.classList.contains('hidden')) return;
            drawer.classList.remove('translate-x-0');
            drawer.classList.add('translate-x-full');
            backdrop.classList.remove('opacity-100');
            backdrop.classList.add('opacity-0');
            overlay.setAttribute('aria-hidden', 'true');
            syncPageScrollLock(false);
            window.setTimeout(function () {
                if (drawer.classList.contains('translate-x-full')) {
                    overlay.classList.add('hidden');
                }
            }, 300);
        };

        window.openCartSidebar = revealSidebar;
        window.closeCartSidebar = hideSidebar;

        backdrop.addEventListener('click', hideSidebar);
        closeBtn.addEventListener('click', hideSidebar);
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !overlay.classList.contains('hidden')) {
                hideSidebar();
            }
        });

        const formatInr = function (value) {
            const amount = Number(value);
            if (!Number.isFinite(amount)) return 'Rs. 0.00';
            return 'Rs. ' + amount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        };

        const parseVariant = function (value) {
            const normalized = String(value || '').trim();
            return normalized === '' ? null : Number(normalized);
        };

        const getLineSubtotal = function (item) {
            if (Number.isFinite(Number(item.lineSubtotal))) {
                return Number(item.lineSubtotal);
            }

            return Number(item.unitPrice || 0) * Math.max(1, Number(item.quantity || 1));
        };

        const getLineTax = function (item) {
            if (Number.isFinite(Number(item.taxAmount))) {
                return Number(item.taxAmount);
            }

            return getLineSubtotal(item) * 0.18;
        };

        const getLineTotal = function (item) {
            if (Number.isFinite(Number(item.lineTotal))) {
                return Number(item.lineTotal);
            }

            return getLineSubtotal(item) + getLineTax(item);
        };

        const renderItem = function (item) {
            const qty = Math.max(1, Number(item.quantity || 1));
            const lineTotal = getLineTotal(item);
            const image = String(item.image || 'https://via.placeholder.com/150x150?text=Bio');
            const name = String(item.name || 'Product');
            const model = String(item.model || '');
            const productId = Number(item.productId || 0);
            const variantId = item.variantId == null ? '' : String(item.variantId);

            return `
                <div class="group relative flex gap-3 rounded-2xl bg-white p-3 transition hover:bg-slate-50" data-pid="${productId}" data-vid="${variantId}">
                    <button type="button" class="absolute right-2 top-2 inline-flex h-5 w-5 items-center justify-center rounded-full bg-rose-100 text-xs text-rose-500 opacity-0 transition group-hover:opacity-100 hover:bg-rose-200" data-sidebar-remove data-product-id="${productId}" data-variant-id="${variantId}" title="Remove">
                        &times;
                    </button>
                    <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-slate-100 p-1">
                        <img src="${image}" alt="${name}" loading="lazy" class="max-h-full max-w-full object-contain">
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="pr-6 text-[13px] font-bold leading-[1.3] text-slate-900 [display:-webkit-box] [-webkit-box-orient:vertical] [-webkit-line-clamp:2] overflow-hidden">${name}</div>
                        ${model ? `<div class="mt-0.5 text-[10px] font-medium text-slate-400">SKU: ${model}</div>` : ''}
                        <div class="mt-2 inline-flex items-center overflow-hidden rounded-lg border border-slate-200 bg-slate-50">
                            <button type="button" class="inline-flex h-7 w-7 items-center justify-center text-sm font-semibold text-slate-600 transition hover:bg-slate-200 hover:text-slate-900" data-sidebar-qty="-1" data-product-id="${productId}" data-variant-id="${variantId}" aria-label="Decrease">
                                -
                            </button>
                            <span class="inline-flex min-w-7 items-center justify-center border-x border-slate-200 px-2 text-[13px] font-bold text-slate-900">${qty}</span>
                            <button type="button" class="inline-flex h-7 w-7 items-center justify-center text-sm font-semibold text-slate-600 transition hover:bg-slate-200 hover:text-slate-900" data-sidebar-qty="1" data-product-id="${productId}" data-variant-id="${variantId}" aria-label="Increase">
                                +
                            </button>
                        </div>
                    </div>
                    <div class="flex shrink-0 flex-col items-end justify-between">
                        <div class="text-[13px] font-bold text-slate-900">${formatInr(lineTotal)}</div>
                        <div class="text-[8px] font-bold uppercase tracking-[0.08em] text-primary-600">Arriving tomorrow</div>
                    </div>
                </div>
            `;
        };

        const bindSidebarActions = function () {
            itemsContainer.querySelectorAll('[data-sidebar-qty]').forEach(function (button) {
                button.addEventListener('click', function () {
                    const productId = Number(button.dataset.productId || 0);
                    const variantId = parseVariant(button.dataset.variantId);
                    const direction = Number(button.dataset.sidebarQty || 0);
                    const items = window.CartStore ? window.CartStore.getItems() : [];
                    const target = items.find(function (item) {
                        return Number(item.productId || 0) === productId && (item.variantId ?? null) === variantId;
                    });

                    if (!target || !window.CartStore) return;

                    const nextQty = Number(target.quantity || 1) + direction;
                    if (nextQty <= 0) {
                        window.CartStore.removeItem(productId, variantId);
                    } else {
                        window.CartStore.updateQuantity(productId, variantId, nextQty);
                    }
                });
            });

            itemsContainer.querySelectorAll('[data-sidebar-remove]').forEach(function (button) {
                button.addEventListener('click', function () {
                    if (!window.CartStore) return;
                    const productId = Number(button.dataset.productId || 0);
                    const variantId = parseVariant(button.dataset.variantId);
                    window.CartStore.removeItem(productId, variantId);
                });
            });
        };

        const render = function () {
            if (!window.CartStore) return;

            const items = window.CartStore.getItems();
            const totalUnits = items.reduce(function (sum, item) {
                return sum + Math.max(1, Number(item.quantity || 1));
            }, 0);

            badge.textContent = totalUnits + ' ITEM' + (totalUnits !== 1 ? 'S' : '');

            if (!items.length) {
                itemsContainer.innerHTML = '';
                itemsContainer.classList.add('hidden');
                emptyState.classList.remove('hidden');
                emptyState.classList.add('flex');
                footer.classList.add('hidden');
                return;
            }

            emptyState.classList.add('hidden');
            emptyState.classList.remove('flex');
            itemsContainer.classList.remove('hidden');
            footer.classList.remove('hidden');
            itemsContainer.innerHTML = items.map(renderItem).join('');

            let subtotal = 0;
            let tax = 0;
            let total = 0;

            items.forEach(function (item) {
                subtotal += getLineSubtotal(item);
                tax += getLineTax(item);
                total += getLineTotal(item);
            });

            subtotalEl.textContent = formatInr(subtotal);
            taxEl.textContent = formatInr(tax);
            totalEl.textContent = formatInr(total);

            bindSidebarActions();
        };

        if (window.CartStore) {
            window.CartStore.subscribe(render);
        }
    });
</script>

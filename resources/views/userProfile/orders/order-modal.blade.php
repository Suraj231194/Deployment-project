@php
    $statusBadgeMap = collect($statusStyles)->mapWithKeys(function ($tone, $key) {
        return [$key => $tone['badge']];
    })->all();
@endphp

<style>
    #orderModalBody,
    #orderModalItemsWrap {
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    #orderModalBody::-webkit-scrollbar,
    #orderModalItemsWrap::-webkit-scrollbar {
        display: none;
        width: 0;
        height: 0;
    }
</style>

<div id="orderModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-300 sm:p-6">
    <button type="button" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm modal-close" aria-label="Close order details" data-modal-close="orderModal"></button>

    <div
        id="orderModalContent"
        class="relative flex flex-col w-full max-w-2xl lg:max-w-3xl grow-0 overflow-hidden rounded-[2rem] bg-white shadow-2xl transition duration-300 scale-85 max-h-[calc(100vh-2rem)]"
    >
        <div class="flex shrink-0 items-start justify-between border-b border-slate-100 px-5 py-3 sm:px-6 sm:py-4">
            <div>
                <div class="flex flex-wrap items-center gap-2.5">
                    <h3 class="text-xl font-bold tracking-tight text-slate-900">Order Details</h3>
                    <span id="orderModalStatus" class="inline-flex rounded-lg px-2 py-1 text-[9px] font-extrabold uppercase tracking-wider"></span>
                </div>
                <p id="orderModalMeta" class="mt-1.5 text-[13px] font-medium text-slate-500"></p>
            </div>
            <button type="button" data-modal-close="orderModal" class="rounded-full p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 modal-close">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div id="orderModalBody" class="flex-1 overflow-y-auto px-5 py-5 sm:px-6 sm:py-6">
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <h4 class="text-[11px] font-bold uppercase tracking-[0.22em] text-slate-400">Tracking ID</h4>
                    <p id="orderModalTracking" class="mt-2 text-[15px] font-bold text-slate-900"></p>
                    <p id="orderModalCarrier" class="mt-1 text-[12px] font-medium text-slate-500"></p>
                </div>
                <div>
                    <h4 class="text-[11px] font-bold uppercase tracking-[0.22em] text-slate-400">Shipping Address</h4>
                    <div id="orderModalAddress" class="mt-2 space-y-1 text-[14px] font-medium leading-relaxed text-slate-800"></div>
                </div>
            </div>

            <div class="mt-5">
                <h4 class="mb-3 text-[11px] font-bold uppercase tracking-[0.22em] text-slate-400">Items Summary</h4>
                <div id="orderModalItemsWrap" class="w-full overflow-x-auto">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr class="border-b border-slate-100 text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">
                                <th class="pb-2 font-medium">Product</th>
                                <th class="pb-2 text-center font-medium">SKU</th>
                                <th class="pb-2 text-center font-medium">Qty</th>
                                <th class="pb-2 text-right font-medium">Price</th>
                                <th class="pb-2 text-right font-medium">Total</th>
                            </tr>
                        </thead>
                        <tbody id="orderModalItems" class="divide-y divide-slate-100"></tbody>
                    </table>
                </div>
            </div>

            <div class="mt-3 flex justify-end">
                <div class="w-full max-w-[18.5rem] space-y-1.5 text-[13px]">
                    <div class="flex justify-between font-medium text-slate-600">
                        <span>Subtotal</span>
                        <span id="orderModalSubtotal"></span>
                    </div>
                    <div class="flex justify-between font-medium text-slate-600">
                        <span id="orderModalTaxLabel">Tax</span>
                        <span id="orderModalTax"></span>
                    </div>
                    <div class="flex justify-between border-b border-slate-100 pb-3 font-medium text-slate-600">
                        <span>Shipping</span>
                        <span id="orderModalShipping"></span>
                    </div>
                    <div class="flex items-center justify-between pt-2">
                        <span class="text-sm font-bold text-slate-900">Grand Total</span>
                        <span id="orderModalGrandTotal" class="text-2xl font-bold text-primary-800"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col items-center justify-between gap-3 border-t border-slate-100 bg-slate-50/70 px-5 py-3 sm:px-6 sm:flex-row shrink-0">
            <button type="button" class="inline-flex h-10 w-full items-center justify-center gap-2 rounded-xl text-[14px] font-bold text-slate-600 transition hover:text-slate-900 sm:w-auto">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print Invoice
            </button>

            <div class="flex w-full items-center justify-end gap-3 sm:w-auto">
                <button type="button" data-modal-close="orderModal" class="h-10 flex-1 rounded-xl px-5 text-[14px] font-bold text-slate-700 transition hover:bg-slate-200/60 sm:flex-none modal-close">
                    Close
                </button>
                <button id="orderModalReorder" type="button" class="h-10 min-w-[136px] flex-1 whitespace-nowrap rounded-xl bg-primary-600 px-7 text-[14px] font-bold text-white shadow-sm transition hover:bg-primary-700 sm:flex-none">
                    Reorder All
                </button>
            </div>
        </div>
    </div>
</div>

<form id="orderModalReorderForm" method="POST" class="hidden">
    @csrf
</form>

<script>
    const previewOrders = @json($orders);
    const previewOrderToneClasses = @json($statusBadgeMap);
    let orderModalHideTimer = null;

    function escapeOrderHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, function (character) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;',
            }[character];
        });
    }

    function setOrderModalVisibility(show) {
        if (typeof window.toggleModal === 'function') {
            window.toggleModal('orderModal', show);
        }
    }

    function closeOrderModal() {
        setOrderModalVisibility(false);
    }

    function openOrderModal(index) {
        const order = previewOrders[index];
        if (!order) return;

        const statusElement = document.getElementById('orderModalStatus');
        const metaElement = document.getElementById('orderModalMeta');
        const trackingElement = document.getElementById('orderModalTracking');
        const carrierElement = document.getElementById('orderModalCarrier');
        const addressElement = document.getElementById('orderModalAddress');
        const itemsElement = document.getElementById('orderModalItems');
        const subtotalElement = document.getElementById('orderModalSubtotal');
        const taxLabelElement = document.getElementById('orderModalTaxLabel');
        const taxElement = document.getElementById('orderModalTax');
        const shippingElement = document.getElementById('orderModalShipping');
        const grandTotalElement = document.getElementById('orderModalGrandTotal');
        const reorderButton = document.getElementById('orderModalReorder');

        if (!statusElement || !metaElement || !trackingElement || !carrierElement || !addressElement || !itemsElement || !subtotalElement || !taxLabelElement || !taxElement || !shippingElement || !grandTotalElement || !reorderButton) {
            return;
        }

        statusElement.className = 'inline-flex rounded-lg px-2 py-1 text-[9px] font-extrabold uppercase tracking-wider ' + (previewOrderToneClasses[order.status_key] || '');
        statusElement.textContent = order.status;
        metaElement.innerHTML = 'ID: ' + escapeOrderHtml(order.reference) + ' &bull; Placed on ' + escapeOrderHtml(order.date);
        trackingElement.textContent = order.tracking_id;
        carrierElement.textContent = order.carrier;
        addressElement.innerHTML = order.address_lines.map(function (line) {
            return '<p>' + escapeOrderHtml(line) + '</p>';
        }).join('');
        itemsElement.innerHTML = order.items.map(function (item) {
            return `
                <tr>
                    <td class="py-2">
                        <div class="flex items-center gap-3.5">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg ${escapeOrderHtml(item.background)} p-1.5">
                                <img src="${escapeOrderHtml(item.image)}" alt="${escapeOrderHtml(item.name)}" class="h-full w-full rounded object-cover shadow-sm opacity-90">
                            </div>
                            <div>
                                <p class="text-[13px] font-bold text-slate-900">${escapeOrderHtml(item.name)}</p>
                                <p class="text-[10px] font-medium text-slate-500">${escapeOrderHtml(item.subtitle)}</p>
                            </div>
                        </div>
                    </td>
                    <td class="py-1.5 text-center font-mono text-[12px] font-medium text-slate-500">${escapeOrderHtml(item.sku)}</td>
                    <td class="py-1.5 text-center text-[13px] font-bold text-slate-700">${escapeOrderHtml(item.qty)}</td>
                    <td class="py-1.5 text-right text-[13px] font-medium text-slate-600">${escapeOrderHtml(item.price)}</td>
                    <td class="py-1.5 text-right text-[13px] font-bold text-slate-900">${escapeOrderHtml(item.total)}</td>
                </tr>
            `;
        }).join('');
        subtotalElement.textContent = order.subtotal;
        taxLabelElement.textContent = order.invoice_note ? 'Tax (' + order.invoice_note + ')' : 'Tax';
        taxElement.textContent = order.tax;
        shippingElement.textContent = order.shipping;
        grandTotalElement.textContent = order.grand_total;
        reorderButton.setAttribute('data-reorder-url', order.reorder_url || '');
        reorderButton.disabled = order.status_key === 'archived';
        reorderButton.classList.toggle('cursor-not-allowed', order.status_key === 'archived');
        reorderButton.classList.toggle('opacity-70', order.status_key === 'archived');

        setOrderModalVisibility(true);
    }

    document.addEventListener('click', function (event) {
        const reorderButton = event.target.closest('#orderModalReorder');

        if (!reorderButton || reorderButton.disabled) {
            return;
        }

        const reorderForm = document.getElementById('orderModalReorderForm');
        const reorderUrl = reorderButton.getAttribute('data-reorder-url');

        if (!reorderForm || !reorderUrl) {
            return;
        }

        reorderForm.action = reorderUrl;
        reorderForm.submit();
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeOrderModal();
        }
    });
</script>

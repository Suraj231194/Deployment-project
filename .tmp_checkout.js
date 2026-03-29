
            document.addEventListener('DOMContentLoaded', function () {
                /* ── selectors ── */
                const summaryItems     = document.getElementById('checkoutSummaryItems');
                const subtotalEl       = document.getElementById('checkoutSubtotal');
                const taxEl            = document.getElementById('checkoutTax');
                const totalEl          = document.getElementById('checkoutTotal');
                const emptyState       = document.getElementById('checkoutEmptyState');
                const paymentCards     = Array.from(document.querySelectorAll('[data-payment-card]'));
                const paymentInputs    = Array.from(document.querySelectorAll('input[name="payment_method"]'));
                const addressCards     = Array.from(document.querySelectorAll('[data-address-card]'));
                const poUploadPanel    = document.getElementById('poUploadPanel');
                const selectedAddressSourceStateField = document.getElementById('checkoutAddressSourceStateField');
                const selectedAddressIdStateField     = document.getElementById('checkoutSelectedAddressIdStateField');

                /* ── Add New Address inline form ── */
                const addToggleBtn  = document.getElementById('addAddressToggleBtn');
                const addForm       = document.getElementById('addAddressForm');
                const addCloseBtn   = document.getElementById('addAddressCloseBtn');
                const addCloseBtn2  = document.getElementById('addAddressCloseBtn2');
                const saveAddressBtn = document.getElementById('saveAddressBtn');

                function openAddressForm() {
                    if (addForm) {
                        addForm.classList.remove('hidden');
                        addForm.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                }
                function closeAddressForm() {
                    if (addForm) addForm.classList.add('hidden');
                }

                const syncAddAddressButtonState = function (isActive) {
                    if (!addToggleBtn) return;
                    addToggleBtn.classList.toggle('border-primary-200', isActive);
                    addToggleBtn.classList.toggle('bg-primary-50', isActive);
                    addToggleBtn.classList.toggle('border-slate-200', !isActive);
                    addToggleBtn.classList.toggle('bg-white', !isActive);
                };

                const activateNewAddressSelection = function (keepFormOpen) {
                    if (selectedAddressSourceStateField) selectedAddressSourceStateField.value = 'new';
                    if (selectedAddressIdStateField) selectedAddressIdStateField.value = '';
                    addressCards.forEach(function (node) {
                        node.dataset.selected = 'false';
                        node.classList.remove('border-primary-200', 'bg-primary-50');
                        node.classList.add('border-slate-200', 'bg-white');
                    });
                    syncAddAddressButtonState(true);
                    if (keepFormOpen) {
                        openAddressForm();
                    } else {
                        closeAddressForm();
                    }
                };

                if (addToggleBtn) addToggleBtn.addEventListener('click', openAddressForm);
                if (addCloseBtn)  addCloseBtn.addEventListener('click', closeAddressForm);
                if (addCloseBtn2) addCloseBtn2.addEventListener('click', closeAddressForm);
                if (saveAddressBtn) {
                    saveAddressBtn.addEventListener('click', function () {
                        activateNewAddressSelection(false);
                    });
                }

                /* ── Step progress bar ── */
                const progressLine = document.getElementById('stepProgressLine');
                const progressConnectors = progressLine ? Array.from(progressLine.querySelectorAll('[data-step-connector]')) : [];
                function setStep(n) {
                    if (!progressLine) return;
                    progressConnectors.forEach(function (connector, index) {
                        const active = index < n - 1;
                        connector.classList.toggle('bg-primary-600', active);
                        connector.classList.toggle('bg-slate-200', !active);
                    });
                    [1, 2, 3].forEach(function (s) {
                        var circle = document.querySelector('#stepIndicator' + s + ' .step-circle');
                        if (!circle) return;
                        if (s <= n) {
                            circle.classList.remove('border-slate-300', 'text-slate-400');
                            circle.classList.add('border-primary-600', 'bg-primary-600', 'text-white');
                        } else {
                            circle.classList.add('border-slate-300', 'text-slate-400');
                            circle.classList.remove('border-primary-600', 'bg-primary-600', 'text-white');
                        }
                    });
                }
                setStep(1);

                /* ── Coupon code ── */
                const couponInput       = document.getElementById('couponInput');
                const couponApplyBtn    = document.getElementById('couponApplyBtn');
                const couponMsg         = document.getElementById('couponMsg');
                const couponCodeField   = document.getElementById('checkoutCouponCodeField');

                if (couponApplyBtn && couponInput) {
                    couponApplyBtn.addEventListener('click', function () {
                        const code = (couponInput.value || '').trim().toUpperCase();
                        couponInput.value = code;
                        if (couponCodeField) couponCodeField.value = code;
                        if (!code) {
                            couponMsg.textContent = 'Please enter a coupon code.';
                            couponMsg.className = 'mt-2 min-h-[1.1rem] text-xs font-medium text-slate-500';
                            return;
                        }
                        couponMsg.textContent = 'Coupon saved. It will be validated during final order placement.';
                        couponMsg.className = 'mt-2 min-h-[1.1rem] text-xs font-semibold text-primary-600';
                    });
                }

                /* ── formatInr ── */
                const isReOrderCheckout = null;
                const initialReOrderItems = null;
                let reOrderItems = [];

                const normalizeReOrderItem = function (item) {
                    const currentItem = item || {};
                    let variantId = null;
                    let productId = 0;
                    let quantity = 1;
                    let unitPrice = 0;
                    let unitTaxAmount = 0;
                    let unitPriceAfterGst = 0;
                    let taxAmount = 0;
                    let lineSubtotal = 0;
                    let lineTotal = 0;
                    let name = 'Product';
                    let model = 'N/A';
                    let image = 'https://via.placeholder.com/96x96?text=Bio';

                    if (Object.prototype.hasOwnProperty.call(currentItem, 'variantId')) {
                        variantId = currentItem.variantId;
                    }

                    if (productId === 0 && currentItem.productId) {
                        productId = currentItem.productId;
                    }

                    if (productId === 0 && currentItem.product_id) {
                        productId = currentItem.product_id;
                    }

                    if (currentItem.quantity) {
                        quantity = currentItem.quantity;
                    }

                    if (currentItem.unitPrice !== undefined) {
                        unitPrice = currentItem.unitPrice;
                    } else if (currentItem.unit_price !== undefined) {
                        unitPrice = currentItem.unit_price;
                    }

                    if (currentItem.unitTaxAmount !== undefined) {
                        unitTaxAmount = currentItem.unitTaxAmount;
                    } else if (currentItem.unit_tax_amount !== undefined) {
                        unitTaxAmount = currentItem.unit_tax_amount;
                    }

                    if (currentItem.unitPriceAfterGst !== undefined) {
                        unitPriceAfterGst = currentItem.unitPriceAfterGst;
                    } else if (currentItem.unit_price_after_gst !== undefined) {
                        unitPriceAfterGst = currentItem.unit_price_after_gst;
                    }

                    if (currentItem.taxAmount !== undefined) {
                        taxAmount = currentItem.taxAmount;
                    } else if (currentItem.tax_amount !== undefined) {
                        taxAmount = currentItem.tax_amount;
                    }

                    if (currentItem.lineSubtotal !== undefined) {
                        lineSubtotal = currentItem.lineSubtotal;
                    } else if (currentItem.line_subtotal !== undefined) {
                        lineSubtotal = currentItem.line_subtotal;
                    }

                    if (currentItem.lineTotal !== undefined) {
                        lineTotal = currentItem.lineTotal;
                    } else if (currentItem.line_total !== undefined) {
                        lineTotal = currentItem.line_total;
                    }

                    if (currentItem.name) {
                        name = currentItem.name;
                    } else if (currentItem.product_name) {
                        name = currentItem.product_name;
                    }

                    if (currentItem.model) {
                        model = currentItem.model;
                    } else if (currentItem.sku) {
                        model = currentItem.sku;
                    }

                    if (currentItem.image) {
                        image = currentItem.image;
                    } else if (currentItem.image_url) {
                        image = currentItem.image_url;
                    }

                    return {
                        productId: Number(productId || 0),
                        variantId: variantId === null || variantId === '' ? null : Number(variantId),
                        quantity: Math.max(1, Number(quantity || 1)),
                        unitPrice: Number(unitPrice || 0),
                        unitTaxAmount: Number(unitTaxAmount || 0),
                        unitPriceAfterGst: Number(unitPriceAfterGst || 0),
                        taxAmount: Number(taxAmount || 0),
                        lineSubtotal: Number(lineSubtotal || 0),
                        lineTotal: Number(lineTotal || 0),
                        name: String(name || 'Product'),
                        model: String(model || 'N/A'),
                        image: String(image || 'https://via.placeholder.com/96x96?text=Bio'),
                    };
                };

                const recalculateReOrderItem = function (item) {
                    const quantity = Math.max(1, Number(item.quantity || 1));
                    const unitPrice = Number(item.unitPrice || 0);
                    const unitTaxAmount = Number(item.unitTaxAmount || 0);
                    const unitPriceAfterGst = Number(item.unitPriceAfterGst || (unitPrice + unitTaxAmount));

                    item.quantity = quantity;
                    item.lineSubtotal = unitPrice * quantity;
                    item.taxAmount = unitTaxAmount * quantity;
                    item.lineTotal = unitPriceAfterGst * quantity;

                    return item;
                };

                const syncReOrderItemsField = function () {
                    const reOrderItemsField = document.getElementById('checkoutReOrderItemsField');

                    if (!reOrderItemsField) {
                        return;
                    }

                    if (!isReOrderCheckout) {
                        reOrderItemsField.value = '';
                        return;
                    }

                    reOrderItemsField.value = JSON.stringify(reOrderItems);
                };

                if (Array.isArray(initialReOrderItems)) {
                    reOrderItems = initialReOrderItems.map(function (item) {
                        const normalizedItem = normalizeReOrderItem(item);

                        return recalculateReOrderItem(normalizedItem);
                    });
                }

                const getCheckoutItems = function () {
                    if (isReOrderCheckout) {
                        return reOrderItems;
                    }

                    return window.CartStore ? window.CartStore.getItems() : [];
                };

                window.syncCheckoutReOrderItems = function () {
                    syncReOrderItemsField();
                };

                window.checkoutUpdateItem = function (productId, variantId, quantity) {
                    if (isReOrderCheckout) {
                        const normalizedProductId = Number(productId || 0);
                        const normalizedVariantId = variantId === null || variantId === '' ? null : Number(variantId);
                        const normalizedQuantity = Math.max(1, Number(quantity || 1));

                        reOrderItems = reOrderItems.map(function (item) {
                            const currentProductId = Number(item.productId || 0);
                            const currentVariantId = item.variantId === null ? null : Number(item.variantId);

                            if (currentProductId !== normalizedProductId) {
                                return item;
                            }

                            if (currentVariantId !== normalizedVariantId) {
                                return item;
                            }

                            item.quantity = normalizedQuantity;

                            return recalculateReOrderItem(item);
                        });

                        syncReOrderItemsField();

                        if (typeof render === 'function') {
                            render();
                        }

                        return;
                    }

                    if (window.CartStore && typeof window.CartStore.updateQuantity === 'function') {
                        window.CartStore.updateQuantity(
                            Number(productId || 0),
                            variantId === null || variantId === '' ? null : Number(variantId),
                            Math.max(1, Number(quantity || 1))
                        );
                    }
                };

                window.checkoutRemoveItem = function (productId, variantId) {
                    if (isReOrderCheckout) {
                        const normalizedProductId = Number(productId || 0);
                        const normalizedVariantId = variantId === null || variantId === '' ? null : Number(variantId);

                        reOrderItems = reOrderItems.filter(function (item) {
                            const currentProductId = Number(item.productId || 0);
                            const currentVariantId = item.variantId === null ? null : Number(item.variantId);

                            if (currentProductId !== normalizedProductId) {
                                return true;
                            }

                            return currentVariantId !== normalizedVariantId;
                        });

                        syncReOrderItemsField();

                        if (typeof render === 'function') {
                            render();
                        }

                        return;
                    }

                    if (window.CartStore && typeof window.CartStore.removeItem === 'function') {
                        window.CartStore.removeItem(
                            Number(productId || 0),
                            variantId === null || variantId === '' ? null : Number(variantId)
                        );
                    }
                };

                const formatInr = function (value) {
                    const numeric = Number(value);
                    if (!Number.isFinite(numeric)) return 'Rs. 0.00';
                    return 'Rs. ' + numeric.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                };

                /* ── render summary row ── */
                const getLineSubtotal = function (item) {
                    let lineSubtotal = item.lineSubtotal;

                    if (lineSubtotal === undefined) {
                        lineSubtotal = item.line_subtotal;
                    }

                    // Step 1: prefer the backend subtotal when the current cart line came from the authenticated cart.
                    if (Number.isFinite(Number(lineSubtotal))) {
                        return Number(lineSubtotal);
                    }

                    // Step 2: keep the existing guest subtotal fallback for pre-login browsing.
                    let unitPrice = item.unitPrice;

                    if (unitPrice === undefined) {
                        unitPrice = item.unit_price;
                    }

                    return Number(unitPrice || 0) * Math.max(1, Number(item.quantity || 1));
                };

                const getLineTax = function (item) {
                    let taxAmount = item.taxAmount;

                    if (taxAmount === undefined) {
                        taxAmount = item.tax_amount;
                    }

                    // Step 1: prefer the backend tax when the current cart line came from the authenticated cart.
                    if (Number.isFinite(Number(taxAmount))) {
                        return Number(taxAmount);
                    }

                    // Step 2: keep the existing guest GST fallback for pre-login browsing.
                    return getLineSubtotal(item) * 0.18;
                };

                const getLineTotal = function (item) {
                    let lineTotal = item.lineTotal;

                    if (lineTotal === undefined) {
                        lineTotal = item.line_total;
                    }

                    // Step 1: prefer the backend total when the current cart line came from the authenticated cart.
                    if (Number.isFinite(Number(lineTotal))) {
                        return Number(lineTotal);
                    }

                    // Step 2: keep the existing guest total fallback for pre-login browsing.
                    return getLineSubtotal(item) + getLineTax(item);
                };

                const renderSummaryRow = function (item) {
                    const quantity = Math.max(1, Number(item.quantity || 1));
                    // Step 1: show the product line price before GST so it stays aligned with the subtotal.
                    const subtotal = getLineSubtotal(item);
                    const image = String(item.image || item.image_url || 'https://via.placeholder.com/96x96?text=Bio');
                    const name = String(item.name || item.product_name || 'Product');
                    const model = String(item.model || item.sku || 'N/A');
                    let productId = item.productId;
                    let variantId = item.variantId;

                    if (productId === undefined) {
                        productId = item.product_id;
                    }

                    if (variantId === undefined) {
                        variantId = item.product_variant_id;
                    }

                    productId = Number(productId || 0);
                    const variantValue = variantId === null || variantId === '' ? 'null' : String(Number(variantId));
                    return `
                        <div class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-3 py-3">
                            <div class="h-16 w-16 shrink-0 overflow-hidden rounded-2xl bg-slate-100">
                                <img src="${image}" alt="${name}" class="h-full w-full object-cover">
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-900">${name}</p>
                                <p class="mt-0.5 text-xs text-slate-500">${model}</p>
                                <div class="mt-2 flex items-center gap-3">
                                    <div class="flex items-center rounded-lg border border-slate-200 bg-white">
                                        <button type="button" class="flex h-7 w-7 items-center justify-center text-slate-500 transition hover:bg-slate-100 hover:text-slate-900" onclick="window.checkoutUpdateItem(${productId}, ${variantValue}, Math.max(1, ${quantity} - 1));">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" /></svg>
                                        </button>
                                        <input type="number" readonly class="w-8 border-x border-slate-200 bg-transparent text-center text-xs font-semibold text-slate-900 outline-none" value="${quantity}">
                                        <button type="button" class="flex h-7 w-7 items-center justify-center text-slate-500 transition hover:bg-slate-100 hover:text-slate-900" onclick="window.checkoutUpdateItem(${productId}, ${variantValue}, ${quantity} + 1);">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                                        </button>
                                    </div>
                                    <button type="button" class="text-xs font-medium text-slate-400 underline decoration-slate-300 underline-offset-2 transition hover:text-rose-600 hover:decoration-rose-300" onclick="window.checkoutRemoveItem(${productId}, ${variantValue});">
                                        Remove
                                    </button>
                                </div>
                            </div>
                            <div class="flex flex-col items-end justify-between self-stretch">
                                <span class="text-sm font-semibold text-primary-700">${formatInr(subtotal)}</span>
                            </div>
                        </div>
                    `;
                };

                /* ── render totals ── */
                const render = function () {
                    const items = getCheckoutItems();
                    if (summaryItems) summaryItems.innerHTML = '';

                    if (!items.length) {
                        if (emptyState) emptyState.classList.remove('hidden');
                        if (summaryItems) summaryItems.classList.add('hidden');
                        if (subtotalEl) subtotalEl.innerHTML = 'Rs. 0.00';
                        if (taxEl)      taxEl.innerHTML      = 'Rs. 0.00';
                        if (totalEl)    totalEl.innerHTML    = 'Rs. 0.00';
                        syncReOrderItemsField();
                        return;
                    }

                    if (emptyState) emptyState.classList.add('hidden');
                    if (summaryItems) summaryItems.classList.remove('hidden');

                    var subtotal = 0;
                    var tax = 0;
                    var total = 0;
                    items.forEach(function (item) {
                        subtotal += getLineSubtotal(item);
                        tax += getLineTax(item);
                        total += getLineTotal(item);
                        if (summaryItems) summaryItems.insertAdjacentHTML('beforeend', renderSummaryRow(item));
                    });

                    if (subtotalEl) subtotalEl.innerHTML = formatInr(subtotal);
                    if (taxEl) taxEl.innerHTML = formatInr(tax);
                    if (totalEl) totalEl.innerHTML = formatInr(total);
                    syncReOrderItemsField();
                };

                /* ── address selection ── */
                const setActiveAddress = function (card) {
                    addressCards.forEach(function (node) {
                        const selected = node === card;
                        node.dataset.selected = selected ? 'true' : 'false';
                        node.classList.toggle('border-primary-200', selected);
                        node.classList.toggle('bg-primary-50', selected);
                        node.classList.toggle('border-slate-200', !selected);
                        node.classList.toggle('bg-white', !selected);
                    });
                    if (selectedAddressSourceStateField) selectedAddressSourceStateField.value = 'existing';
                    if (selectedAddressIdStateField) selectedAddressIdStateField.value = card.dataset.addressId || '';
                    syncAddAddressButtonState(false);
                    closeAddressForm();
                };

                /* ── payment card sync ── */
                const syncPaymentCards = function () {
                    paymentCards.forEach(function (card) {
                        const input = card.querySelector('input[type="radio"]');
                        if (!input) return;
                        card.classList.toggle('border-primary-200', input.checked);
                        card.classList.toggle('bg-primary-50', input.checked);
                        card.classList.toggle('border-slate-200', !input.checked);
                        card.classList.toggle('bg-white', !input.checked);
                    });
                    const active = paymentInputs.find(function (i) { return i.checked; });
                    if (poUploadPanel) {
                        poUploadPanel.classList.toggle('hidden', !(active && active.value === 'po'));
                    }
                };

                /* ── events ── */
                addressCards.forEach(function (card) {
                    card.addEventListener('click', function () {
                        setActiveAddress(card);
                    });
                });

                paymentInputs.forEach(function (input) {
                    input.addEventListener('change', syncPaymentCards);
                });

                /* ── GSTIN auto-uppercase ── */
                var gstinInput = document.getElementById('gstinInput');
                var panInput   = document.getElementById('panInput');
                if (gstinInput) gstinInput.addEventListener('input', function () { this.value = this.value.toUpperCase(); });
                if (panInput)   panInput.addEventListener('input',   function () { this.value = this.value.toUpperCase(); });

                /* ── init ── */
                if (isReOrderCheckout) {
                    render();
                }

                if (!isReOrderCheckout && window.CartStore) {
                    window.CartStore.subscribe(render);
                }
                if (selectedAddressSourceStateField && selectedAddressSourceStateField.value === 'new') {
                    activateNewAddressSelection(addForm && !addForm.classList.contains('hidden'));
                } else {
                    syncAddAddressButtonState(false);
                }
                syncPaymentCards();
            });
        

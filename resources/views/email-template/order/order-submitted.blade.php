@php
    $appName = config('app.name', 'Biogenix');
    $submittedDate = optional($order->submitted_at)->format('d M Y, h:i A') ?: now()->format('d M Y, h:i A');
    $itemCount = $order->items->count();
    $currency = $order->currency ?: 'INR';
    $formatMoney = function ($amount) use ($currency) {
        $numericAmount = (float) $amount;

        if ($currency === 'INR') {
            return 'Rs. '.number_format($numericAmount, 2);
        }

        return $currency.' '.number_format($numericAmount, 2);
    };
    $subtotalAmount = (float) $order->subtotal_amount;
    $discountAmount = (float) $order->discount_amount;
    $shippingAmount = (float) $order->shipping_amount;
    $adjustmentAmount = (float) $order->adjustment_amount;
    $roundingAmount = (float) $order->rounding_amount;
    $taxAmount = (float) $order->tax_amount;
    $orderTotal = (float) $order->total_amount;
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your order has been submitted</title>
</head>
<body style="margin:0; padding:24px; background:#f8fafc; font-family:Arial, Helvetica, sans-serif; color:#0f172a;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px; margin:0 auto; background:#ffffff; border:1px solid #e2e8f0; border-radius:16px; overflow:hidden;">
        <tr>
            <td style="padding:32px;">
                <!-- Business step: lead with a simple confirmation header so the customer instantly knows the order is safely received. -->
                <p style="margin:0; font-size:12px; font-weight:700; letter-spacing:0.18em; text-transform:uppercase; color:#1A4D2E;">Order Submitted</p>
                <h1 style="margin:16px 0 0; font-size:28px; line-height:1.25; color:#0f172a;">We have received your order</h1>

                <p style="margin:16px 0 0; font-size:15px; line-height:1.8; color:#475569;">
                    Hello {{ $user->name ?: 'Biogenix Customer' }},
                </p>

                <p style="margin:12px 0 0; font-size:15px; line-height:1.8; color:#475569;">
                    Thank you for placing your order with {{ $appName }}. Our team has received your request and will start processing it soon.
                </p>

                <!-- Business step: show the order reference details first so the customer can identify this purchase easily. -->
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-top:24px; border:1px solid #e2e8f0; border-radius:12px; background:#f8fafc;">
                    <tr>
                        <td style="padding:18px;">
                            <p style="margin:0; font-size:13px; line-height:1.8; color:#64748b;">
                                <strong style="color:#0f172a;">Order Number:</strong> #{{ $order->id }}<br>
                                <strong style="color:#0f172a;">Submitted On:</strong> {{ $submittedDate }}<br>
                                <strong style="color:#0f172a;">Items:</strong> {{ $itemCount }}<br>
                                <strong style="color:#0f172a;">Order Total:</strong> {{ $formatMoney($orderTotal) }}
                            </p>
                        </td>
                    </tr>
                </table>

                <!-- Business step: present the ordered products in a familiar ecommerce summary so the customer can review quantities and commercial details quickly. -->
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-top:24px; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden;">
                    <tr>
                        <td style="padding:16px 18px; background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                            <p style="margin:0; font-size:14px; font-weight:700; color:#0f172a;">Order Details</p>
                        </td>
                    </tr>
                    @foreach ($order->items as $orderItem)
                        <tr>
                            <td style="padding:16px 18px; border-bottom:{{ $loop->last ? '0' : '1px solid #e2e8f0' }};">
                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td valign="top" style="padding-right:16px;">
                                            <p style="margin:0; font-size:14px; font-weight:700; color:#0f172a;">{{ $orderItem->product_name }}</p>
                                            @if (filled($orderItem->variant_name))
                                                <p style="margin:6px 0 0; font-size:12px; line-height:1.7; color:#64748b;">
                                                    <strong style="color:#334155;">Variant:</strong> {{ $orderItem->variant_name }}
                                                </p>
                                            @endif
                                            @if (filled($orderItem->sku))
                                                <p style="margin:4px 0 0; font-size:12px; line-height:1.7; color:#64748b;">
                                                    <strong style="color:#334155;">SKU:</strong> {{ $orderItem->sku }}
                                                </p>
                                            @endif
                                            <p style="margin:4px 0 0; font-size:12px; line-height:1.7; color:#64748b;">
                                                <strong style="color:#334155;">Quantity:</strong> {{ (int) $orderItem->quantity }}
                                            </p>
                                        </td>
                                        <td valign="top" align="right" style="white-space:nowrap;">
                                            <p style="margin:0; font-size:12px; line-height:1.7; color:#64748b;">Unit Price</p>
                                            <p style="margin:4px 0 0; font-size:14px; font-weight:700; color:#0f172a;">{{ $formatMoney($orderItem->unit_price) }}</p>
                                            <p style="margin:10px 0 0; font-size:12px; line-height:1.7; color:#64748b;">Line Subtotal</p>
                                            <p style="margin:4px 0 0; font-size:14px; font-weight:700; color:#1A4D2E;">{{ $formatMoney($orderItem->subtotal_amount) }}</p>
                                            @if ((float) $orderItem->discount_amount > 0)
                                                <!-- Business step: show product-level savings near the item row so the customer can understand the benefit without breaking the order total math. -->
                                                <p style="margin:10px 0 0; font-size:12px; line-height:1.7; color:#64748b;">Savings</p>
                                                <p style="margin:4px 0 0; font-size:13px; font-weight:700; color:#16a34a;">-{{ $formatMoney($orderItem->discount_amount) }}</p>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    @endforeach
                </table>

                <!-- Business step: show the final commercial summary in the same order as the checkout summary for easy customer reconciliation. -->
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-top:24px; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden;">
                    <tr>
                        <td style="padding:16px 18px; background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                            <p style="margin:0; font-size:14px; font-weight:700; color:#0f172a;">Payment Summary</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:18px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="padding:0 0 10px; font-size:14px; color:#475569;">Subtotal</td>
                                    <td align="right" style="padding:0 0 10px; font-size:14px; font-weight:600; color:#0f172a;">{{ $formatMoney($subtotalAmount) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:0 0 10px; font-size:14px; color:#475569;">Shipping</td>
                                    <td align="right" style="padding:0 0 10px; font-size:14px; font-weight:600; color:#0f172a;">
                                        {{ $shippingAmount > 0 ? $formatMoney($shippingAmount) : 'FREE' }}
                                    </td>
                                </tr>
                                @if ($adjustmentAmount !== 0.0)
                                    <tr>
                                        <td style="padding:0 0 10px; font-size:14px; color:#475569;">Adjustment</td>
                                        <td align="right" style="padding:0 0 10px; font-size:14px; font-weight:600; color:#0f172a;">{{ $formatMoney($adjustmentAmount) }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td style="padding:0 0 14px; font-size:14px; color:#475569;">GST</td>
                                    <td align="right" style="padding:0 0 14px; font-size:14px; font-weight:600; color:#0f172a;">{{ $formatMoney($taxAmount) }}</td>
                                </tr>
                                @if ($roundingAmount !== 0.0)
                                    <tr>
                                        <td style="padding:0 0 14px; font-size:14px; color:#475569;">Rounding</td>
                                        <td align="right" style="padding:0 0 14px; font-size:14px; font-weight:600; color:#0f172a;">{{ $formatMoney($roundingAmount) }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td colspan="2" style="border-top:1px solid #e2e8f0; height:14px; font-size:1px; line-height:1px;">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td style="font-size:16px; font-weight:700; color:#0f172a;">Total</td>
                                    <td align="right" style="font-size:16px; font-weight:800; color:#0f172a;">{{ $formatMoney($orderTotal) }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                @if ($discountAmount > 0)
                    <!-- Business step: surface total savings as a separate positive note so the customer sees the benefit without confusing the payment summary rows. -->
                    <div style="margin-top:16px; padding:14px 16px; border-radius:12px; background:#f0fdf4; color:#166534;">
                        <p style="margin:0; font-size:13px; line-height:1.8;">
                            You saved <strong>{{ $formatMoney($discountAmount) }}</strong> on this order through active pricing benefits.
                        </p>
                    </div>
                @endif

                @if (filled($order->notes))
                    <!-- Business step: keep any submitted customer note visible so the buyer knows the team received it. -->
                    <div style="margin-top:24px; padding:16px; border-radius:12px; background:#fff7ed; color:#9a3412;">
                        <p style="margin:0; font-size:12px; font-weight:700; letter-spacing:0.14em; text-transform:uppercase;">Order Note</p>
                        <p style="margin:8px 0 0; font-size:14px; line-height:1.8;">{{ $order->notes }}</p>
                    </div>
                @endif

                <p style="margin:24px 0 0; font-size:14px; line-height:1.8; color:#475569;">
                    Our operations team will now review stock availability, order details, and dispatch readiness. You will receive the next update once the order moves forward in processing.
                </p>

                <p style="margin:12px 0 0; font-size:14px; line-height:1.8; color:#475569;">
                    If you need any urgent help regarding this order, you can contact the {{ $appName }} support team.
                </p>

                <div style="margin-top:28px; padding:16px; border-radius:12px; background:#eff6ff; color:#1e3a8a; font-size:13px; line-height:1.8;">
                    Business note: this email confirms that your order has been submitted successfully. Processing and further operational checks will begin shortly.
                </div>
            </td>
        </tr>
    </table>
</body>
</html>

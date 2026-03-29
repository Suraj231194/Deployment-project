@if ($order)
    @php
        $currency = $order->currency ?? 'INR';
        $fmt = fn($val) => $currency . ' ' . number_format((float) $val, 2);
        $statusMap = [
            'draft'     => ['bg' => 'bg-amber-100',   'text' => 'text-amber-800'],
            'submitted' => ['bg' => 'bg-emerald-100',  'text' => 'text-emerald-800'],
            'cancelled' => ['bg' => 'bg-rose-100',     'text' => 'text-rose-800'],
            'delivered' => ['bg' => 'bg-primary-100',  'text' => 'text-primary-800'],
        ];
        $s = $statusMap[strtolower($order->status)] ?? ['bg' => 'bg-slate-100', 'text' => 'text-slate-700'];
    @endphp

    {{-- ═══ HEADER ═══ --}}
    <div class="flex items-start justify-between gap-4 pb-6 mb-6 border-b border-slate-200">
        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-3 mb-2">
                <h2 class="text-2xl font-black tracking-tight text-slate-900 leading-none">Order Details</h2>
                <span class="shrink-0 inline-flex items-center rounded-lg px-3 py-1 text-[11px] font-bold uppercase tracking-wider {{ $s['bg'] }} {{ $s['text'] }}">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
            <p class="text-sm text-slate-500">
                <span class="font-semibold text-slate-700">#ORD-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</span>
                <span class="mx-2 text-slate-300">•</span>
                Placed on {{ optional($order->created_at)->format('M d, Y') }}
            </p>
        </div>
    </div>

    {{-- ═══ META GRID ═══ --}}
    <div class="grid grid-cols-2 gap-6 mb-8">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-2">Placed By</p>
            <p class="text-[15px] font-bold text-slate-900">{{ $order->placedByUser?->name ?? 'Unknown' }}</p>
            <p class="text-[13px] text-slate-500 mt-0.5">Via Biogenix Procurement</p>
        </div>
        <div>
            <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-2">Purchasing Entity</p>
            <p class="text-[15px] font-bold text-slate-900">{{ $order->company?->name ?? 'Self' }}</p>
            @if ($order->company?->address)
                <p class="text-[13px] text-slate-500 mt-0.5">{{ $order->company->address }}</p>
            @endif
        </div>
    </div>

    {{-- ═══ ITEMS TABLE ═══ --}}
    <div class="mb-8">
        <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-4">Items Summary</p>

        {{-- Table header --}}
        <div class="grid gap-2 pb-3 border-b border-slate-200" style="grid-template-columns: 2fr 1.2fr 0.5fr 1fr 1fr;">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Product</span>
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">SKU</span>
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 text-center">Qty</span>
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 text-right">Price</span>
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 text-right">Total</span>
        </div>

        {{-- Table rows --}}
        @foreach ($order->items as $item)
            <div class="grid gap-2 py-4 border-b border-slate-100 items-center" style="grid-template-columns: 2fr 1.2fr 0.5fr 1fr 1fr;">
                {{-- Product --}}
                <div class="flex items-center gap-3 min-w-0">
                    <div class="shrink-0 w-12 h-12 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center overflow-hidden">
                        @if ($item->product?->image_path)
                            <img src="{{ asset($item->product->image_path) }}" alt="{{ $item->product_name }}" class="w-full h-full object-contain p-1">
                        @else
                            <svg class="w-5 h-5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/>
                            </svg>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="text-[14px] font-bold text-slate-900 truncate">{{ $item->product_name }}</p>
                        @if ($item->variant_name)
                            <p class="text-[12px] text-slate-500 mt-0.5">{{ $item->variant_name }}</p>
                        @endif
                    </div>
                </div>
                {{-- SKU --}}
                <p class="font-mono text-[12px] text-slate-500 truncate">{{ $item->sku }}</p>
                {{-- Qty --}}
                <p class="text-[14px] font-bold text-slate-800 text-center">{{ $item->quantity }}</p>
                {{-- Price --}}
                <p class="text-[13px] text-slate-600 text-right">{{ $fmt($item->unit_price) }}</p>
                {{-- Total --}}
                <p class="text-[14px] font-bold text-slate-900 text-right">{{ $fmt($item->total_amount) }}</p>
            </div>
        @endforeach
    </div>

    {{-- ═══ FINANCIAL SUMMARY ═══ --}}
    <div class="flex justify-end mb-8">
        <div class="w-80">
            <div class="space-y-2.5 text-[14px]">
                <div class="flex justify-between text-slate-500">
                    <span>Subtotal</span>
                    <span class="font-medium text-slate-800">{{ $fmt($order->subtotal_amount) }}</span>
                </div>
                <div class="flex justify-between text-slate-500">
                    <span>Tax (GST)</span>
                    <span class="font-medium text-slate-800">{{ $fmt($order->tax_amount) }}</span>
                </div>
                <div class="flex justify-between text-slate-500 pb-3 border-b border-slate-200">
                    <span>Shipping</span>
                    <span class="font-medium text-slate-800">{{ $fmt($order->shipping_amount) }}</span>
                </div>
                @if ($order->adjustment_amount != 0)
                    <div class="flex justify-between text-slate-500">
                        <span>Adjustments</span>
                        <span class="font-bold {{ $order->adjustment_amount < 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                            {{ $fmt($order->adjustment_amount) }}
                        </span>
                    </div>
                @endif
                <div class="flex justify-between items-center pt-3">
                    <span class="text-base font-extrabold text-slate-900">Grand Total</span>
                    <span class="text-[22px] font-black text-primary-700 leading-none">{{ $fmt($order->total_amount) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ NOTES ═══ --}}
    @if ($order->notes)
        <div class="mb-6 rounded-2xl bg-slate-50 border border-slate-200 p-5">
            <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-2">Procurement Notes</p>
            <p class="text-[14px] leading-relaxed text-slate-600">{{ $order->notes }}</p>
        </div>
    @endif

    {{-- ═══ FOOTER ACTIONS ═══ --}}
    <div class="flex items-center justify-between gap-3 pt-5 border-t border-slate-200">
        <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 text-[13px] font-semibold text-slate-500 hover:text-slate-800 transition cursor-pointer px-3 py-2 rounded-xl hover:bg-slate-100">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Print Invoice
        </button>

        <div class="flex items-center gap-3">
            <button type="button"
                data-modal-close="order-details-modal"
                class="modal-close h-11 px-7 rounded-xl border border-slate-200 bg-white text-[14px] font-bold text-slate-700 hover:bg-slate-50 transition cursor-pointer shadow-sm">
                Close
            </button>
            <form method="POST" action="{{ route('orders.reorder', $order->id) }}" class="inline">
                @csrf
                <button type="submit"
                    class="h-11 px-8 rounded-xl bg-primary-600 text-[14px] font-bold text-white shadow-md shadow-primary-600/25 hover:bg-primary-700 hover:shadow-lg hover:shadow-primary-600/30 transition cursor-pointer whitespace-nowrap">
                    Reorder All
                </button>
            </form>
        </div>
    </div>

@else
    <div class="py-16 text-center">
        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-slate-100 flex items-center justify-center">
            <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
            </svg>
        </div>
        <p class="text-base font-bold text-slate-900">Order Data Unavailable</p>
        <p class="mt-1 text-sm text-slate-500">We couldn't retrieve the details for this order.</p>
    </div>
@endif

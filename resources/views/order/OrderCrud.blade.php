@extends('layouts.customer')
@section('customer_minimal', 'minimal')


@php
    $portal = auth()->user()?->user_type ?? request('user_type', request('portal', 'b2c'));
    $portal = $portal === 'b2b' ? 'b2b' : 'b2c';
    $backUrl = url()->previous() ?: route('home');
    $orderItems = collect($orders->items());
    $draftCount = $orderItems->where('status', 'draft')->count();
    $submittedCount = $orderItems->where('status', 'submitted')->count();
    $cancelledCount = $orderItems->where('status', 'cancelled')->count();
    $totalValue = $orderItems->sum(fn ($order) => (float) $order->total_amount);
    $companyOptions = $orderItems
        ->map(fn ($order) => $order->company?->name ?? 'Self')
        ->filter()
        ->unique()
        ->sort()
        ->values();
    $productIds = old('product_id', $editingItems->pluck('product_id')->all());
    $quantities = old('quantity', $editingItems->pluck('quantity')->all());
    $lineCount = max(3, count($productIds), count($quantities));
    $metricCardClass = 'rounded-3xl border border-slate-200 bg-white p-4 shadow-sm md:p-5';
    $panelClass = 'space-y-6 rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm md:p-8';
    $inputClass = 'h-11 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:ring-2 focus:ring-primary-500/40';
    $textareaClass = 'w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:ring-2 focus:ring-primary-500/40';
    $buttonSecondary = 'inline-flex h-10 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50';
@endphp

@section('title', 'Orders')
@section('customer_active', 'orders')

@push('styles')
<style>
    /* Standard select enhancement using only Tailwind-compatible properties */
    #create-order-section select,
    #ordersFilterStatus,
    #ordersFilterCompany {
        accent-color: var(--color-primary-600);
    }

    #create-order-section select:focus,
    #ordersFilterStatus:focus,
    #ordersFilterCompany:focus {
        border-color: var(--color-primary-600) !important;
        box-shadow: 0 0 0 4px rgba(26, 77, 46, 0.1) !important;
    }

    #ordersFilterStatus,
    #ordersFilterCompany {
        border: 0 !important;
        background-color: transparent !important;
        box-shadow: none !important;
    }

    #ordersFilterStatus:focus,
    #ordersFilterCompany:focus {
        border-color: transparent !important;
        box-shadow: 0 0 0 3px rgba(26, 77, 46, 0.12) !important;
    }

    #ordersFilterStatus option,
    #ordersFilterCompany option,
    #create-order-section select option {
        background-color: #ffffff;
        color: #0f172a;
    }

    #ordersFilterStatus option:checked,
    #ordersFilterStatus option:hover,
    #ordersFilterCompany option:checked,
    #ordersFilterCompany option:hover,
    #create-order-section select option:checked,
    #create-order-section select option:hover {
        background: linear-gradient(0deg, var(--color-primary-600), var(--color-primary-600)) !important;
        color: white !important;
    }
</style>
@endpush

@section('customer_content')
    <x-account.workspace
        :portal="$portal"
        active="orders"
        :back-url="$backUrl"
        back-label="Back"
        title="Orders"
        description="Track and manage your procurement history and active documentation."
    >
        <x-slot:headerActions>
            @if (!$editingOrder)
                <button onclick="document.getElementById('create-order-section').scrollIntoView({behavior: 'smooth'})" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-primary-600 px-4 text-[13px] font-bold text-white shadow-lg transition hover:bg-primary-700 hover:shadow-primary-600/20">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M12 4v16m8-8H4"/></svg>
                    New Procurement
                </button>
            @endif
        </x-slot:headerActions>
        <x-slot:metrics>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @php
                    $stats = [
                        ['label' => 'Total Orders', 'value' => $orders->total(), 'icon' => 'shopping-cart', 'color' => 'indigo', 'bg' => 'bg-indigo-50', 'text' => 'text-indigo-600'],
                        ['label' => 'Draft Items', 'value' => $draftCount, 'icon' => 'edit-3', 'color' => 'amber', 'bg' => 'bg-amber-50', 'text' => 'text-amber-600'],
                        ['label' => 'Submitted', 'value' => $submittedCount, 'icon' => 'check-circle', 'color' => 'emerald', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-600'],
                        ['label' => 'Est. Value', 'value' => 'INR ' . number_format($totalValue, 0), 'icon' => 'credit-card', 'color' => 'primary', 'bg' => 'bg-primary-50', 'text' => 'text-primary-600'],
                    ];
                @endphp
                @foreach ($stats as $stat)
                    <div class="group relative overflow-hidden rounded-[2.5rem] border border-slate-200/60 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-slate-200/50 before:absolute before:inset-0 before:-translate-x-full before:bg-gradient-to-r before:from-transparent before:via-white/60 before:to-transparent before:transition-transform before:duration-1000 hover:before:translate-x-full">
                        <div class="flex items-center justify-between">
                            <div class="space-y-1">
                                <p class="text-[11px] font-extrabold uppercase tracking-widest text-slate-400">{{ $stat['label'] }}</p>
                                <p class="text-2xl font-black tracking-tight text-slate-900">{{ $stat['value'] }}</p>
                            </div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl {{ $stat['bg'] }} {{ $stat['text'] }} transition-transform duration-500 group-hover:scale-110 group-hover:rotate-6">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    @if($stat['icon'] === 'shopping-cart') <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /> @endif
                                    @if($stat['icon'] === 'edit-3') <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /> @endif
                                    @if($stat['icon'] === 'check-circle') <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /> @endif
                                    @if($stat['icon'] === 'credit-card') <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /> @endif
                                </svg>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-slot:metrics>

        <div id="create-order-section" class="scroll-mt-8 {{ $panelClass }} overflow-hidden">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">{{ $editingOrder ? 'Update Order #'.$editingOrder->id : 'Create New Order' }}</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Quickly draft a new procurement by adding line items below.</p>
                </div>
                @if ($editingOrder)
                    <a href="{{ route('orders.index') }}" class="inline-flex h-10 items-center justify-center rounded-xl border border-rose-200 bg-rose-50 px-5 text-[13px] font-bold text-rose-600 transition hover:bg-rose-100">Cancel Edit Mode</a>
                @endif
            </div>

            <form method="POST" action="{{ $editingOrder ? route('orders.update', $editingOrder->id) : route('orders.store') }}" class="mt-8 space-y-8">
                @csrf
                @if ($editingOrder)
                    @method('PUT')
                @endif

                <div class="grid gap-4 md:grid-cols-4">
                    <div class="space-y-2">
                        <label for="status" class="text-sm font-semibold text-slate-700">Order Status</label>
                        <select id="status" name="status" class="{{ $inputClass }} accent-primary-600" required>
                            @foreach (['draft', 'submitted', 'cancelled'] as $status)
                                <option value="{{ $status }}" @selected(old('status', $editingOrder?->status ?? 'draft') === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label for="shipping_amount" class="text-sm font-semibold text-slate-700">Shipping Amount</label>
                        <input id="shipping_amount" name="shipping_amount" type="number" step="0.01" class="{{ $inputClass }}" value="{{ old('shipping_amount', $editingOrder?->shipping_amount ?? 0) }}">
                    </div>
                    <div class="space-y-2">
                        <label for="adjustment_amount" class="text-sm font-semibold text-slate-700">Adjustment Amount</label>
                        <input id="adjustment_amount" name="adjustment_amount" type="number" step="0.01" class="{{ $inputClass }}" value="{{ old('adjustment_amount', $editingOrder?->adjustment_amount ?? 0) }}">
                    </div>
                    <div class="space-y-2">
                        <label for="rounding_amount" class="text-sm font-semibold text-slate-700">Rounding Amount</label>
                        <input id="rounding_amount" name="rounding_amount" type="number" step="0.01" class="{{ $inputClass }}" value="{{ old('rounding_amount', $editingOrder?->rounding_amount ?? 0) }}">
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-semibold text-slate-900">Order Lines</h3>
                        <span class="rounded-full bg-primary-50 px-3 py-1 text-xs font-semibold text-primary-700">{{ $lineCount }} prepared rows</span>
                    </div>

                    @for ($index = 0; $index < $lineCount; $index++)
                        @php
                            $selectedProductId = old('product_id.'.$index, $editingItems[$index]->product_id ?? null);
                            $selectedQuantity = old('quantity.'.$index, $editingItems[$index]->quantity ?? null);
                        @endphp
                        <div class="group relative grid gap-3 rounded-3xl border border-slate-200 bg-white/50 p-3 shadow-sm transition-all hover:bg-white hover:shadow-md md:grid-cols-5 md:items-end lg:p-4">
                            <div class="space-y-1 md:col-span-2">
                                <label class="text-[9px] font-extrabold uppercase tracking-widest text-slate-400">Product {{ $index + 1 }}</label>
                                <div class="relative">
                                    <select name="product_id[]" class="h-10 w-full appearance-none rounded-xl border-slate-200 bg-white px-3 text-[12px] font-medium text-slate-900 shadow-sm outline-none transition focus:border-primary-600 focus:ring-4 focus:ring-primary-600/10 accent-primary-600">
                                        <option value="">Select product...</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}" @selected((string) $selectedProductId === (string) $product->id)>
                                                {{ $product->name }}{{ $product->variant_name ? ' — '.$product->variant_name : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 text-slate-400">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-1 md:col-span-2">
                                <label class="text-[9px] font-extrabold uppercase tracking-widest text-slate-400">Quantity</label>
                                <input name="quantity[]" type="number" min="1" class="h-10 w-full rounded-xl border-slate-200 bg-white px-3 text-[12px] font-medium text-slate-900 shadow-sm outline-none transition focus:border-primary-600 focus:ring-4 focus:ring-primary-600/10" value="{{ $selectedQuantity }}" placeholder="1">
                            </div>

                            <div class="flex h-full flex-col justify-end space-y-1 md:col-span-1">
                                <label class="hidden text-[9px] font-extrabold uppercase tracking-widest text-slate-400 opacity-0 md:block">Action</label>
                                <div class="flex h-10 w-10 self-center rounded-xl bg-slate-50 text-slate-300 transition-colors group-hover:bg-primary-50 group-hover:text-primary-600 items-center justify-center">
                                     <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>

                <div class="space-y-2">
                    <label for="notes" class="text-sm font-semibold text-slate-700">Notes</label>
                    <textarea id="notes" name="notes" rows="4" class="{{ $textareaClass }}">{{ old('notes', $editingOrder?->notes) }}</textarea>
                </div>

                <div class="flex flex-wrap items-center justify-end gap-3">
                    <a href="{{ route('orders.index') }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Cancel</a>
                    <button type="submit" class="inline-flex h-11 items-center justify-center rounded-xl bg-primary-600 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700">{{ $editingOrder ? 'Update Order' : 'Create Order' }}</button>
                </div>
            </form>
        </div>

        <div class="space-y-6">
            {{-- Search & Filters --}}
            <div class="rounded-[2.5rem] border border-slate-200/60 bg-white/70 backdrop-blur-md p-6 shadow-sm sm:p-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">Procurement History</h2>
                        <p class="mt-0.5 text-sm text-slate-500">Track and manage your order lifecycle and documentation.</p>
                    </div>
                    <div class="flex items-center gap-3">
                         <p class="text-[13px] font-medium text-slate-600"><span id="ordersVisibleCount" class="font-bold text-slate-950">{{ $orderItems->count() }}</span> items found</p>
                    </div>
                </div>

                <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="relative group">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 transition-colors group-focus-within:text-primary-600">
                             <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </div>
                        <input id="ordersFilterSearch" type="text" placeholder="Search ID or company..." class="h-12 w-full rounded-[1.25rem] border-slate-200 bg-slate-50/50 pl-11 pr-4 text-[13px] font-medium text-slate-900 outline-none transition focus:border-primary-600 focus:ring-4 focus:ring-primary-600/10">
                    </div>
                    <div class="relative">
                        <select id="ordersFilterStatus" class="h-12 w-full appearance-none rounded-[1.25rem] border-0 bg-transparent px-4 text-[13px] font-medium text-slate-900 shadow-none outline-none transition focus:border-transparent focus:ring-0 accent-primary-600 text-slate-500">
                            <option value="all">All statuses</option>
                            <option value="draft">Draft</option>
                            <option value="submitted">Submitted</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <div class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-slate-400">
                             <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </div>
                    </div>
                    <div class="relative">
                        <select id="ordersFilterCompany" class="h-12 w-full appearance-none rounded-[1.25rem] border-0 bg-transparent px-4 text-[13px] font-medium text-slate-900 shadow-none outline-none transition focus:border-transparent focus:ring-0 accent-primary-600 text-slate-500">
                            <option value="all">All scopes</option>
                            @foreach ($companyOptions as $companyOption)
                                <option value="{{ strtolower($companyOption) }}">{{ $companyOption }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-slate-400">
                             <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </div>
                    </div>
                    <button id="ordersFilterReset" class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-[1.25rem] border border-slate-200 bg-white px-4 text-[13px] font-bold text-slate-700 shadow-sm transition hover:bg-slate-50">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        Reset Filters
                    </button>
                </div>
            </div>

            <div id="ordersList" class="space-y-4">
                @forelse ($orders as $order)
                    @php $companyName = $order->company?->name ?? 'Self'; @endphp
                    <article class="group relative overflow-hidden rounded-[2.5rem] border-y border-r border-l-[4px] border-slate-200 border-l-transparent bg-white p-5 shadow-sm transition-all duration-300 hover:shadow-lg hover:shadow-slate-200/40 hover:border-l-primary-500 lg:p-6" 
                        data-order-card 
                        data-order-id="{{ $order->id }}" 
                        data-order-status="{{ strtolower((string) $order->status) }}" 
                        data-order-company="{{ strtolower($companyName) }}">
                        
                        <div class="flex flex-col gap-6 lg:flex-row lg:items-center">
                            {{-- Order ID & Status --}}
                            <div class="flex flex-shrink-0 items-center gap-4 lg:w-48">
                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-900">
                                    <span class="text-[13px] font-black">#{{ $order->id }}</span>
                                </div>
                                @if(strtolower((string) $order->status) === 'submitted')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-bold uppercase tracking-wider text-emerald-800">
                                        <span class="relative flex h-2 w-2">
                                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-500 opacity-60"></span>
                                            <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-600"></span>
                                        </span>
                                        Submitted
                                    </span>
                                @else
                                    <x-ui.status-badge type="status" :value="$order->status" :label="ucfirst($order->status)" />
                                @endif
                            </div>

                            {{-- Info Grid --}}
                            <div class="grid flex-1 gap-6 sm:grid-cols-3">
                                <div>
                                    <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Purchasing Entity</p>
                                    <p class="mt-1.5 text-[14px] font-bold text-slate-900 truncate" title="{{ $companyName }}">{{ $companyName }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Submitted On</p>
                                    <p class="mt-1.5 text-[14px] font-bold text-slate-900">{{ optional($order->created_at)->format('M d, Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Transaction Total</p>
                                    <p class="mt-1.5 text-[14px] font-black text-primary-700">INR {{ number_format((float) $order->total_amount, 2) }}</p>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex flex-wrap items-center gap-2 lg:justify-end">
                                <button type="button" data-order-details-link data-order-url="{{ route('orders.show', $order->id) }}" class="inline-flex h-10 items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 text-[13px] font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:text-primary-700 hover:border-primary-200 cursor-pointer">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    View Details
                                </button>
                                <a href="{{ route('orders.index', ['edit_order_id' => $order->id]) }}" class="inline-flex h-10 items-center justify-center rounded-xl bg-slate-900 px-4 text-[13px] font-bold text-white shadow-sm transition hover:bg-slate-800 no-underline">Modify</a>
                                <form method="POST" action="{{ route('orders.reorder', $order->id) }}" class="inline">
                                    @csrf 
                                    <button type="submit" class="inline-flex h-10 items-center justify-center rounded-xl bg-emerald-600 px-4 text-[13px] font-bold text-white shadow-sm transition hover:bg-emerald-700">Reorder</button>
                                </form>
                                <form method="POST" action="{{ route('orders.destroy', $order->id) }}" onsubmit="return confirm('Archive this record?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex h-10 items-center justify-center rounded-xl border border-rose-100 bg-rose-50 px-4 text-[13px] font-bold text-rose-600 transition hover:bg-rose-100">Delete</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-[2.5rem] border-2 border-dashed border-slate-200 py-16 text-center">
                         <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-50 text-slate-400">
                             <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                         </div>
                         <h3 class="mt-4 text-base font-bold text-slate-900">No Orders Found</h3>
                         <p class="mt-1 text-sm text-slate-500">You haven't initiated any procurements yet.</p>
                    </div>
                @endforelse
            </div>

            @if ($orderItems->count())
                <div id="ordersFilterEmpty" class="hidden rounded-[2.5rem] border border-slate-200 bg-slate-50 px-4 py-16 text-center">
                    <p class="text-base font-bold text-slate-900">No results match your criteria</p>
                    <p class="mt-2 text-sm text-slate-500">Adjustment your search term or filters to find what you're looking for.</p>
                </div>
            @endif

            <div class="mt-8">
                <x-ui.pagination :paginator="$orders" />
            </div>
        </div>
    </x-account.workspace>

    {{-- Order Details Modal --}}
    <div id="order-details-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-300 sm:p-7">
        <button
            type="button"
            class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
            aria-label="Close order details"
            data-modal-close="order-details-modal"
        ></button>

        <div
            id="order-details-modal-panel"
            class="relative flex w-[calc(100vw-2rem)] max-w-[720px] scale-95 flex-col overflow-hidden rounded-[1.5rem] bg-white shadow-2xl transition duration-300 sm:w-[calc(100vw-5rem)] sm:rounded-[2rem]"
        >
            <button
                type="button"
                class="absolute right-4 top-4 z-10 inline-flex h-10 w-10 items-center justify-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                aria-label="Close order details"
                data-modal-close="order-details-modal"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div id="order-details-modal-content" class="flex-1 overflow-hidden p-3 sm:p-5 lg:p-6">
                <div class="flex flex-col items-center justify-center py-20 text-slate-400">
                    <div class="h-10 w-10 animate-spin rounded-full border-4 border-slate-200 border-t-primary-600"></div>
                    <p class="mt-4 text-xs font-bold uppercase tracking-widest">Retrieving Record...</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('ordersFilterSearch');
            const statusSelect = document.getElementById('ordersFilterStatus');
            const companySelect = document.getElementById('ordersFilterCompany');
            const resetButton = document.getElementById('ordersFilterReset');
            const visibleCount = document.getElementById('ordersVisibleCount');
            const emptyState = document.getElementById('ordersFilterEmpty');
            const orderCards = Array.from(document.querySelectorAll('[data-order-card]'));

            if (orderCards.length && searchInput && statusSelect && companySelect && resetButton && visibleCount) {
                const applyFilters = function () {
                    const searchValue = searchInput.value.trim().toLowerCase();
                    const statusValue = statusSelect.value;
                    const companyValue = companySelect.value;
                    let shown = 0;

                    orderCards.forEach(function (card) {
                        const orderId = String(card.dataset.orderId || '').toLowerCase();
                        const orderStatus = String(card.dataset.orderStatus || '').toLowerCase();
                        const orderCompany = String(card.dataset.orderCompany || '').toLowerCase();

                        const matchesSearch = searchValue === '' || orderId.includes(searchValue) || orderCompany.includes(searchValue);
                        const matchesStatus = statusValue === 'all' || orderStatus === statusValue;
                        const matchesCompany = companyValue === 'all' || orderCompany === companyValue;
                        const isVisible = matchesSearch && matchesStatus && matchesCompany;

                        card.classList.toggle('hidden', !isVisible);
                        if (isVisible) {
                            shown += 1;
                        }
                    });

                    visibleCount.textContent = String(shown);
                    if (emptyState) {
                        emptyState.classList.toggle('hidden', shown !== 0);
                    }
                };

                searchInput.addEventListener('input', applyFilters);
                statusSelect.addEventListener('change', applyFilters);
                companySelect.addEventListener('change', applyFilters);
                resetButton.addEventListener('click', function () {
                    searchInput.value = '';
                    statusSelect.value = 'all';
                    companySelect.value = 'all';
                    applyFilters();
                });

                applyFilters();
            }

            // ─── Order Details Modal Logic ───
            const detailsModal = document.getElementById('order-details-modal');
            const detailsContent = document.getElementById('order-details-modal-content');
            const detailsPanel = document.getElementById('order-details-modal-panel');

            function openDetailsModal() {
                if (!detailsModal || !detailsPanel || !detailsContent) return;
                detailsContent.scrollTop = 0;
                detailsContent.scrollLeft = 0;
                detailsModal.classList.remove('hidden');
                detailsModal.classList.add('flex');
                requestAnimationFrame(function () {
                    detailsModal.classList.remove('opacity-0');
                    detailsPanel.classList.remove('scale-95');
                    detailsPanel.classList.add('scale-100');
                });
            }

            function closeDetailsModal() {
                if (!detailsModal || !detailsPanel || !detailsContent) return;
                detailsModal.classList.add('opacity-0');
                detailsPanel.classList.add('scale-95');
                detailsPanel.classList.remove('scale-100');
                setTimeout(function () {
                    detailsModal.classList.add('hidden');
                    detailsModal.classList.remove('flex');
                    detailsContent.scrollTop = 0;
                    detailsContent.scrollLeft = 0;
                }, 300);
            }

            // Open modal on View Details click
            document.querySelectorAll('[data-order-details-link]').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    var url = btn.getAttribute('data-order-url') || btn.getAttribute('href');
                    if (!url || !detailsContent) return;

                    // Show loader
                    detailsContent.innerHTML = '<div class="flex flex-col items-center justify-center py-20 text-slate-400"><div class="h-10 w-10 animate-spin rounded-full border-4 border-slate-200 border-t-primary-600"></div><p class="mt-4 text-xs font-bold uppercase tracking-widest">Retrieving Record...</p></div>';

                    openDetailsModal();

                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(function (response) { return response.text(); })
                        .then(function (html) {
                            detailsContent.innerHTML = html;
                            detailsContent.scrollTop = 0;
                            detailsContent.scrollLeft = 0;
                        })
                        .catch(function (error) {
                            console.error('Error fetching order details:', error);
                            detailsContent.innerHTML = '<div class="py-12 text-center text-rose-500"><p class="font-bold">Failed to load details</p><p class="text-xs">Please try again or refresh the page.</p></div>';
                        });
                });
            });

            // Close: event delegation for dynamically loaded close buttons
            document.addEventListener('click', function (e) {
                var closeBtn = e.target.closest('[data-modal-close="order-details-modal"]');
                if (closeBtn) {
                    closeDetailsModal();
                }
                // Also close on backdrop click
                if (e.target.closest('.modal-close') && detailsModal && detailsModal.contains(e.target)) {
                    closeDetailsModal();
                }
            });

            // Close on Escape
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && detailsModal && !detailsModal.classList.contains('hidden')) {
                    closeDetailsModal();
                }
            });
        });
    </script>
@endpush

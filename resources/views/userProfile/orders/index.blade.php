@extends('layouts.customer')

@php
    $portal = auth()->user()?->user_type ?? request('user_type', request('portal', 'b2c'));
    $portal = $portal === 'b2b' ? 'b2b' : 'b2c';

    $panelClass = 'rounded-2xl border border-slate-100 bg-white p-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] md:p-7';
    $compactPanelClass = 'rounded-2xl border border-slate-100 bg-white p-5 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] md:p-6';
    $buttonBaseClass = 'inline-flex h-10 items-center justify-center rounded-xl px-5 text-[13px] font-bold transition focus-visible:outline-none';
    $statusFilters = ['All', 'Processing', 'Shipped', 'Delivered'];
    $periodFilters = ['Last 3 months', 'Last 6 months', '2023'];
    $statusStyles = [
        'delivered' => [
            'badge' => 'bg-primary-50 text-primary-600 ring-1 ring-inset ring-primary-200',
            'reorder' => 'bg-primary-600 text-white shadow-sm hover:bg-primary-700',
        ],
        'processing' => [
            'badge' => 'bg-secondary-50 text-secondary-700 ring-1 ring-inset ring-amber-200',
            'reorder' => 'bg-primary-600 text-white shadow-sm hover:bg-primary-700',
        ],
        'shipped' => [
            'badge' => 'bg-primary-50 text-primary-600 ring-1 ring-inset ring-primary-200',
            'reorder' => 'bg-primary-600 text-white shadow-sm hover:bg-primary-700',
        ],
        'archived' => [
            'badge' => 'bg-slate-100 text-slate-600 ring-1 ring-inset ring-slate-200',
            'reorder' => 'bg-slate-300 text-white',
        ],
    ];
@endphp

@section('title', 'My Orders')
@section('customer_active', 'orders')
@section('customer_minimal', 'minimal')

@section('customer_content')
    <x-account.workspace
        :portal="$portal"
        active="orders"
        title="Order History"
        description="Manage and track your laboratory supply procurements."
    >
        <section class="{{ $compactPanelClass }}">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end">
                <div class="min-w-0 lg:max-w-[65%] lg:flex-1">
                    <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-slate-400">Status</p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach ($statusFilters as $filter)
                            <button
                                type="button"
                                class="inline-flex h-10 items-center justify-center rounded-xl px-4 text-[13px] font-bold transition {{ $filter === 'All' ? 'bg-primary-600 text-white shadow-md hover:bg-primary-700' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900 shadow-sm' }}"
                            >
                                {{ $filter }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="min-w-0 lg:ml-auto lg:w-full lg:max-w-[35%]">
                    <label for="order-range" class="block text-[11px] font-bold uppercase tracking-[0.22em] text-slate-400">Date Range</label>
                    <div class="relative mt-3">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <select
                            id="order-range"
                            class="h-11 w-full appearance-none rounded-xl border border-slate-200 bg-slate-50 pl-10 pr-10 text-[13px] font-semibold text-slate-700 outline-none transition focus:border-primary-600 focus:ring-1 focus:ring-primary-600"
                        >
                            @foreach ($periodFilters as $period)
                                <option>{{ $period }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="space-y-5">
            @foreach ($orders as $index => $order)
                @php($statusStyle = $statusStyles[$order['status_key']] ?? $statusStyles['processing'])

                <article class="{{ $panelClass }}">
                    <div class="grid gap-6 xl:grid-cols-[auto_minmax(0,1fr)_auto] xl:items-center">
                        <div class="flex h-24 w-24 items-center justify-center overflow-hidden rounded-2xl {{ $order['image_background'] }} p-3 shadow-inner">
                            <img src="{{ $order['image'] }}" alt="{{ $order['product'] }}" class="h-full w-full rounded-xl object-cover mix-blend-multiply">
                        </div>

                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-3">
                                <span class="inline-flex rounded-lg px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wider {{ $statusStyle['badge'] }}">
                                    {{ $order['status'] }}
                                </span>
                                <span class="text-[12px] font-bold tracking-tight text-slate-400"># {{ $order['id'] }}</span>
                            </div>

                            <h3 class="mt-3 text-xl font-bold tracking-tight text-slate-900">{{ $order['product'] }}</h3>

                            <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-slate-500">
                                <span class="inline-flex items-center gap-2">
                                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ $order['date'] }}
                                </span>
                                <span class="text-lg font-bold text-slate-900">{{ $order['total'] }}</span>
                                <span class="inline-flex items-center rounded-full bg-slate-50 px-3 py-1 text-[12px] font-semibold text-slate-500">
                                    {{ $order['summary_note'] }}
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <button
                                type="button"
                                onclick="openOrderModal({{ $index }})"
                                class="{{ $buttonBaseClass }} border border-slate-200 bg-white text-slate-700 shadow-sm hover:bg-slate-50"
                            >
                                View Details
                            </button>
                            <form method="POST" action="{{ $order['reorder_url'] }}">
                                @csrf
                                <button
                                    type="submit"
                                    class="{{ $buttonBaseClass }} {{ $statusStyle['reorder'] }} {{ $order['status_key'] === 'archived' ? 'cursor-not-allowed opacity-70' : '' }}"
                                    @disabled($order['status_key'] === 'archived')
                                >
                                    Reorder
                                </button>
                            </form>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        @include('userProfile.orders.order-modal')
    </x-account.workspace>
@endsection

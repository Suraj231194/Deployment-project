@extends('layouts.customer')

@php
    $portal = auth()->user()?->user_type ?? request('user_type', request('portal', 'b2c'));
    $portal = $portal === 'b2b' ? 'b2b' : 'b2c';
    $backUrl = route('orders.index');
    $metricCardClass = 'rounded-3xl border border-slate-200 bg-white p-4 shadow-sm md:p-5';
    $panelClass = 'space-y-5 rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm md:p-8';
    $sidePanelClass = 'space-y-4 rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm';
@endphp

@section('title', $order ? 'Order #'.$order->id : 'Order Details')
@section('customer_active', 'orders')
@section('customer_minimal', 'minimal')

@section('customer_content')
    <x-account.workspace
        :portal="$portal"
        active="orders"
        :back-url="$backUrl"
        back-label="Back to Orders"
        :title="$order ? 'Order #'.$order->id : 'Order Details'"
        description="Review pricing, line items, and the saved commercial snapshot for this order."
    >
        @include('order.partials.order-details')
    </x-account.workspace>
@endsection

@extends('admin.layout')

@section('title', 'Order Management - Biogenix Admin')

@section('admin_content')

<div class="space-y-6">

    <!-- Breadcrumb -->
    <nav class="flex text-[13px] text-slate-500 font-medium mb-2">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-900 transition flex items-center gap-1.5">
            Admin
        </a>
        <span class="mx-2 text-slate-300">/</span>
        <span class="text-slate-900 font-semibold">Order Management</span>
    </nav>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Order Management</h1>
            <p class="text-sm text-slate-500 mt-1">Review and track medical supply chain operations across all regions.</p>
        </div>
        <button id="exportCsvBtn" onclick="exportOrdersCSV()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 transition shadow-sm shrink-0 cursor-pointer">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
            Export CSV
        </button>
    </div>

    <!-- Main Card container matching Dashboard specifications -->
    <div class="bg-white rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 overflow-hidden flex flex-col relative">
        
        <!-- Filter Bar -->
        <div class="px-5 lg:px-6 py-4 border-b border-slate-100 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            
            <!-- Search -->
            <div class="relative w-full lg:w-80">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input id="orderSearch" type="text" placeholder="Search Order ID, Client, or SKU..." class="w-full bg-slate-50 border border-slate-200 text-sm rounded-xl pl-9 pr-4 py-2.5 focus:bg-white focus:border-primary-600 focus:ring-1 focus:ring-primary-600 transition outline-none text-slate-800 placeholder:text-slate-400 font-medium">
            </div>

            <!-- Status Pills -->
            <div id="statusPills" class="flex items-center gap-2 overflow-x-auto pb-1 lg:pb-0 scrollbar-hide">
                <button data-status="all" class="status-pill active inline-flex items-center justify-center whitespace-nowrap px-4 py-2 rounded-full text-xs font-bold bg-primary-600 text-white cursor-pointer">All Orders</button>
                <button data-status="Pending" class="status-pill inline-flex items-center justify-center whitespace-nowrap px-4 py-2 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200/60 hover:bg-amber-100 transition cursor-pointer">Pending</button>
                <button data-status="Processing" class="status-pill inline-flex items-center justify-center whitespace-nowrap px-4 py-2 rounded-full text-xs font-bold bg-primary-50 text-primary-700 border border-primary-200/60 hover:bg-primary-100 transition cursor-pointer">Processing</button>
                <button data-status="Dispatched" class="status-pill inline-flex items-center justify-center whitespace-nowrap px-4 py-2 rounded-full text-xs font-bold bg-violet-50 text-violet-700 border border-violet-200/60 hover:bg-violet-100 transition cursor-pointer">Dispatched</button>
                <button data-status="Delivered" class="status-pill inline-flex items-center justify-center whitespace-nowrap px-4 py-2 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60 hover:bg-emerald-100 transition cursor-pointer">Delivered</button>
                <button data-status="Cancelled" class="status-pill inline-flex items-center justify-center whitespace-nowrap px-4 py-2 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200/60 hover:bg-rose-100 transition cursor-pointer">Cancelled</button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table id="ordersTable" class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-white border-b border-slate-100">
                        <th class="px-5 lg:px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest cursor-pointer hover:text-slate-600 transition" data-sort="id">Order ID <span class="sort-icon">↕</span></th>
                        <th class="px-5 lg:px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest cursor-pointer hover:text-slate-600 transition" data-sort="customer">Customer Name <span class="sort-icon">↕</span></th>
                        <th class="px-5 lg:px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest cursor-pointer hover:text-slate-600 transition" data-sort="date">Date <span class="sort-icon">↕</span></th>
                        <th class="px-5 lg:px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest cursor-pointer hover:text-slate-600 transition" data-sort="amount">Total Amount <span class="sort-icon">↕</span></th>
                        <th class="px-5 lg:px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Payment Status</th>
                        <th class="px-5 lg:px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Fulfillment</th>
                        <th class="px-5 lg:px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    
                    <!-- Row 1 -->
                    <tr class="hover:bg-slate-50/50 transition-colors group cursor-pointer">
                        <td class="px-5 lg:px-6 py-4">
                            <span class="text-[13px] font-bold text-slate-900">#ORD-99281</span>
                        </td>
                        <td class="px-5 lg:px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-[13px] font-bold text-slate-900">Mount Sinai Hospital</span>
                                <span class="text-[12px] font-medium text-slate-400">New York, NY</span>
                            </div>
                        </td>
                        <td class="px-5 lg:px-6 py-4">
                            <span class="text-[13px] font-semibold text-slate-600">Oct 24, 2023</span>
                        </td>
                        <td class="px-5 lg:px-6 py-4">
                            <span class="text-[13px] font-bold text-slate-900">$1,240.00</span>
                        </td>
                        <td class="px-5 lg:px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200/60 text-[11px] font-bold rounded-md">Paid</span>
                        </td>
                        <td class="px-5 lg:px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 bg-primary-50 text-primary-700 border border-primary-200/60 text-[11px] font-bold rounded-full">Processing</span>
                        </td>
                        <td class="px-5 lg:px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <x-ui.action-icon type="view" href="{{ route('admin.orders.view') }}" class="ajax-link">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </x-ui.action-icon>
                                <x-ui.action-icon type="document">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                </x-ui.action-icon>
                                <x-ui.action-icon type="edit">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </x-ui.action-icon>
                            </div>
                        </td>
                    </tr>

                    <!-- Row 2 -->
                    <tr class="hover:bg-slate-50/50 transition-colors group cursor-pointer">
                        <td class="px-5 lg:px-6 py-4">
                            <span class="text-[13px] font-bold text-slate-900">#ORD-99280</span>
                        </td>
                        <td class="px-5 lg:px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-[13px] font-bold text-slate-900">Genotech Labs</span>
                                <span class="text-[12px] font-medium text-slate-400">San Francisco, CA</span>
                            </div>
                        </td>
                        <td class="px-5 lg:px-6 py-4">
                            <span class="text-[13px] font-semibold text-slate-600">Oct 23, 2023</span>
                        </td>
                        <td class="px-5 lg:px-6 py-4">
                            <span class="text-[13px] font-bold text-slate-900">$850.00</span>
                        </td>
                        <td class="px-5 lg:px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-200/60 text-[11px] font-bold rounded-md">Pending</span>
                        </td>
                        <td class="px-5 lg:px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 bg-slate-50 text-slate-600 border border-slate-200/60 text-[11px] font-bold rounded-full">Hold</span>
                        </td>
                        <td class="px-5 lg:px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <x-ui.action-icon type="view" href="{{ route('admin.orders.view') }}" class="ajax-link">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </x-ui.action-icon>
                                <x-ui.action-icon type="document">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                </x-ui.action-icon>
                                <x-ui.action-icon type="edit">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </x-ui.action-icon>
                            </div>
                        </td>
                    </tr>

                    <!-- Row 3 -->
                    <tr class="hover:bg-slate-50/50 transition-colors group cursor-pointer">
                        <td class="px-5 lg:px-6 py-4">
                            <span class="text-[13px] font-bold text-slate-900">#ORD-99279</span>
                        </td>
                        <td class="px-5 lg:px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-[13px] font-bold text-slate-900">Dr. Aris Thorne</span>
                                <span class="text-[12px] font-medium text-slate-400">Private Practice</span>
                            </div>
                        </td>
                        <td class="px-5 lg:px-6 py-4">
                            <span class="text-[13px] font-semibold text-slate-600">Oct 23, 2023</span>
                        </td>
                        <td class="px-5 lg:px-6 py-4">
                            <span class="text-[13px] font-bold text-slate-900">$2,100.00</span>
                        </td>
                        <td class="px-5 lg:px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200/60 text-[11px] font-bold rounded-md">Paid</span>
                        </td>
                        <td class="px-5 lg:px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 bg-violet-50 text-violet-700 border border-violet-200/60 text-[11px] font-bold rounded-full">Dispatched</span>
                        </td>
                        <td class="px-5 lg:px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <x-ui.action-icon type="view" href="{{ route('admin.orders.view') }}" class="ajax-link">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </x-ui.action-icon>
                                <x-ui.action-icon type="document">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                </x-ui.action-icon>
                                <x-ui.action-icon type="edit">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </x-ui.action-icon>
                            </div>
                        </td>
                    </tr>

                    <!-- Row 4 -->
                    <tr class="hover:bg-slate-50/50 transition-colors group cursor-pointer">
                        <td class="px-5 lg:px-6 py-4">
                            <span class="text-[13px] font-bold text-slate-900">#ORD-99278</span>
                        </td>
                        <td class="px-5 lg:px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-[13px] font-bold text-slate-900">BioHealth Clinic</span>
                                <span class="text-[12px] font-medium text-slate-400">London, UK</span>
                            </div>
                        </td>
                        <td class="px-5 lg:px-6 py-4">
                            <span class="text-[13px] font-semibold text-slate-600">Oct 22, 2023</span>
                        </td>
                        <td class="px-5 lg:px-6 py-4">
                            <span class="text-[13px] font-bold text-slate-900">$450.00</span>
                        </td>
                        <td class="px-5 lg:px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 bg-violet-50 text-violet-700 border border-violet-200/60 text-[11px] font-bold rounded-md">Refunded</span>
                        </td>
                        <td class="px-5 lg:px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 bg-rose-50 text-rose-700 border border-rose-200/60 text-[11px] font-bold rounded-full">Cancelled</span>
                        </td>
                        <td class="px-5 lg:px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <x-ui.action-icon type="view" href="{{ route('admin.orders.view') }}" class="ajax-link">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </x-ui.action-icon>
                                <x-ui.action-icon type="document">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                </x-ui.action-icon>
                                <x-ui.action-icon type="edit">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </x-ui.action-icon>
                            </div>
                        </td>
                    </tr>

                    <!-- Row 5 -->
                    <tr class="hover:bg-slate-50/50 transition-colors group cursor-pointer">
                        <td class="px-5 lg:px-6 py-4">
                            <span class="text-[13px] font-bold text-slate-900">#ORD-99277</span>
                        </td>
                        <td class="px-5 lg:px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-[13px] font-bold text-slate-900">St. Jude Research</span>
                                <span class="text-[12px] font-medium text-slate-400">Memphis, TN</span>
                            </div>
                        </td>
                        <td class="px-5 lg:px-6 py-4">
                            <span class="text-[13px] font-semibold text-slate-600">Oct 21, 2023</span>
                        </td>
                        <td class="px-5 lg:px-6 py-4">
                            <span class="text-[13px] font-bold text-slate-900">$3,200.00</span>
                        </td>
                        <td class="px-5 lg:px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200/60 text-[11px] font-bold rounded-md">Paid</span>
                        </td>
                        <td class="px-5 lg:px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200/60 text-[11px] font-bold rounded-full">Delivered</span>
                        </td>
                        <td class="px-5 lg:px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <x-ui.action-icon type="view" href="{{ route('admin.orders.view') }}" class="ajax-link">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </x-ui.action-icon>
                                <x-ui.action-icon type="document">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                </x-ui.action-icon>
                                <x-ui.action-icon type="edit">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </x-ui.action-icon>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-5 lg:px-6 py-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p id="orderCount" class="text-[13px] text-slate-500 font-medium">
                Showing 5 of 5 results
            </p>
            <div class="flex items-center gap-2">
                <button class="h-9 w-9 flex items-center justify-center rounded border border-slate-200 text-slate-400 bg-white hover:bg-slate-50 transition cursor-pointer">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                </button>
                <div class="flex font-semibold text-[13px]">
                    <button class="h-9 w-9 flex items-center justify-center rounded bg-primary-600 text-white cursor-pointer">1</button>
                    <button class="h-9 w-9 flex items-center justify-center rounded bg-white text-slate-600 hover:bg-slate-50 transition border border-transparent hover:border-slate-200 cursor-pointer">2</button>
                    <button class="h-9 w-9 flex items-center justify-center rounded bg-white text-slate-600 hover:bg-slate-50 transition border border-transparent hover:border-slate-200 cursor-pointer">3</button>
                </div>
                <button class="h-9 w-9 flex items-center justify-center rounded border border-slate-200 text-slate-400 bg-white hover:bg-slate-50 transition cursor-pointer">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                </button>
            </div>
        </div>

    </div>

</div>

@endsection

@push('scripts')
<script>
// ─── Status pill filter ───
document.querySelectorAll('.status-pill').forEach(pill => {
    pill.addEventListener('click', () => {
        document.querySelectorAll('.status-pill').forEach(p => {
            p.classList.remove('bg-primary-600', 'text-white', 'active', 'border-transparent');
            // Restore original subtle classes based on data-status
            const s = p.dataset.status;
            let cls = 'bg-slate-50 text-slate-600 border-slate-200';
            if(s === 'Pending') cls = 'bg-amber-50 text-amber-700 border-amber-200/60';
            else if(s === 'Processing') cls = 'bg-primary-50 text-primary-700 border-primary-200/60';
            else if(s === 'Dispatched') cls = 'bg-violet-50 text-violet-700 border-violet-200/60';
            else if(s === 'Delivered') cls = 'bg-emerald-50 text-emerald-700 border-emerald-200/60';
            else if(s === 'Cancelled') cls = 'bg-rose-50 text-rose-700 border-rose-200/60';
            
            p.className = `status-pill inline-flex items-center justify-center whitespace-nowrap px-4 py-2 rounded-full text-xs font-bold border transition cursor-pointer ${cls}`;
        });
        pill.classList.remove('bg-slate-50', 'bg-amber-50', 'bg-primary-50', 'bg-violet-50', 'bg-emerald-50', 'bg-rose-50', 'text-slate-600', 'text-amber-700', 'text-primary-700', 'text-violet-700', 'text-emerald-700', 'text-rose-700', 'border-slate-200', 'border-amber-200/60', 'border-primary-200/60', 'border-violet-200/60', 'border-emerald-200/60', 'border-rose-200/60');
        pill.classList.add('bg-primary-600', 'text-white', 'active', 'border-transparent');
        filterOrders();
    });
});

// ─── Search filter ───
document.getElementById('orderSearch')?.addEventListener('input', filterOrders);

function filterOrders() {
    const search = (document.getElementById('orderSearch')?.value || '').toLowerCase();
    const activeStatus = document.querySelector('.status-pill.active')?.dataset.status || 'all';
    const rows = document.querySelectorAll('#ordersTable tbody tr');
    let visible = 0;
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const fulfillment = row.querySelector('td:nth-child(6)')?.textContent.trim() || '';
        const matchSearch = !search || text.includes(search);
        const matchStatus = activeStatus === 'all' || fulfillment === activeStatus;
        row.style.display = (matchSearch && matchStatus) ? '' : 'none';
        if (matchSearch && matchStatus) visible++;
    });
    const countEl = document.getElementById('orderCount');
    if (countEl) countEl.textContent = `Showing ${visible} of ${rows.length} results`;
}

// ─── Column sorting ───
document.querySelectorAll('#ordersTable th[data-sort]').forEach(th => {
    th.addEventListener('click', () => {
        const key = th.dataset.sort;
        const tbody = document.querySelector('#ordersTable tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const asc = th.classList.toggle('sort-asc');
        rows.sort((a, b) => {
            let va, vb;
            if (key === 'id') { va = a.cells[0].textContent; vb = b.cells[0].textContent; }
            else if (key === 'customer') { va = a.cells[1].textContent.trim(); vb = b.cells[1].textContent.trim(); }
            else if (key === 'date') { va = new Date(a.cells[2].textContent.trim()); vb = new Date(b.cells[2].textContent.trim()); return asc ? va - vb : vb - va; }
            else if (key === 'amount') { va = parseFloat(a.cells[3].textContent.replace(/[^0-9.]/g,'')); vb = parseFloat(b.cells[3].textContent.replace(/[^0-9.]/g,'')); return asc ? va - vb : vb - va; }
            return asc ? va.localeCompare(vb) : vb.localeCompare(va);
        });
        rows.forEach(r => tbody.appendChild(r));
        document.querySelectorAll('#ordersTable .sort-icon').forEach(s => s.textContent = '↕');
        th.querySelector('.sort-icon').textContent = asc ? '↑' : '↓';
    });
});

// ─── Export CSV ───
function exportOrdersCSV() {
    const rows = document.querySelectorAll('#ordersTable tbody tr');
    let csv = 'Order ID,Customer,Date,Amount,Payment,Fulfillment\n';
    rows.forEach(row => {
        if (row.style.display === 'none') return;
        const cells = row.querySelectorAll('td');
        csv += `${cells[0].textContent.trim()},"${cells[1].textContent.trim().replace(/\s+/g,' ')}",${cells[2].textContent.trim()},${cells[3].textContent.trim()},${cells[4].textContent.trim()},${cells[5].textContent.trim()}\n`;
    });
    const blob = new Blob([csv], {type: 'text/csv'});
    const a = document.createElement('a'); a.href = URL.createObjectURL(blob); a.download = 'orders_export.csv'; a.click();
    AdminToast.show('Orders exported successfully!', 'success');
}
</script>
@endpush

@extends('admin.layout')

@section('title', 'Quotation / PI Management - Biogenix Admin')

@section('admin_content')

<div class="space-y-6">

    <!-- Breadcrumb -->
    <nav class="flex text-[13px] text-slate-500 font-medium mb-2">
        <a href="{{ route('admin.dashboard') }}" class="ajax-link hover:text-slate-900 transition flex items-center gap-1.5 cursor-pointer">
            Admin
        </a>
        <span class="mx-2 text-slate-300">/</span>
        <span class="text-slate-900 font-semibold">Quotation / PI</span>
    </nav>

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Quotation / PI Management</h1>
            <p class="text-sm text-slate-500 mt-1">Manage, convert, and track Proforma Invoices.</p>
        </div>
        <div class="flex items-center gap-3 shrink-0">
            <!-- Search -->
            <div class="relative w-full sm:w-64 relative hidden sm:block">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" placeholder="Search PI, client..." class="w-full bg-slate-50 border border-slate-200 text-sm rounded-xl pl-9 pr-4 py-2 focus:bg-white focus:border-primary-600 focus:ring-1 focus:ring-primary-600 transition outline-none text-slate-800 placeholder:text-slate-400 font-medium">
            </div>
            <button class="px-4 py-2 rounded-lg text-sm font-bold text-white bg-primary-600 hover:bg-primary-700 transition shadow-sm flex items-center gap-2 cursor-pointer">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Branding Settings
            </button>
            <button class="h-9 w-9 flex items-center justify-center rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200 transition cursor-pointer">
                 <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
            </button>
        </div>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 overflow-hidden flex flex-col relative pb-2 mt-6">
        
        <!-- Top Toolbar List -->
        <div class="px-5 lg:px-6 py-5 border-b border-slate-100 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            
            <!-- Status Pills -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1 lg:pb-0 scrollbar-hide">
                <a href="#" class="inline-flex items-center justify-center whitespace-nowrap px-5 py-2 rounded-full text-[13px] font-bold bg-primary-600 text-white">All</a>
                <a href="#" class="inline-flex items-center justify-center whitespace-nowrap px-5 py-2 rounded-full text-[13px] font-bold bg-amber-50 text-amber-700 border border-amber-200/60 hover:bg-amber-100 transition">Draft</a>
                <a href="#" class="inline-flex items-center justify-center whitespace-nowrap px-5 py-2 rounded-full text-[13px] font-bold bg-primary-50 text-primary-700 border border-primary-200/60 hover:bg-primary-100 transition">Sent</a>
                <a href="#" class="inline-flex items-center justify-center whitespace-nowrap px-5 py-2 rounded-full text-[13px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60 hover:bg-emerald-100 transition">Converted</a>
                <a href="#" class="inline-flex items-center justify-center whitespace-nowrap px-5 py-2 rounded-full text-[13px] font-bold bg-rose-50 text-rose-700 border border-rose-200/60 hover:bg-rose-100 transition">Expired</a>
            </div>

            <!-- Add Button -->
            <a href="{{ route('admin.pi-quotation.create') }}" class="ajax-link inline-flex shrink-0 items-center justify-center px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-primary-600 hover:bg-primary-700 transition shadow-[0_2px_10px_-3px_rgba(26,77,46,0.2)] gap-2 cursor-pointer">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                Create New PI
            </a>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-white border-b border-slate-100">
                        <th class="px-5 lg:px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">PI Number</th>
                        <th class="px-5 lg:px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Customer Name</th>
                        <th class="px-5 lg:px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Date</th>
                        <th class="px-5 lg:px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Amount</th>
                        <th class="px-5 lg:px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center">Status</th>
                        <th class="px-5 lg:px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    
                    <!-- Row 1 -->
                    <tr class="hover:bg-slate-50/50 transition-colors group cursor-pointer">
                        <td class="px-5 lg:px-6 py-5">
                            <span class="text-[14px] font-bold text-slate-900">PI-2023-001</span>
                        </td>
                        <td class="px-5 lg:px-6 py-5">
                            <div class="flex flex-col">
                                <span class="text-[14px] font-bold text-slate-900">Global Lab Corp</span>
                                <span class="text-[12px] font-medium text-slate-400">contact@globallab.com</span>
                            </div>
                        </td>
                        <td class="px-5 lg:px-6 py-5">
                            <span class="text-[14px] font-semibold text-slate-500">Oct 12, 2023</span>
                        </td>
                        <td class="px-5 lg:px-6 py-5">
                            <span class="text-[14px] font-extrabold text-slate-900 tracking-tight">$12,450.00</span>
                        </td>
                        <td class="px-5 lg:px-6 py-5 text-center">
                            <span class="inline-flex items-center px-3 py-1 bg-primary-50 text-primary-700 border border-primary-200/60 text-[10px] font-black uppercase tracking-widest rounded-full">Sent</span>
                        </td>
                        <td class="px-5 lg:px-6 py-5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <x-ui.action-icon type="document" title="View PI Document">
                                    <svg class="h-[18px] w-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                </x-ui.action-icon>
                                <x-ui.action-icon type="tertiary" title="Convert PI">
                                    <svg class="h-[18px] w-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                                </x-ui.action-icon>
                                <x-ui.action-icon type="edit">
                                    <svg class="h-[18px] w-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </x-ui.action-icon>
                            </div>
                        </td>
                    </tr>

                    <!-- Row 2 -->
                    <tr class="hover:bg-slate-50/50 transition-colors group cursor-pointer">
                        <td class="px-5 lg:px-6 py-5">
                            <span class="text-[14px] font-bold text-slate-900">PI-2023-002</span>
                        </td>
                        <td class="px-5 lg:px-6 py-5">
                            <div class="flex flex-col">
                                <span class="text-[14px] font-bold text-slate-900">BioResearch Inc</span>
                            </div>
                        </td>
                        <td class="px-5 lg:px-6 py-5">
                            <span class="text-[14px] font-semibold text-slate-500">Oct 14, 2023</span>
                        </td>
                        <td class="px-5 lg:px-6 py-5">
                            <span class="text-[14px] font-extrabold text-slate-900 tracking-tight">$8,200.00</span>
                        </td>
                        <td class="px-5 lg:px-6 py-5 text-center">
                            <span class="inline-flex items-center px-3 py-1 bg-amber-50 text-amber-700 border border-amber-200/60 text-[10px] font-black uppercase tracking-widest rounded-full">Draft</span>
                        </td>
                        <td class="px-5 lg:px-6 py-5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <x-ui.action-icon type="document" title="View PI Document">
                                    <svg class="h-[18px] w-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                </x-ui.action-icon>
                                <x-ui.action-icon type="tertiary" title="Convert PI">
                                    <svg class="h-[18px] w-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                                </x-ui.action-icon>
                                <x-ui.action-icon type="edit">
                                    <svg class="h-[18px] w-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </x-ui.action-icon>
                            </div>
                        </td>
                    </tr>

                    <!-- Row 3 -->
                    <tr class="hover:bg-slate-50/50 transition-colors group cursor-pointer">
                        <td class="px-5 lg:px-6 py-5">
                            <span class="text-[14px] font-bold text-slate-900">PI-2023-003</span>
                        </td>
                        <td class="px-5 lg:px-6 py-5">
                            <div class="flex flex-col">
                                <span class="text-[14px] font-bold text-slate-900">HealthTech Solutions</span>
                            </div>
                        </td>
                        <td class="px-5 lg:px-6 py-5">
                            <span class="text-[14px] font-semibold text-slate-500">Oct 15, 2023</span>
                        </td>
                        <td class="px-5 lg:px-6 py-5">
                            <span class="text-[14px] font-extrabold text-slate-900 tracking-tight">$25,000.00</span>
                        </td>
                        <td class="px-5 lg:px-6 py-5 text-center">
                            <span class="inline-flex items-center px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200/60 text-[10px] font-black uppercase tracking-widest rounded-full">Converted</span>
                        </td>
                        <td class="px-5 lg:px-6 py-5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <x-ui.action-icon type="document" title="View PI Document">
                                    <svg class="h-[18px] w-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                </x-ui.action-icon>
                                <x-ui.action-icon type="tertiary" title="Convert PI">
                                    <svg class="h-[18px] w-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                                </x-ui.action-icon>
                                <x-ui.action-icon type="edit">
                                    <svg class="h-[18px] w-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </x-ui.action-icon>
                            </div>
                        </td>
                    </tr>

                    <!-- Row 4 -->
                    <tr class="hover:bg-slate-50/50 transition-colors group cursor-pointer">
                        <td class="px-5 lg:px-6 py-5">
                            <span class="text-[14px] font-bold text-slate-900">PI-2023-004</span>
                        </td>
                        <td class="px-5 lg:px-6 py-5">
                            <div class="flex flex-col">
                                <span class="text-[14px] font-bold text-slate-900">Genomics Center</span>
                            </div>
                        </td>
                        <td class="px-5 lg:px-6 py-5">
                            <span class="text-[14px] font-semibold text-slate-500">Oct 10, 2023</span>
                        </td>
                        <td class="px-5 lg:px-6 py-5">
                            <span class="text-[14px] font-extrabold text-slate-900 tracking-tight">$5,100.00</span>
                        </td>
                        <td class="px-5 lg:px-6 py-5 text-center">
                            <span class="inline-flex items-center px-3 py-1 bg-rose-50 text-rose-700 border border-rose-200/60 text-[10px] font-black uppercase tracking-widest rounded-full">Expired</span>
                        </td>
                        <td class="px-5 lg:px-6 py-5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <x-ui.action-icon type="document" title="View PI Document">
                                    <svg class="h-[18px] w-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                </x-ui.action-icon>
                                <x-ui.action-icon type="tertiary" title="Convert PI">
                                    <svg class="h-[18px] w-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                                </x-ui.action-icon>
                                <x-ui.action-icon type="edit">
                                    <svg class="h-[18px] w-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </x-ui.action-icon>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-5 lg:px-6 py-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-[13px] text-slate-500 font-medium">
                Showing 1-4 of 42 results
            </p>
            <div class="flex items-center gap-2">
                <button class="h-9 w-9 flex items-center justify-center rounded border border-slate-200 text-slate-400 bg-white hover:bg-slate-50 transition cursor-pointer">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                </button>
                <div class="flex font-semibold text-[13px]">
                    <button class="h-9 w-9 flex items-center justify-center rounded bg-primary-600 text-white cursor-pointer">1</button>
                    <button class="h-9 w-9 flex items-center justify-center rounded bg-white text-slate-600 hover:bg-slate-50 transition border border-transparent hover:border-slate-200 cursor-pointer">2</button>
                </div>
                <button class="h-9 w-9 flex items-center justify-center rounded border border-slate-200 text-slate-400 bg-white hover:bg-slate-50 transition cursor-pointer">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                </button>
            </div>
        </div>

    </div>

</div>

@endsection

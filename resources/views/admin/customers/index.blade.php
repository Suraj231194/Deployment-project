@extends('admin.layout')

@section('title', 'Customer Management - Biogenix Admin')

@section('admin_content')

    {{-- Breadcrumb --}}
    <nav class="flex text-[13px] text-slate-500 font-medium mb-2">
        <a href="{{ route('admin.dashboard') }}" class="ajax-link hover:text-slate-900 transition cursor-pointer">Admin</a>
        <span class="mx-2 text-slate-300">/</span>
        <span class="text-slate-900 font-semibold">Customer Management</span>
    </nav>

    {{-- Page Header --}}
    <div class="mb-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Customer Management</h1>
            <p class="text-sm text-slate-500 mt-1">Manage customer accounts, verifications, and access settings.</p>
        </div>
        {{-- Global Search + Add Customer --}}
        <div class="flex items-center gap-3">
            <div class="relative w-64">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input id="global-customer-search" type="text" placeholder="Global search..." class="w-full bg-slate-50 border border-slate-200 text-sm rounded-xl pl-9 pr-4 py-2.5 focus:bg-white focus:border-primary-600 focus:ring-1 focus:ring-primary-600 transition outline-none text-slate-800 placeholder:text-slate-400 font-medium">
            </div>
        </div>
    </div>

    {{-- ─── Pending Verifications Banner ─── --}}
    <div id="pending-verifications-section" class="bg-slate-100 border border-slate-200/60 rounded-2xl px-5 py-4 mb-5">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2.5">
                <svg class="h-5 w-5 text-secondary-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                <span class="text-sm font-bold text-amber-800">Pending User Verifications (3)</span>
            </div>
            <button class="text-[12px] font-bold text-secondary-700 hover:text-amber-900 transition cursor-pointer">View All Pending</button>
        </div>
        <div class="space-y-2.5" id="pending-list">

            {{-- Pending Item 1 --}}
            <div class="flex items-center justify-between bg-white rounded-xl px-4 py-3 border border-slate-100 shadow-sm gap-4" data-pending-id="1">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="h-9 w-9 rounded-full bg-primary-600 text-white flex items-center justify-center text-[11px] font-black shrink-0">ML</div>
                    <div class="min-w-0">
                        <p class="text-[13px] font-bold text-slate-900 truncate">MediLab Solutions</p>
                        <p class="text-[11px] text-slate-500 font-medium truncate">contact@medilab.co &bull; Applied for B2B</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button onclick="handleVerification(this, 'approve', 1)" class="px-4 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-[12px] font-bold rounded-lg transition cursor-pointer">Approve</button>
                    <button onclick="handleVerification(this, 'reject', 1)" class="px-4 py-1.5 bg-white hover:bg-rose-50 text-rose-600 border border-rose-200 text-[12px] font-bold rounded-lg transition cursor-pointer">Reject</button>
                </div>
            </div>

            {{-- Pending Item 2 --}}
            <div class="flex items-center justify-between bg-white rounded-xl px-4 py-3 border border-slate-100 shadow-sm gap-4" data-pending-id="2">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="h-9 w-9 rounded-full bg-violet-600 text-white flex items-center justify-center text-[11px] font-black shrink-0">AP</div>
                    <div class="min-w-0">
                        <p class="text-[13px] font-bold text-slate-900 truncate">Arthur P. Morgon</p>
                        <p class="text-[11px] text-slate-500 font-medium truncate">arthur.m@gmail.com &bull; Applied for Retail</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button onclick="handleVerification(this, 'approve', 2)" class="px-4 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-[12px] font-bold rounded-lg transition cursor-pointer">Approve</button>
                    <button onclick="handleVerification(this, 'reject', 2)" class="px-4 py-1.5 bg-white hover:bg-rose-50 text-rose-600 border border-rose-200 text-[12px] font-bold rounded-lg transition cursor-pointer">Reject</button>
                </div>
            </div>

            {{-- Pending Item 3 --}}
            <div class="flex items-center justify-between bg-white rounded-xl px-4 py-3 border border-slate-100 shadow-sm gap-4" data-pending-id="3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="h-9 w-9 rounded-full bg-primary-600 text-white flex items-center justify-center text-[11px] font-black shrink-0">BL</div>
                    <div class="min-w-0">
                        <p class="text-[13px] font-bold text-slate-900 truncate">BioLink Diagnostics</p>
                        <p class="text-[11px] text-slate-500 font-medium truncate">info@biolink.in &bull; Applied for B2B</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button onclick="handleVerification(this, 'approve', 3)" class="px-4 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-[12px] font-bold rounded-lg transition cursor-pointer">Approve</button>
                    <button onclick="handleVerification(this, 'reject', 3)" class="px-4 py-1.5 bg-white hover:bg-rose-50 text-rose-600 border border-rose-200 text-[12px] font-bold rounded-lg transition cursor-pointer">Reject</button>
                </div>
            </div>

        </div>
    </div>

    {{-- ─── Category & Eligibility Management ─── --}}
    <div class="bg-white rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 p-6 mb-5">
        <h2 class="text-base font-extrabold text-slate-900 mb-0.5">Category &amp; Eligibility Management</h2>
        <p class="text-[13px] text-slate-500 mb-5">Select a customer to configure access levels and credit limits</p>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Selected Customer Card --}}
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">Selected Customer</label>
                <div id="selected-customer-card" class="border-2 border-primary-600 bg-slate-100 rounded-xl px-4 py-4 cursor-pointer hover:shadow-md transition">
                    <p class="text-[14px] font-extrabold text-primary-800">Nova Scientific Group</p>
                    <p class="text-[12px] text-slate-500 font-medium mt-0.5">ID: #CUST-99021</p>
                    <span class="mt-2 inline-flex items-center px-2.5 py-1 bg-primary-600 text-white text-[10px] font-bold rounded-full">CURRENT: B2B</span>
                    <div class="mt-3">
                        <button id="change-selection-btn" class="text-[12px] font-bold text-primary-800 hover:underline focus:outline-none cursor-pointer">Change Selection</button>
                    </div>
                </div>
            </div>

            {{-- Credit Limit --}}
            <div class="flex flex-col justify-between">
                <div>
                    <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Credit Limit</label>
                    <div class="flex items-center border border-slate-200 rounded-xl overflow-hidden focus-within:border-primary-600 focus-within:ring-1 focus-within:ring-primary-600 bg-slate-50 transition">
                        <span class="px-3 py-3 text-slate-400 font-bold text-sm border-r border-slate-200 bg-slate-50 select-none">$</span>
                        <input id="credit-limit-input" type="number" value="25000" min="0" class="flex-1 px-3 py-3 text-sm font-semibold text-slate-900 bg-transparent outline-none">
                    </div>
                    <p class="text-[11px] text-slate-400 italic mt-1.5">Financial limit specifically for B2B wholesale accounts.</p>
                </div>
                <div class="mt-4 flex justify-end">
                    <button id="btn-update-params" onclick="updateParameters()" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-md shadow-primary-600/20 transition cursor-pointer">Update Parameters</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Customer Directory ─── --}}
    <div class="bg-white rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 overflow-hidden">

        {{-- Table Header --}}
        <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-extrabold text-slate-900">Customer Directory</h2>
                <p class="text-[13px] text-slate-500">Manage and filter your global customer database</p>
            </div>
            <div class="flex items-center gap-2">
                {{-- Category filter --}}
                <div class="relative">
                    <select id="category-filter" class="appearance-none bg-slate-50 border border-slate-200 text-[13px] font-semibold text-slate-700 rounded-lg px-3 py-2 pr-7 cursor-pointer outline-none hover:border-slate-300 transition focus:border-primary-600 focus:ring-1 focus:ring-primary-600">
                        <option value="">All Categories</option>
                        <option value="B2B">B2B</option>
                        <option value="Retail">Retail</option>
                        <option value="Guest">Guest</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-2 flex items-center">
                        <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
                {{-- Filters button --}}
                <button id="btn-filters" class="flex items-center gap-1.5 bg-slate-50 border border-slate-200 text-[13px] font-semibold text-slate-700 rounded-lg px-3 py-2 hover:border-slate-300 transition cursor-pointer">
                    <svg class="h-3.5 w-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                    Filters
                </button>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-white border-b border-slate-100">
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Customer Name</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Email Address</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Category</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Date Joined</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100" id="customer-table-body">

                    @php
                    $customers = [
                        ['name' => 'Nova Scientific Group',  'email' => 'contact@nova.com',      'category' => 'B2B',    'status' => 'Active',    'date' => 'Oct 12, 2023', 'initials' => 'NS', 'color' => '#1A4D2E'],
                        ['name' => 'David Wilson',           'email' => 'david.w@provider.net',   'category' => 'Retail', 'status' => 'Active',    'date' => 'Nov 05, 2023', 'initials' => 'DW', 'color' => '#7c3aed'],
                        ['name' => 'Bio-Chem Logistics',    'email' => 'billing@biochem.log',    'category' => 'B2B',    'status' => 'Suspended', 'date' => 'Aug 22, 2023', 'initials' => 'BC', 'color' => '#1A4D2E'],
                        ['name' => 'Elena Rodriguez',       'email' => 'elena.rod@webmail.com',  'category' => 'Guest',  'status' => 'Active',    'date' => 'Dec 01, 2023', 'initials' => 'ER', 'color' => '#059669'],
                        ['name' => 'Omni BioSystems Ltd',  'email' => 'ops@omnibiosys.com',      'category' => 'B2B',    'status' => 'Active',    'date' => 'Jan 15, 2024', 'initials' => 'OB', 'color' => '#d97706'],
                        ['name' => 'Clara Mendez',          'email' => 'c.mendez@gmail.com',     'category' => 'Retail', 'status' => 'Inactive',  'date' => 'Feb 20, 2024', 'initials' => 'CM', 'color' => '#db2777'],
                        ['name' => 'LabCore Sciences',     'email' => 'admin@labcore.io',        'category' => 'B2B',    'status' => 'Active',    'date' => 'Mar 03, 2024', 'initials' => 'LC', 'color' => '#0f766e'],
                        ['name' => 'Thomas Reinholt',      'email' => 't.reinholt@bionet.de',    'category' => 'Retail', 'status' => 'Active',    'date' => 'Mar 10, 2024', 'initials' => 'TR', 'color' => '#6d28d9'],
                    ];
                    @endphp

                    @foreach($customers as $c)
                    @php
                        $statusClasses = match($c['status']) {
                            'Active'    => 'bg-emerald-50 text-emerald-700 border border-emerald-200/60',
                            'Suspended' => 'bg-rose-50 text-rose-700 border border-rose-200/60',
                            'Inactive'  => 'bg-slate-100 text-slate-600 border border-slate-200/60',
                            default     => 'bg-slate-100 text-slate-600 border border-slate-200/60',
                        };
                        $catClasses = match($c['category']) {
                            'B2B'    => 'bg-primary-50 text-primary-700 border border-primary-200/60',
                            'Retail' => 'bg-violet-50 text-violet-700 border border-violet-200/60',
                            'Guest'  => 'bg-amber-50 text-amber-700 border border-amber-200/60',
                            default  => 'bg-slate-100 text-slate-600 border border-slate-200/60',
                        };
                    @endphp
                    <tr class="hover:bg-slate-50/50 transition-colors group customer-row cursor-pointer" data-name="{{ strtolower($c['name']) }}" data-category="{{ $c['category'] }}">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 rounded-full text-white flex items-center justify-center text-[10px] font-black shrink-0" style="background-color: {{ $c['color'] }}">{{ $c['initials'] }}</div>
                                <span class="text-[13px] font-bold text-slate-900">{{ $c['name'] }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-[13px] text-slate-600 font-medium">{{ $c['email'] }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 {{ $catClasses }} text-[11px] font-bold rounded-full">{{ $c['category'] }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 {{ $statusClasses }} text-[11px] font-bold rounded-full">{{ $c['status'] }}</span>
                        </td>
                        <td class="px-6 py-4 text-[13px] text-slate-500 font-medium">{{ $c['date'] }}</td>
                        <td class="px-6 py-4 text-right flex justify-end">
                            <x-ui.action-icon type="view" onclick="openManageModal('{{ $c['name'] }}')">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            </x-ui.action-icon>
                        </td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>

        {{-- View More & Pagination --}}
        <div class="px-6 py-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <a href="{{ route('admin.customer-directory') }}" class="ajax-link text-[13px] font-bold text-primary-800 hover:underline flex items-center gap-1 cursor-pointer">
                    View More Records
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                <p class="text-[12px] text-slate-400 mt-0.5">Showing 1 to 8 of 248 customers</p>
            </div>
            <div class="flex items-center gap-2">
                <button class="h-9 w-9 flex items-center justify-center rounded border border-slate-200 text-slate-400 bg-white hover:bg-slate-50 transition cursor-pointer">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <div class="flex font-semibold text-[13px]">
                    <button class="h-9 w-9 flex items-center justify-center rounded bg-primary-600 text-white cursor-pointer">1</button>
                    <button class="h-9 w-9 flex items-center justify-center rounded bg-white text-slate-600 hover:bg-slate-50 transition border border-transparent hover:border-slate-200 cursor-pointer">2</button>
                    <button class="h-9 w-9 flex items-center justify-center rounded bg-white text-slate-600 hover:bg-slate-50 transition border border-transparent hover:border-slate-200 cursor-pointer">3</button>
                    <span class="h-9 w-9 flex items-center justify-center text-slate-400">…</span>
                    <button class="h-9 w-9 flex items-center justify-center rounded bg-white text-slate-600 hover:bg-slate-50 transition border border-transparent hover:border-slate-200 cursor-pointer">31</button>
                </div>
                <button class="h-9 w-9 flex items-center justify-center rounded border border-slate-200 text-slate-400 bg-white hover:bg-slate-50 transition cursor-pointer">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ─── Manage Customer Modal ─── --}}
    <div id="manage-customer-modal" class="fixed inset-0 z-[1000] flex items-center justify-center hidden" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm cursor-pointer" onclick="closeManageModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 p-6 animate-fade-in">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-extrabold text-slate-900" id="modal-customer-name">Manage Customer</h3>
                <button onclick="closeManageModal()" class="h-8 w-8 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-700 transition flex items-center justify-center cursor-pointer">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="space-y-4">
                {{-- Account status --}}
                <div>
                    <label class="block text-[12px] font-bold text-slate-600 mb-1.5">Account Status</label>
                    <div class="flex gap-2">
                        <button onclick="setStatus(this,'Active')" class="status-btn flex-1 py-2 rounded-lg text-[12px] font-bold border border-primary-200 bg-primary-50 text-primary-600 hover:bg-emerald-100 transition cursor-pointer">Active</button>
                        <button onclick="setStatus(this,'Suspended')" class="status-btn flex-1 py-2 rounded-lg text-[12px] font-bold border border-slate-200 bg-slate-50 text-slate-600 hover:bg-slate-100 transition cursor-pointer">Suspended</button>
                        <button onclick="setStatus(this,'Inactive')" class="status-btn flex-1 py-2 rounded-lg text-[12px] font-bold border border-slate-200 bg-slate-50 text-slate-600 hover:bg-slate-100 transition cursor-pointer">Inactive</button>
                    </div>
                </div>
                {{-- Category --}}
                <div>
                    <label class="block text-[12px] font-bold text-slate-600 mb-1.5">Customer Category</label>
                    <select class="w-full bg-slate-50 border border-slate-200 text-[13px] font-semibold text-slate-700 rounded-lg px-3 py-2.5 outline-none focus:border-primary-600 focus:ring-1 focus:ring-primary-600 transition">
                        <option>B2B</option>
                        <option>Retail</option>
                        <option>Guest</option>
                    </select>
                </div>
                {{-- Notes --}}
                <div>
                    <label class="block text-[12px] font-bold text-slate-600 mb-1.5">Admin Notes</label>
                    <textarea rows="3" placeholder="Add internal notes about this customer..." class="w-full bg-slate-50 border border-slate-200 text-[13px] text-slate-700 rounded-lg px-3 py-2.5 outline-none focus:border-primary-600 focus:ring-1 focus:ring-primary-600 transition resize-none font-medium placeholder:text-slate-400"></textarea>
                </div>
            </div>
            <div class="mt-5 flex gap-3 justify-end">
                <button onclick="closeManageModal()" class="px-5 py-2.5 rounded-xl text-sm font-bold border border-slate-200 text-slate-600 hover:bg-slate-50 transition cursor-pointer">Cancel</button>
                <button class="px-5 py-2.5 rounded-xl text-sm font-bold bg-primary-600 hover:bg-primary-700 text-white shadow-md shadow-primary-600/20 transition cursor-pointer">Save Changes</button>
            </div>
        </div>
    </div>


<style>
    @keyframes fade-in { from { opacity:0; transform:scale(0.96) translateY(8px); } to { opacity:1; transform:scale(1) translateY(0); } }
    .animate-fade-in { animation: fade-in 0.2s ease-out forwards; }
</style>

@push('scripts')
<script>
(function () {
    // ─── Pending Verification Handlers ───
    window.handleVerification = function(btn, action, id) {
        const card = document.querySelector(`[data-pending-id="${id}"]`);
        if (!card) return;
        const label = action === 'approve' ? 'Approved' : 'Rejected';
        const colorClass = action === 'approve' ? 'text-primary-600' : 'text-rose-500';
        card.innerHTML = `<div class="flex items-center gap-2 py-1 px-1"><svg class="h-4 w-4 ${colorClass}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="${action === 'approve' ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12'}"/></svg><span class="text-[12px] font-bold ${colorClass}">${label}</span></div>`;
        setTimeout(() => card.style.height = '0', 1500);
        setTimeout(() => card.remove(), 1800);
        updatePendingCount();
    };

    function updatePendingCount() {
        const remaining = document.querySelectorAll('[data-pending-id]').length - 1;
        const heading = document.querySelector('#pending-verifications-section .font-bold.text-amber-800');
        if (heading) heading.textContent = `Pending User Verifications (${Math.max(0, remaining)})`;
        if (remaining <= 0) {
            setTimeout(() => {
                const section = document.getElementById('pending-verifications-section');
                if (section) { section.style.opacity = '0'; section.style.transition = 'opacity 0.4s'; setTimeout(() => section.remove(), 400); }
            }, 2000);
        }
    }

    // ─── Category Filter ───
    document.getElementById('category-filter')?.addEventListener('change', function() {
        const val = this.value;
        document.querySelectorAll('.customer-row').forEach(row => {
            const match = !val || row.dataset.category === val;
            row.style.display = match ? '' : 'none';
        });
    });

    // ─── Global search filter ───
    document.getElementById('global-customer-search')?.addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.customer-row').forEach(row => {
            row.style.display = row.dataset.name.includes(q) ? '' : 'none';
        });
    });

    // ─── Manage Modal ───
    window.openManageModal = function(name) {
        document.getElementById('modal-customer-name').textContent = name;
        document.getElementById('manage-customer-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };
    window.closeManageModal = function() {
        document.getElementById('manage-customer-modal').classList.add('hidden');
        document.body.style.overflow = '';
    };


    // ─── Status buttons inside modal ───
    window.setStatus = function(btn, status) {
        document.querySelectorAll('.status-btn').forEach(b => {
            b.className = 'status-btn flex-1 py-2 rounded-lg text-[12px] font-bold border border-slate-200 bg-slate-50 text-slate-600 hover:bg-slate-100 transition';
        });
        const active = { 'Active': 'border-primary-200 bg-primary-50 text-primary-600 hover:bg-emerald-100', 'Suspended': 'border-rose-200 bg-rose-50 text-rose-600 hover:bg-rose-100', 'Inactive': 'border-slate-300 bg-slate-100 text-slate-700 hover:bg-slate-200' };
        btn.className = `status-btn flex-1 py-2 rounded-lg text-[12px] font-bold border transition ${active[status] || ''}`;
    };

    // ─── Update Parameters button ───
    window.updateParameters = function() {
        const btn = document.getElementById('btn-update-params');
        if (!btn) return;
        const original = btn.textContent;
        btn.textContent = 'Saving…';
        btn.disabled = true;
        setTimeout(() => { btn.textContent = '✓ Saved'; }, 800);
        setTimeout(() => { btn.textContent = original; btn.disabled = false; }, 2200);
    };

    // ─── Change Selection ───
    document.getElementById('change-selection-btn')?.addEventListener('click', () => {
        window.AdminToast?.show('Select a customer from the directory below', 'info');
    });

    // ─── Escape key to close modals ───
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') { closeManageModal(); closeAddCustomerModal(); }
    });
})();
</script>
@endpush

@endsection

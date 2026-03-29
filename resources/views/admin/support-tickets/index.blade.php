@extends('admin.layout')

@section('title', 'Support Tickets - Biogenix Admin')

@section('admin_content')

    {{-- Breadcrumb --}}
    <nav class="flex text-[13px] text-slate-500 font-medium mb-2">
        <a href="{{ route('admin.dashboard') }}" class="ajax-link hover:text-slate-900 transition cursor-pointer">Admin</a>
        <span class="mx-2 text-slate-300">/</span>
        <span class="text-slate-900 font-semibold">Support Tickets</span>
    </nav>

    {{-- Page Header --}}
    <div class="mb-5 flex flex-col md:flex-row md:items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Support Ticket System</h1>
            <p class="text-sm text-slate-500 mt-1 max-w-lg">Centralized hub for managing, resolving, and tracking customer inquiries across Biogenix services.</p>
        </div>
    </div>

    {{-- ─── Active Ticket Inbox ─── --}}
    <div class="bg-white rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 overflow-hidden mb-5">

        {{-- Toolbar --}}
        <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <h2 class="text-sm font-extrabold text-slate-900">Active Ticket Inbox</h2>
            <div class="relative w-full sm:w-80">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input id="ticket-search" type="text" placeholder="Search by Ticket ID, Customer, or Subject..." class="w-full bg-slate-50 border border-slate-200 text-sm rounded-xl pl-9 pr-4 py-2.5 focus:bg-white focus:border-primary-600 focus:ring-1 focus:ring-primary-600 transition outline-none text-slate-800 placeholder:text-slate-400 font-medium">
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="px-6 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Ticket ID</th>
                        <th class="px-6 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Customer Name</th>
                        <th class="px-6 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Subject</th>
                        <th class="px-6 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Priority</th>
                        <th class="px-6 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100" id="ticket-table-body">

                    @php
                    $tickets = [
                        ['id' => 'TK-8821', 'customer' => 'Alice Henderson', 'subject' => 'Subscription Billing Issue',   'priority' => 'High',   'priority_color' => 'bg-rose-50 text-rose-700 border border-rose-200/60',    'status' => 'Open',        'status_color' => 'bg-amber-50 text-amber-700 border border-amber-200/60'],
                        ['id' => 'TK-8819', 'customer' => 'Marcus Thorne',   'subject' => 'Lab Report Access',           'priority' => 'Medium', 'priority_color' => 'bg-primary-50 text-primary-700 border border-primary-200/60',  'status' => 'In Progress', 'status_color' => 'bg-blue-50 text-blue-700 border border-blue-200/60'],
                        ['id' => 'TK-8795', 'customer' => 'Elena Rossi',     'subject' => 'Technical Bug: Login Loop',   'priority' => 'Critical','priority_color' => 'bg-red-50 text-red-700 border border-red-200/60',        'status' => 'Resolved',    'status_color' => 'bg-emerald-50 text-emerald-700 border border-emerald-200/60'],
                        ['id' => 'TK-8790', 'customer' => 'James Wilson',    'subject' => 'Feature Request: API Access', 'priority' => null,     'priority_color' => '',                             'status' => 'Open',        'status_color' => 'bg-amber-50 text-amber-700 border border-amber-200/60'],
                        ['id' => 'TK-8787', 'customer' => 'Priya Anand',     'subject' => 'Order Not Received',          'priority' => 'High',   'priority_color' => 'bg-rose-50 text-rose-700 border border-rose-200/60',    'status' => 'Open',        'status_color' => 'bg-amber-50 text-amber-700 border border-amber-200/60'],
                        ['id' => 'TK-8771', 'customer' => 'Karl Messner',    'subject' => 'Refund Processing Delay',     'priority' => 'Low',    'priority_color' => 'bg-slate-50 text-slate-600 border border-slate-200/60',  'status' => 'Closed',      'status_color' => 'bg-slate-50 text-slate-500 border border-slate-200/60'],
                    ];
                    @endphp

                    @foreach($tickets as $t)
                    <tr class="hover:bg-slate-50/60 transition-colors cursor-pointer ticket-row" onclick="selectTicket('{{ $t['id'] }}')" data-ticket="{{ $t['id'] }}" data-name="{{ strtolower($t['customer']) }}" data-subject="{{ strtolower($t['subject']) }}">
                        <td class="px-6 py-3.5">
                            <span class="text-[13px] font-extrabold text-primary-800">#{{ $t['id'] }}</span>
                        </td>
                        <td class="px-6 py-3.5">
                            <span class="text-[13px] font-semibold text-slate-800">{{ $t['customer'] }}</span>
                        </td>
                        <td class="px-6 py-3.5">
                            <span class="text-[13px] text-slate-600 font-medium">{{ $t['subject'] }}</span>
                        </td>
                        <td class="px-6 py-3.5">
                            @if($t['priority'])
                            <div class="flex items-center gap-1">
                                <span class="inline-flex items-center px-2.5 py-1 {{ $t['priority_color'] }} text-[11px] font-bold rounded-full">{{ $t['priority'] }}</span>
                                <button class="h-5 w-5 flex items-center justify-center rounded text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                            </div>
                            @else
                            <button class="text-[11px] font-bold text-slate-400 border border-dashed border-slate-300 rounded-full px-2.5 py-1 hover:border-slate-400 hover:text-slate-600 transition cursor-pointer">Add Priority</button>
                            @endif
                        </td>
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-1">
                                <span class="inline-flex items-center px-2.5 py-1 {{ $t['status_color'] }} text-[11px] font-bold rounded-full">{{ $t['status'] }}</span>
                                <button class="h-5 w-5 flex items-center justify-center rounded text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>

    {{-- ─── Ticket Detail + Right Panel ─── --}}
    <div class="grid grid-cols-1 xl:grid-cols-[1fr_18rem] gap-5">

        {{-- Left: Ticket Detail --}}
        <div id="ticket-detail-panel" class="bg-white rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 overflow-hidden flex flex-col">

            {{-- Ticket Header --}}
            <div class="px-6 py-4 border-b border-slate-100">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <h2 class="text-[15px] font-extrabold text-slate-900" id="detail-title">#TK-8821: Subscription Billing Issue</h2>
                            <button class="h-6 w-6 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition cursor-pointer">
                                <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                            </button>
                        </div>
                        <p class="text-[12px] text-slate-500" id="detail-meta">Opened by Alice Henderson &bull; 3 hours ago</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="inline-flex items-center px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-200/60 text-[11px] font-bold rounded-full" id="detail-status-badge">Status: Open</span>
                        <span class="inline-flex items-center px-2.5 py-1 bg-rose-50 text-rose-700 border border-rose-200/60 text-[11px] font-bold rounded-full" id="detail-priority-badge">Priority: High</span>
                    </div>
                </div>

                {{-- Meta Row --}}
                <div class="mt-4 grid grid-cols-3 gap-4 bg-slate-50 rounded-xl px-4 py-3">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Assigned To</p>
                        <div class="flex items-center gap-1.5">
                            <div class="h-5 w-5 rounded-full bg-primary-600 text-white flex items-center justify-center text-[8px] font-black">SM</div>
                            <span class="text-[12px] font-semibold text-slate-800" id="detail-assignee">Sarah Miller</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Category</p>
                        <span class="text-[12px] font-semibold text-slate-800" id="detail-category">Billing</span>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Last Update</p>
                        <span class="text-[12px] font-semibold text-slate-800">14 mins ago</span>
                    </div>
                </div>
            </div>

            {{-- Conversation Thread --}}
            <div class="px-6 py-4 flex-1 overflow-y-auto" id="conversation-thread">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Conversation Thread</span>
                </div>
                <div class="space-y-4">

                    {{-- Customer message --}}
                    <div class="flex items-start gap-3">
                        <div class="h-7 w-7 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center text-[9px] font-black shrink-0 mt-0.5">AH</div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-baseline gap-2 mb-1">
                                <span class="text-[12px] font-bold text-slate-900">Alice Henderson</span>
                                <span class="text-[10px] text-slate-400">09:12 AM</span>
                            </div>
                            <div class="bg-slate-50 border border-slate-100 rounded-xl rounded-tl-sm px-4 py-3 text-[12px] text-slate-700 leading-relaxed">
                                "Hi, I noticed an extra charge of $49.99 on my account this morning that doesn't seem to match my current subscription tier. Can you please check why this happened?"
                            </div>
                        </div>
                    </div>

                    {{-- Time separator --}}
                    <div class="flex items-center gap-3">
                        <div class="flex-1 h-px bg-slate-100"></div>
                        <span class="text-[10px] text-slate-400 font-medium whitespace-nowrap">11:30 AM &bull; Sarah Miller (Support)</span>
                        <div class="flex-1 h-px bg-slate-100"></div>
                    </div>

                    {{-- Support reply --}}
                    <div class="flex items-start gap-3 flex-row-reverse">
                        <div class="h-7 w-7 rounded-full bg-primary-600 text-white flex items-center justify-center text-[9px] font-black shrink-0 mt-0.5">SM</div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-baseline gap-2 mb-1 justify-end">
                                <span class="text-[10px] text-slate-400">11:30 AM</span>
                                <span class="text-[12px] font-bold text-slate-900">Sarah Miller (Support)</span>
                            </div>
                            <div class="bg-primary-600 rounded-xl rounded-tr-sm px-4 py-3 text-[12px] text-white leading-relaxed">
                                "Hello Alice, I'm currently looking into your billing discrepancy. It appears there might have been a double-authorization during the renewal process. Could you please confirm if you received two receipt emails?"
                            </div>
                        </div>
                    </div>

                    {{-- Customer followup --}}
                    <div class="flex items-start gap-3">
                        <div class="h-7 w-7 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center text-[9px] font-black shrink-0 mt-0.5">AH</div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-baseline gap-2 mb-1">
                                <span class="text-[12px] font-bold text-slate-900">Alice Henderson</span>
                                <span class="text-[10px] text-slate-400">11:45 AM</span>
                            </div>
                            <div class="bg-slate-50 border border-slate-100 rounded-xl rounded-tl-sm px-4 py-3 text-[12px] text-slate-700 leading-relaxed">
                                "Yes, I did receive two emails. One says 'Order Confirmed' and the other says 'Subscription Updated'."
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Ticket History --}}
            <div class="px-6 py-4 border-t border-slate-100">
                <div class="flex items-center gap-2 mb-3">
                    <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Ticket History</span>
                </div>
                <div class="space-y-2.5">
                    <div class="flex items-start gap-3">
                        <div class="mt-1 h-5 w-5 rounded-full bg-slate-100 flex items-center justify-center shrink-0">
                            <svg class="h-3 w-3 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        </div>
                        <div>
                            <p class="text-[12px] font-semibold text-slate-900"><span class="font-bold">Ticket Created</span> by Alice Henderson</p>
                            <p class="text-[11px] text-slate-400">08:17 AM</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="mt-1 h-5 w-5 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                            <svg class="h-3 w-3 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                            <p class="text-[12px] font-semibold text-slate-900"><span class="font-bold">Ticket Assigned</span> to Sarah Miller</p>
                            <p class="text-[11px] text-slate-400">08:46 AM</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="mt-1 h-5 w-5 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                            <svg class="h-3 w-3 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </div>
                        <div>
                            <p class="text-[12px] font-semibold text-slate-900"><span class="font-bold">Status Changed</span> to Open</p>
                            <p class="text-[11px] text-slate-400">09:20 AM</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Reply Box --}}
            <div class="px-6 py-4 border-t border-slate-100">
                <textarea id="reply-input" rows="3" placeholder="Type your response here..." class="w-full bg-slate-50 border border-slate-200 text-[13px] text-slate-800 rounded-xl px-4 py-3 outline-none focus:border-primary-600 focus:ring-1 focus:ring-primary-600 transition resize-none placeholder:text-slate-400 font-medium"></textarea>
                <div class="mt-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <button class="h-8 w-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition cursor-pointer" title="Attach file">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                        </button>
                        <button class="h-8 w-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition cursor-pointer" title="Emoji">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </button>
                    </div>
                    <button id="btn-send-message" onclick="sendMessage()" class="bg-primary-600 hover:bg-primary-700 transition text-white px-5 py-2 rounded-lg text-[13px] font-bold shadow-sm shadow-primary-600/20 cursor-pointer">Send Message</button>
                </div>
            </div>
        </div>

        {{-- Right: Support Categories + Configuration --}}
        <div class="flex flex-col gap-5">

            {{-- Support Categories --}}
            <div class="bg-white rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 p-5">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Support Categories</span>
                    <button class="text-[12px] font-bold text-primary-800 hover:underline cursor-pointer">Manage</button>
                </div>
                <div id="categories-list">
                    {{-- Simple rows with separator lines, matching the original design --}}
                    <div class="flex items-center justify-between py-2.5 border-b border-slate-100">
                        <span class="text-[13px] font-semibold text-slate-800">Technical Support</span>
                        <span class="text-[11px] font-bold bg-primary-600 text-white rounded-full px-2.5 py-0.5 min-w-[28px] text-center">128</span>
                    </div>
                    <div class="flex items-center justify-between py-2.5 border-b border-slate-100">
                        <span class="text-[13px] font-semibold text-slate-800">Billing &amp; Subscription</span>
                        <span class="text-[11px] font-bold bg-slate-100 text-slate-600 rounded-full px-2.5 py-0.5 min-w-[28px] text-center">45</span>
                    </div>
                    <div class="flex items-center justify-between py-2.5">
                        <span class="text-[13px] font-semibold text-slate-800">Lab Results</span>
                        <span class="text-[11px] font-bold bg-slate-100 text-slate-600 rounded-full px-2.5 py-0.5 min-w-[28px] text-center">12</span>
                    </div>
                </div>
                <button id="btn-add-category" onclick="addCategory()" class="mt-3 w-full py-2 border border-dashed border-slate-200 rounded-xl text-[12px] font-semibold text-slate-400 hover:border-primary-600 hover:text-primary-800 transition flex items-center justify-center gap-1.5 cursor-pointer">
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    + Add New Category
                </button>
            </div>

            {{-- Configuration --}}
            <div class="bg-white rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 p-5">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Configuration</span>
                </div>
                {{-- Only 1 configuration item as per the screenshot --}}
                <a href="{{ route('admin.ui-fields-modification') }}" class="ajax-link w-full text-left rounded-xl border border-slate-100 bg-slate-50 hover:border-primary-600/30 hover:bg-slate-100 transition px-4 py-3 group flex flex-col cursor-pointer">
                    <div class="flex items-center justify-between">
                        <span class="text-[13px] font-bold text-slate-900 group-hover:text-primary-800 transition">UI Fields Modification</span>
                        <svg class="h-3.5 w-3.5 text-slate-400 group-hover:text-primary-800 transition shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">Customize ticket forms, custom fields, and data validation rules.</p>
                </a>
            </div>

        </div>
    </div>


<style>
    @keyframes ticket-fade { from { opacity: 0; transform: scale(0.96) translateY(8px); } to { opacity: 1; transform: scale(1) translateY(0); } }
    .animate-ticket-fade { animation: ticket-fade 0.2s ease-out forwards; }
    .ticket-row.active-ticket { background: #f0f3f8; }
    .ticket-row.active-ticket td:first-child { border-left: 3px solid #1A4D2E; }
</style>

<script>
(function() {

    // ─── Ticket inbox search ───
    document.getElementById('ticket-search')?.addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.ticket-row').forEach(row => {
            const match = row.dataset.name.includes(q) || row.dataset.subject.includes(q) || row.dataset.ticket.toLowerCase().includes(q);
            row.style.display = match ? '' : 'none';
        });
    });

    // ─── Select ticket to view detail ───
    window.selectTicket = function(id) {
        document.querySelectorAll('.ticket-row').forEach(r => r.classList.remove('active-ticket'));
        const row = document.querySelector(`[data-ticket="${id}"]`);
        if (row) row.classList.add('active-ticket');
        // In a real app you'd fetch the ticket details via AJAX here
    };

    // ─── Mark first row as active on load ───
    const firstRow = document.querySelector('.ticket-row');
    if (firstRow) firstRow.classList.add('active-ticket');

    // ─── Send Message ───
    window.sendMessage = function() {
        const input = document.getElementById('reply-input');
        const msg = input?.value?.trim();
        if (!msg) return;
        const thread = document.getElementById('conversation-thread').querySelector('.space-y-4');
        const now = new Date();
        const timeStr = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        const bubble = document.createElement('div');
        bubble.className = 'flex items-start gap-3 flex-row-reverse';
        bubble.innerHTML = `
            <div class="h-7 w-7 rounded-full bg-primary-600 text-white flex items-center justify-center text-[9px] font-black shrink-0 mt-0.5">SA</div>
            <div class="flex-1 min-w-0">
                <div class="flex items-baseline gap-2 mb-1 justify-end">
                    <span class="text-[10px] text-slate-400">${timeStr}</span>
                    <span class="text-[12px] font-bold text-slate-900">Super Admin</span>
                </div>
                <div class="bg-primary-600 rounded-xl rounded-tr-sm px-4 py-3 text-[12px] text-white leading-relaxed">${msg.replace(/</g,'&lt;').replace(/>/g,'&gt;')}</div>
            </div>`;
        thread.appendChild(bubble);
        input.value = '';
        bubble.scrollIntoView({ behavior: 'smooth', block: 'end' });
    };

    // ─── Add Category ───
    window.addCategory = function() {
        const name = prompt('New category name:');
        if (!name?.trim()) return;
        const list = document.getElementById('categories-list');
        const div = document.createElement('div');
        div.className = 'flex items-center justify-between py-2 border-b border-slate-50';
        div.innerHTML = `<span class="text-[13px] font-semibold text-slate-800">${name.trim()}</span><span class="text-[11px] font-bold bg-slate-100 text-slate-600 rounded-full px-2 py-0.5">0</span>`;
        list.appendChild(div);
    };

    // ─── Escape key ───
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeNewTicketModal(); });

    // ─── Enter to send message (Ctrl+Enter) ───
    document.getElementById('reply-input')?.addEventListener('keydown', e => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') { e.preventDefault(); sendMessage(); }
    });
})();
</script>

@endsection

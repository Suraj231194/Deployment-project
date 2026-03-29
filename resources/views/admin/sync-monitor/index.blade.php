@extends('admin.layout')

@section('title', 'Server Sync Monitor - Biogenix Admin')

@section('admin_content')
@php
    $syncNodes = [
        [
            'title' => 'Inventory Sync',
            'status' => 'Active',
            'rate' => '99.8%',
            'last_sync' => '2m ago',
            'icon_bg' => 'bg-primary-50',
            'icon_color' => 'text-primary-600',
            'status_bg' => 'bg-primary-50',
            'status_text' => 'text-primary-600',
            'rate_text' => 'text-primary-600',
            'card_class' => '',
            'icon_path' => 'M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V9m-5-4a2 2 0 002 2m-2-2a2 2 0 00-2 2m2-2h2a2 2 0 012 2v2m-7 7 2 2 4-4',
        ],
        [
            'title' => 'Orders Sync',
            'status' => 'Active',
            'rate' => '100%',
            'last_sync' => '45s ago',
            'icon_bg' => 'bg-primary-50',
            'icon_color' => 'text-primary-600',
            'status_bg' => 'bg-primary-50',
            'status_text' => 'text-primary-600',
            'rate_text' => 'text-primary-600',
            'card_class' => '',
            'icon_path' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0',
        ],
        [
            'title' => 'Pricing Sync',
            'status' => 'Warning',
            'rate' => '94.2%',
            'last_sync' => '12m ago',
            'icon_bg' => 'bg-secondary-50',
            'icon_color' => 'text-secondary-700',
            'status_bg' => 'bg-secondary-50',
            'status_text' => 'text-secondary-700',
            'rate_text' => 'text-secondary-700',
            'card_class' => '',
            'icon_path' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0',
        ],
        [
            'title' => 'Logistics Sync',
            'status' => 'Failed',
            'rate' => '0% (Timed out)',
            'last_sync' => 'Manual Req.',
            'icon_bg' => 'bg-rose-50',
            'icon_color' => 'text-rose-500',
            'status_bg' => 'bg-rose-50',
            'status_text' => 'text-rose-600',
            'rate_text' => 'text-rose-600',
            'card_class' => 'border-l-4 border-l-rose-500',
            'icon_path' => 'M8 7h8m0 0-3-3m3 3-3 3m-2 7H8m0 0 3 3m-3-3 3-3',
        ],
    ];

    $syncLogs = [
        [
            'date' => 'Oct 24,',
            'time' => '14:32:01',
            'source' => 'Logistics / Node-7',
            'tags' => ['504 Gateway', 'Timeout'],
        ],
        [
            'date' => 'Oct 24,',
            'time' => '14:15:22',
            'source' => 'Pricing / DB-Global',
            'tags' => ['Data Validation', 'Error'],
        ],
        [
            'date' => 'Oct 24,',
            'time' => '13:58:45',
            'source' => 'Inventory / Warehouse-02',
            'tags' => ['Handshake Failed'],
        ],
    ];

    $activityFeed = [
        [
            'time' => 'Just Now',
            'title' => 'Orders Sync Completed',
            'body' => '428 records synchronized successfully from North America Cluster.',
            'dot' => 'bg-primary-600',
            'ring' => 'ring-emerald-50',
            'title_color' => 'text-slate-900',
        ],
        [
            'time' => '4 mins ago',
            'title' => 'Manual Trigger Initialized',
            'body' => "Admin 'S. Thompson' triggered full inventory refresh.",
            'dot' => 'bg-primary-600',
            'ring' => 'ring-indigo-50',
            'title_color' => 'text-slate-900',
        ],
        [
            'time' => '14 mins ago',
            'title' => 'Connection Interrupted',
            'body' => 'Logistics node lost heartbeat. Retrying in 5 seconds...',
            'dot' => 'bg-rose-500',
            'ring' => 'ring-rose-50',
            'title_color' => 'text-rose-600',
        ],
        [
            'time' => '1 hour ago',
            'title' => 'Security Protocol Update',
            'body' => 'All sync tokens rotated successfully for the next cycle.',
            'dot' => 'bg-primary-600',
            'ring' => 'ring-emerald-50',
            'title_color' => 'text-slate-900',
        ],
    ];
@endphp

<div class="space-y-6">
    <div class="mb-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Server Sync Monitor</h2>
            <p class="mt-1 text-sm text-slate-500">Real-time status and control of Biogenix server nodes.</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <button type="button" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 px-5 py-2.5 text-[13px] font-bold text-white shadow-[0_2px_10px_-3px_rgba(6,81,237,0.18)] transition hover:bg-primary-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m14.836 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Trigger Full System Sync
            </button>

            <button type="button" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-[13px] font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:text-primary-800">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h10m-10 6h7" />
                </svg>
                Sync Module
            </button>
        </div>
    </div>

    <div>
        <div class="mb-4 flex items-center gap-2">
            <div class="flex h-6 w-6 items-center justify-center rounded bg-primary-600">
                <svg class="h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 19h16M7 16V9m5 7V5m5 11v-4" />
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-900">Node Status Overview</h3>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:gap-6 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($syncNodes as $node)
                <article class="bg-white rounded-2xl p-5 lg:p-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 flex min-h-[200px] flex-col justify-between {{ $node['card_class'] }}">
                    <div class="flex items-start justify-between">
                        <div class="h-10 w-10 flex items-center justify-center rounded-xl {{ $node['icon_bg'] }} {{ $node['icon_color'] }}">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $node['icon_path'] }}"></path>
                            </svg>
                        </div>

                        <span class="inline-flex items-center px-2.5 py-1 rounded-md {{ $node['status_bg'] }} {{ $node['status_text'] }} text-[10px] font-bold uppercase tracking-wider">
                            {{ $node['status'] }}
                        </span>
                    </div>

                    <div class="mt-6">
                        <h4 class="text-lg font-bold text-slate-900">{{ $node['title'] }}</h4>

                        <div class="mt-5 space-y-3">
                            <div class="flex items-center justify-between gap-4 text-[12px] lg:text-[13px]">
                                <span class="font-semibold text-slate-500">Success Rate</span>
                                <span class="font-bold {{ $node['rate_text'] }}">{{ $node['rate'] }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-4 text-[12px] lg:text-[13px]">
                                <span class="font-semibold text-slate-500">Last Sync</span>
                                <span class="font-bold {{ $node['status_text'] === 'text-rose-600' ? 'text-rose-600 italic' : 'text-slate-900' }}">{{ $node['last_sync'] }}</span>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-8">
            <div class="mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <div class="flex h-5 w-5 items-center justify-center rounded-full bg-rose-500 shadow-sm shadow-rose-500/30">
                        <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Failed Sync Logs</h3>
                </div>

                <div class="relative w-full sm:w-80">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" placeholder="Search error codes..." class="w-full bg-white border border-slate-200 shadow-sm text-sm rounded-xl pl-9 pr-4 py-2.5 focus:border-primary-600 focus:ring-1 focus:ring-primary-600 transition outline-none text-slate-700 placeholder:text-slate-400">
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="px-5 lg:px-7 py-3 lg:py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">Timestamp</th>
                                <th class="px-5 lg:px-7 py-3 lg:py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">Source/Module</th>
                                <th class="px-5 lg:px-7 py-3 lg:py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">Error Type</th>
                                <th class="px-5 lg:px-7 py-3 lg:py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100/80 text-[12px] lg:text-[13px] font-semibold text-slate-900">
                            @foreach ($syncLogs as $log)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-5 lg:px-7 py-4 lg:py-5 align-top">
                                        <p>{{ $log['date'] }}</p>
                                        <p>{{ $log['time'] }}</p>
                                    </td>
                                    <td class="px-5 lg:px-7 py-4 lg:py-5 align-top text-slate-700 font-medium">
                                        <div class="max-w-[12rem] whitespace-normal leading-6">{{ $log['source'] }}</div>
                                    </td>
                                    <td class="px-5 lg:px-7 py-4 lg:py-5 align-top">
                                        <div class="flex max-w-[12rem] flex-wrap gap-2">
                                            @foreach ($log['tags'] as $tag)
                                                <span class="inline-flex items-center px-2 py-1 bg-rose-100/50 text-rose-600 text-[10px] lg:text-[11px] font-bold rounded-md">
                                                    {{ $tag }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-5 lg:px-7 py-4 lg:py-5 align-top text-right">
                                        <button type="button" class="inline-flex items-center gap-1.5 text-[12px] lg:text-[13px] font-bold text-primary-800 hover:underline underline-offset-2">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m14.836 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                            Retry
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-5 lg:px-7 py-4 border-t border-slate-100 bg-slate-50/50 text-center">
                    <a href="#" class="text-[12px] lg:text-[13px] font-bold text-primary-800 hover:underline underline-offset-2">View Full Archive</a>
                </div>
            </div>
        </div>

        <div class="lg:col-span-4">
            <div class="mb-4 flex items-center gap-2">
                <div class="flex h-5 w-5 items-center justify-center text-primary-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-900">Live Activity Feed</h3>
            </div>

            <div class="bg-white rounded-2xl p-6 lg:p-7 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 relative h-full">
                <div class="absolute left-8 top-8 bottom-8 w-px bg-slate-100 hidden sm:block"></div>

                <div class="space-y-6">
                    @foreach ($activityFeed as $item)
                        <article class="relative flex gap-4">
                            <div class="relative hidden sm:block w-5">
                                <span class="absolute left-0.5 top-1 h-3 w-3 rounded-full {{ $item['dot'] }} ring-4 {{ $item['ring'] }}"></span>
                            </div>

                            <div class="sm:pl-4">
                                <p class="text-[11px] font-semibold text-slate-400">{{ $item['time'] }}</p>
                                <h4 class="mt-1 text-[13px] font-bold {{ $item['title_color'] }}">{{ $item['title'] }}</h4>
                                <p class="mt-1 text-[12px] leading-6 text-slate-500">{{ $item['body'] }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

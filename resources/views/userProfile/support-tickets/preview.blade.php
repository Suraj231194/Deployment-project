@extends('customer.layout')

@php
    $portal = auth()->user()?->user_type ?? request('user_type', request('portal', 'b2c'));
    $portal = $portal === 'b2b' ? 'b2b' : 'b2c';
    $statusColors = [
        'open' => 'text-primary-600',
        'in_progress' => 'text-secondary-700',
        'closed' => 'text-primary-600',
        'awaiting_response' => 'text-primary-600',
    ];
    $statusDots = [
        'open' => 'bg-primary-500',
        'in_progress' => 'bg-secondary-600',
        'closed' => 'bg-primary-600',
        'awaiting_response' => 'bg-primary-600',
    ];
    $categoryBg = [
        'security' => 'bg-slate-100 text-slate-700',
        'logistics' => 'bg-slate-100 text-slate-700',
        'technical' => 'bg-slate-100 text-slate-700',
        'returns' => 'bg-slate-100 text-slate-700',
        'billing' => 'bg-slate-100 text-slate-700',
        'product_inquiry' => 'bg-slate-100 text-slate-700',
    ];
    $previewTickets = isset($tickets) && $tickets->count()
        ? collect($tickets->items())->map(function ($ticket) use ($statusColors, $statusDots, $categoryBg) {
            return [
                'id' => $ticket->ticket_number,
                'date' => \Illuminate\Support\Carbon::parse($ticket->created_at)->format('M d, Y'),
                'subject' => \Illuminate\Support\Str::limit($ticket->description, 40),
                'category' => ucfirst(str_replace('_', ' ', $ticket->category)),
                'catClass' => $categoryBg[$ticket->category] ?? 'bg-slate-100 text-slate-700',
                'status' => ucfirst(str_replace('_', ' ', $ticket->status)),
                'statusColor' => $statusColors[$ticket->status] ?? 'text-slate-600',
                'dotColor' => $statusDots[$ticket->status] ?? 'bg-slate-400',
                'href' => route('support-tickets.show', $ticket->id),
            ];
        })->values()
        : collect([
            ['id' => '#TK-8955', 'date' => 'Nov 02, 2023', 'subject' => 'Account Access Issue', 'category' => 'Security', 'catClass' => 'bg-slate-100 text-slate-700', 'status' => 'Open', 'statusColor' => 'text-primary-600', 'dotColor' => 'bg-primary-500', 'href' => null],
            ['id' => '#TK-8902', 'date' => 'Oct 24, 2023', 'subject' => 'Shipping Delay Inquiry', 'category' => 'Logistics', 'catClass' => 'bg-slate-100 text-slate-700', 'status' => 'In Progress', 'statusColor' => 'text-secondary-700', 'dotColor' => 'bg-secondary-600', 'href' => null],
            ['id' => '#TK-8841', 'date' => 'Oct 12, 2023', 'subject' => 'Product Storage Guidelines', 'category' => 'Technical', 'catClass' => 'bg-slate-100 text-slate-700', 'status' => 'Resolved', 'statusColor' => 'text-primary-600', 'dotColor' => 'bg-primary-600', 'href' => null],
            ['id' => '#TK-8712', 'date' => 'Sep 28, 2023', 'subject' => 'Damaged Packaging', 'category' => 'Returns', 'catClass' => 'bg-slate-100 text-slate-700', 'status' => 'Resolved', 'statusColor' => 'text-primary-600', 'dotColor' => 'bg-primary-600', 'href' => null],
        ]);
@endphp

@section('title', 'Support Tickets')
@section('customer_active', 'support')
@section('customer_minimal', 'minimal')

@section('customer_content')
    <x-account.workspace
        :portal="$portal"
        active="support"
        title="Support Tickets"
        description="Manage and track your inquiries and technical support requests."
    >
        {{-- Header action --}}
        <div class="flex justify-end -mt-2">
            <button type="button" class="inline-flex h-10 shrink-0 items-center gap-2 rounded-xl bg-primary-600 px-5 text-[13px] font-bold text-white shadow-sm transition hover:bg-primary-700 cursor-pointer" onclick="if (typeof toggleSupportForm === 'function') { toggleSupportForm(); }">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Raise Ticket
            </button>
        </div>

        {{-- Stat cards --}}
        <div class="grid gap-4 sm:grid-cols-3">
            @php
                $stats = [
                    ['label' => 'Open Tickets', 'count' => isset($tickets) ? collect($tickets->items())->where('status', 'open')->count() : 2, 'color' => 'primary', 'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                    ['label' => 'In Progress', 'count' => isset($tickets) ? collect($tickets->items())->where('status', 'in_progress')->count() : 1, 'color' => 'amber', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                    ['label' => 'Resolved', 'count' => isset($tickets) ? collect($tickets->items())->where('status', 'closed')->count() : 14, 'color' => 'emerald', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ];
            @endphp
            @foreach ($stats as $stat)
                <div class="flex items-center gap-4 rounded-2xl border border-slate-100 bg-white p-5 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)]">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-{{ $stat['color'] }}-50">
                        <svg class="h-5 w-5 text-{{ $stat['color'] }}-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $stat['icon'] }}"/></svg>
                    </div>
                    <div>
                        <p class="text-[12px] lg:text-[13px] font-semibold text-slate-500">{{ $stat['label'] }}</p>
                        <p class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $stat['count'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Ticket History --}}
        <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)]">
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-base font-bold text-slate-900">Ticket History</h2>
                <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row sm:items-center">
                    <div class="relative w-full sm:w-56">
                        <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                        <input type="text" placeholder="Search tickets..." id="ticketSearchInput" class="h-10 w-full rounded-xl border border-slate-200 bg-white pl-10 pr-4 text-[13px] text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-primary-600 focus:ring-1 focus:ring-primary-600" oninput="filterTickets(this.value)">
                    </div>
                    <button type="button" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 text-slate-400 transition hover:bg-slate-50">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    </button>
                </div>
            </div>

            {{-- Mobile cards --}}
            <div class="space-y-3 md:hidden">
                @foreach ($previewTickets as $ticket)
                    <article class="ticket-item rounded-2xl border border-slate-100 bg-slate-50/50 p-4">
                        <div class="flex flex-col gap-4">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <p class="text-[13px] font-bold text-primary-800">{{ $ticket['id'] }}</p>
                                    <p class="mt-1 text-[13px] font-medium text-slate-900">{{ $ticket['subject'] }}</p>
                                </div>
                                <span class="inline-flex items-center gap-1.5 {{ $ticket['statusColor'] }}">
                                    <span class="h-2 w-2 rounded-full {{ $ticket['dotColor'] }}"></span>
                                    <span class="text-[12px] font-semibold">{{ $ticket['status'] }}</span>
                                </span>
                            </div>
                            <div class="grid gap-3 sm:grid-cols-2">
                                <div>
                                    <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Date</p>
                                    <p class="mt-1 text-[13px] text-slate-700">{{ $ticket['date'] }}</p>
                                </div>
                                <div>
                                    <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Category</p>
                                    <span class="mt-1 inline-flex rounded-md {{ $ticket['catClass'] }} px-2.5 py-1 text-[11px] font-semibold">{{ $ticket['category'] }}</span>
                                </div>
                            </div>
                            @if ($ticket['href'])
                                <a href="{{ $ticket['href'] }}" class="inline-flex h-9 w-full items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-[13px] font-bold text-slate-700 no-underline transition hover:bg-slate-50">
                                    View Details
                                </a>
                            @else
                                <span class="inline-flex h-9 w-full items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-[13px] font-bold text-primary-800">
                                    View Details
                                </span>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Table --}}
            <div class="hidden overflow-x-auto md:block">
                <table class="w-full min-w-[42rem] text-left border-collapse whitespace-nowrap" id="ticketsTable">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="px-5 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">Ticket ID</th>
                            <th class="px-5 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">Date</th>
                            <th class="px-5 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">Subject</th>
                            <th class="px-5 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">Category</th>
                            <th class="px-5 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">Status</th>
                            <th class="px-5 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100/80 text-[12px] lg:text-[13px] font-semibold text-slate-900" id="ticketsBody">
                        @foreach ($previewTickets as $ticket)
                            <tr class="ticket-item hover:bg-slate-50/50 transition-colors">
                                <td class="px-5 py-4">
                                    @if ($ticket['href'])
                                        <a href="{{ $ticket['href'] }}" class="font-bold text-primary-800 no-underline hover:underline">{{ $ticket['id'] }}</a>
                                    @else
                                        <span class="font-bold text-primary-800">{{ $ticket['id'] }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-slate-600 font-medium">{{ $ticket['date'] }}</td>
                                <td class="px-5 py-4 font-medium text-slate-900">{{ $ticket['subject'] }}</td>
                                <td class="px-5 py-4">
                                    <span class="rounded-md {{ $ticket['catClass'] }} px-2.5 py-1 text-[11px] font-semibold">{{ $ticket['category'] }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="flex items-center gap-1.5 {{ $ticket['statusColor'] }}">
                                        <span class="h-2 w-2 rounded-full {{ $ticket['dotColor'] }}"></span>
                                        {{ $ticket['status'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    @if ($ticket['href'])
                                        <a href="{{ $ticket['href'] }}" class="text-[13px] font-bold text-primary-800 no-underline hover:underline">View Details</a>
                                    @else
                                        <span class="text-[13px] font-bold text-primary-800">View Details</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-6 flex flex-col items-center justify-between gap-3 border-t border-slate-100 pt-4 sm:flex-row">
                <p class="text-[13px] text-slate-500">
                    @if (isset($tickets))
                        Showing {{ $tickets->firstItem() ?? 0 }} to {{ $tickets->lastItem() ?? 0 }} of {{ $tickets->total() }} tickets
                    @else
                        Showing 1 to 4 of 17 tickets
                    @endif
                </p>
                <div class="flex items-center gap-2">
                    @if (isset($tickets) && $tickets->hasPages())
                        {{ $tickets->links() }}
                    @else
                        <button type="button" class="h-9 rounded-lg border border-slate-200 bg-white px-4 text-[13px] font-semibold text-slate-600 transition hover:bg-slate-50">Previous</button>
                        <button type="button" class="h-9 rounded-lg border border-slate-200 bg-white px-4 text-[13px] font-semibold text-slate-600 transition hover:bg-slate-50">Next</button>
                    @endif
                </div>
            </div>
        </div>

    </x-account.workspace>

@push('scripts')
<script>
function filterTickets(query) {
    var rows = document.querySelectorAll('.ticket-item');
    var lowerQuery = query.toLowerCase();
    rows.forEach(function(row) {
        var text = row.textContent.toLowerCase();
        row.classList.toggle('hidden', lowerQuery !== '' && text.indexOf(lowerQuery) === -1);
    });
}
</script>
@endpush
@endsection

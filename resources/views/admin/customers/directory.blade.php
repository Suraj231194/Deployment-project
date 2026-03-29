@extends('admin.layout')

@section('title', 'Customer Directory - Biogenix Admin')

@section('admin_content')

    {{-- Breadcrumb --}}
    <nav class="flex items-center text-[13px] text-slate-500 font-medium mb-3">
        <a href="{{ route('admin.customers') }}" class="ajax-link mr-3 h-8 w-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition cursor-pointer" title="Go Back">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <a href="{{ route('admin.customers') }}" class="ajax-link hover:text-slate-900 transition cursor-pointer">Customer Management</a>
        <span class="mx-2 text-slate-300">›</span>
        <span class="text-primary-800 font-bold">Customer Directory</span>
    </nav>

    {{-- Page Header --}}
    <div class="mb-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Customer Directory</h1>
            <p class="text-[13.5px] text-slate-500 mt-1">Manage and monitor all B2B and Retail customer records from a central location.</p>
        </div>
        <div class="flex items-center gap-3 shrink-0">
            <button class="flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-bold border border-slate-200 text-slate-700 bg-white hover:bg-slate-50 transition cursor-pointer">
                <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4H4"/></svg>
                Export CSV
            </button>
            <button id="btn-add-customer" onclick="openAddCustomerModal()" class="bg-primary-600 hover:bg-primary-700 transition text-white px-5 py-2.5 rounded-lg text-sm font-bold shadow-md shadow-primary-600/20 flex items-center gap-2 cursor-pointer">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add Customer
            </button>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white rounded-t-2xl border border-slate-100 border-b-0 p-4 flex flex-col sm:flex-row gap-4 items-center justify-between">
        <div class="relative w-full sm:max-w-md">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input id="customer-search" type="text" placeholder="Search by name, email, or company..." class="w-full bg-slate-50 border border-slate-200 text-[13px] rounded-xl pl-10 pr-4 py-2.5 focus:bg-white focus:border-primary-600 focus:ring-1 focus:ring-primary-600 transition outline-none text-slate-800 placeholder:text-slate-400 font-medium">
        </div>
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <select class="appearance-none bg-slate-50 border border-slate-200 text-[13px] font-semibold text-slate-700 rounded-xl px-4 py-2.5 pr-8 outline-none hover:border-slate-300 transition focus:border-primary-600 focus:ring-1 focus:ring-primary-600">
                <option>All Categories</option>
                <option>B2B</option>
                <option>Retail</option>
            </select>
            <select class="appearance-none bg-slate-50 border border-slate-200 text-[13px] font-semibold text-slate-700 rounded-xl px-4 py-2.5 pr-8 outline-none hover:border-slate-300 transition focus:border-primary-600 focus:ring-1 focus:ring-primary-600">
                <option>All Statuses</option>
                <option>Active</option>
                <option>Inactive</option>
            </select>
        </div>
    </div>

    {{-- Main Directory Table --}}
    <div class="bg-white rounded-b-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 overflow-hidden relative">
        <div class="overflow-x-auto">
            <table class="w-full text-left whitespace-nowrap">
                <thead>
                    <tr class="border-b border-slate-100 bg-white">
                        <th class="px-6 py-4 text-[10px] font-extrabold text-slate-400 text-left uppercase tracking-widest w-[25%]">Name</th>
                        <th class="px-6 py-4 text-[10px] font-extrabold text-slate-400 text-left uppercase tracking-widest w-[25%]">Email</th>
                        <th class="px-6 py-4 text-[10px] font-extrabold text-slate-400 text-center uppercase tracking-widest">Category</th>
                        <th class="px-6 py-4 text-[10px] font-extrabold text-slate-400 text-left uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-[10px] font-extrabold text-slate-400 text-left uppercase tracking-widest">Date Joined</th>
                        <th class="px-6 py-4 text-[10px] font-extrabold text-slate-400 text-right uppercase tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white" id="directory-tbody">
                    @php
                    // Array containing 30 static records precisely mimicking the screenshot design
                    $thirtyCustomers = [
                        ['name'=>'Acme Biotech Corp', 'email'=>'ops@acmebiotech.com', 'cat'=>'B2B', 'status'=>'Active', 'date'=>'Jan 12, 2024'],
                        ['name'=>'David Miller', 'email'=>'david.miller@gmail.com', 'cat'=>'Retail', 'status'=>'Active', 'date'=>'Jan 14, 2024'],
                        ['name'=>'Starlight Laboratories', 'email'=>'billing@starlight.io', 'cat'=>'B2B', 'status'=>'Inactive', 'date'=>'Jan 18, 2024'],
                        ['name'=>'Sarah Jenkins', 'email'=>'s.jenkins@outlook.com', 'cat'=>'Retail', 'status'=>'Active', 'date'=>'Feb 02, 2024'],
                        ['name'=>'Nexus Pharmacies Ltd', 'email'=>'supply@nexus.co.uk', 'cat'=>'B2B', 'status'=>'Active', 'date'=>'Feb 05, 2024'],
                        ['name'=>'HealthGrid Solutions', 'email'=>'contact@healthgrid.com', 'cat'=>'B2B', 'status'=>'Active', 'date'=>'Feb 10, 2024'],
                        ['name'=>'Marcus Aurelius', 'email'=>'m.aurelius@rome.com', 'cat'=>'Retail', 'status'=>'Inactive', 'date'=>'Feb 12, 2024'],
                        ['name'=>'BioGen Distrib', 'email'=>'logistics@biogen.com', 'cat'=>'B2B', 'status'=>'Active', 'date'=>'Feb 15, 2024'],
                        ['name'=>'Emma Watson', 'email'=>'emma.w@gmail.com', 'cat'=>'Retail', 'status'=>'Active', 'date'=>'Feb 18, 2024'],
                        ['name'=>'LifeCare Labs', 'email'=>'admin@lifecare.org', 'cat'=>'B2B', 'status'=>'Active', 'date'=>'Feb 20, 2024'],
                        ['name'=>'Julian Frost', 'email'=>'jfrost@me.com', 'cat'=>'Retail', 'status'=>'Active', 'date'=>'Feb 22, 2024'],
                        ['name'=>'OmniMed Group', 'email'=>'info@omnimed.net', 'cat'=>'B2B', 'status'=>'Inactive', 'date'=>'Feb 25, 2024'],
                        ['name'=>'Olivia Brown', 'email'=>'olivia.b@icloud.com', 'cat'=>'Retail', 'status'=>'Active', 'date'=>'Feb 28, 2024'],
                        ['name'=>'SilverCrest Care', 'email'=>'orders@silvercrest.com', 'cat'=>'B2B', 'status'=>'Active', 'date'=>'Mar 01, 2024'],
                        ['name'=>'Nathan Drake', 'email'=>'n.drake@uncharted.com', 'cat'=>'Retail', 'status'=>'Active', 'date'=>'Mar 05, 2024'],
                        ['name'=>'Vanguard Clinics', 'email'=>'v.clinics@vanguard.org', 'cat'=>'B2B', 'status'=>'Inactive', 'date'=>'Mar 08, 2024'],
                        ['name'=>'Isabella Swan', 'email'=>'isabella.s@yahoo.com', 'cat'=>'Retail', 'status'=>'Active', 'date'=>'Mar 11, 2024'],
                        ['name'=>'Alpha Medical', 'email'=>'procurement@alphamed.co', 'cat'=>'B2B', 'status'=>'Active', 'date'=>'Mar 14, 2024'],
                        ['name'=>'Derek Hale', 'email'=>'derek.h@gmail.com', 'cat'=>'Retail', 'status'=>'Inactive', 'date'=>'Mar 16, 2024'],
                        ['name'=>'Global Diagnostics', 'email'=>'support@globaldiag.com', 'cat'=>'B2B', 'status'=>'Active', 'date'=>'Mar 19, 2024'],
                        ['name'=>'Emily Blunt', 'email'=>'emily.b@work.org', 'cat'=>'Retail', 'status'=>'Active', 'date'=>'Mar 21, 2024'],
                        ['name'=>'Peak BioTech', 'email'=>'sales@peakbiotech.com', 'cat'=>'B2B', 'status'=>'Active', 'date'=>'Mar 24, 2024'],
                        ['name'=>'Liam Neeson', 'email'=>'liam.n@movie.net', 'cat'=>'Retail', 'status'=>'Active', 'date'=>'Mar 25, 2024'],
                        ['name'=>'Horizon Labs', 'email'=>'billing@horizonlabs.io', 'cat'=>'B2B', 'status'=>'Inactive', 'date'=>'Mar 28, 2024'],
                        ['name'=>'Sophia Loren', 'email'=>'sophia.l@italy.com', 'cat'=>'Retail', 'status'=>'Active', 'date'=>'Apr 02, 2024'],
                        ['name'=>'CureAll Hospitals', 'email'=>'admin@cureall.org', 'cat'=>'B2B', 'status'=>'Active', 'date'=>'Apr 05, 2024'],
                        ['name'=>'Jackson Maine', 'email'=>'j.maine@music.com', 'cat'=>'Retail', 'status'=>'Active', 'date'=>'Apr 08, 2024'],
                        ['name'=>'Elite Sciences Group', 'email'=>'ops@elitescience.net', 'cat'=>'B2B', 'status'=>'Inactive', 'date'=>'Apr 11, 2024'],
                        ['name'=>'Ava Max', 'email'=>'ava.max@pop.com', 'cat'=>'Retail', 'status'=>'Active', 'date'=>'Apr 14, 2024'],
                        ['name'=>'Future Health Inc.', 'email'=>'contact@futurehealth.co', 'cat'=>'B2B', 'status'=>'Active', 'date'=>'Apr 18, 2024'],
                    ];
                    @endphp

                    @foreach($thirtyCustomers as $c)
                    @php
                        $catBadge = $c['cat'] === 'B2B' 
                            ? 'bg-slate-100 text-slate-600' 
                            : 'bg-slate-100 text-slate-600';
                            
                        $dotColor = $c['status'] === 'Active' ? 'bg-primary-600' : 'bg-slate-300';
                        $statusTextColor = $c['status'] === 'Active' ? 'text-primary-600' : 'text-slate-500';
                    @endphp
                    <tr class="hover:bg-slate-50/50 transition-colors directory-row cursor-pointer" data-name="{{ strtolower($c['name']) }}" data-email="{{ strtolower($c['email']) }}">
                        <td class="px-6 py-4">
                            <span class="text-[13px] font-semibold text-slate-900">{{ $c['name'] }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-[13px] text-slate-500">{{ $c['email'] }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 {{ $catBadge }} text-[10px] font-bold rounded-full tracking-wide">{{ $c['cat'] }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-1.5">
                                <div class="h-2 w-2 rounded-full {{ $dotColor }}"></div>
                                <span class="text-[12px] font-semibold {{ $statusTextColor }}">{{ $c['status'] }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-[13px] text-slate-500">{{ $c['date'] }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button onclick="openManageModal('{{ $c['name'] }}')" class="text-[12px] font-bold text-primary-800 hover:text-slate-600 hover:underline transition pr-2 cursor-pointer">Manage</button>
                        </td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>

        {{-- Pagination Footer --}}
        <div class="px-6 py-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4 bg-slate-50/50">
            <div>
                <p class="text-[12.5px] text-slate-500">Showing <span class="font-bold text-slate-700">1</span> to <span class="font-bold text-slate-700">30</span> of <span class="font-bold text-slate-700">248</span> records</p>
            </div>
            <div class="flex items-center gap-1.5">
                <button class="h-8 w-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-400 bg-white hover:bg-slate-50 hover:text-slate-600 transition cursor-pointer">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <div class="flex items-center font-bold text-[12.5px] gap-1.5 mx-1">
                    <button class="h-8 w-8 flex items-center justify-center rounded-lg bg-primary-600 text-white shadow-sm cursor-pointer">1</button>
                    <button class="h-8 w-8 flex items-center justify-center rounded-lg bg-white text-slate-600 hover:bg-slate-50 hover:text-primary-800 border border-slate-200 transition cursor-pointer">2</button>
                    <button class="h-8 w-8 flex items-center justify-center rounded-lg bg-white text-slate-600 hover:bg-slate-50 hover:text-primary-800 border border-slate-200 transition cursor-pointer">3</button>
                    <span class="h-8 w-8 flex items-center justify-center text-slate-400 font-normal tracking-widest">...</span>
                    <button class="h-8 w-8 flex items-center justify-center rounded-lg bg-white text-slate-600 hover:bg-slate-50 hover:text-primary-800 border border-slate-200 transition cursor-pointer">9</button>
                </div>
                <button class="h-8 w-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-600 bg-white hover:bg-slate-50 hover:text-primary-800 transition cursor-pointer">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>
    </div>


    {{-- Add/Manage Modals (reused from customers page) --}}
    <div id="add-customer-modal" class="fixed inset-0 z-[1000] flex items-center justify-center hidden" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm cursor-pointer" onclick="closeAddCustomerModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 p-6 animate-fade-in">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-extrabold text-slate-900">Add New Customer</h3>
                <button onclick="closeAddCustomerModal()" class="h-8 w-8 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-700 transition flex items-center justify-center cursor-pointer">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="space-y-3">
                <div>
                    <label class="block text-[12px] font-bold text-slate-600 mb-1">Full Name / Company Name</label>
                    <input type="text" placeholder="e.g. Nova Scientific Group" class="w-full bg-slate-50 border border-slate-200 text-[13px] font-semibold text-slate-700 rounded-lg px-3 py-2.5 outline-none focus:border-primary-600 focus:ring-1 focus:ring-primary-600 transition placeholder:text-slate-400">
                </div>
                <div>
                    <label class="block text-[12px] font-bold text-slate-600 mb-1">Email Address</label>
                    <input type="email" placeholder="contact@company.com" class="w-full bg-slate-50 border border-slate-200 text-[13px] font-semibold text-slate-700 rounded-lg px-3 py-2.5 outline-none focus:border-primary-600 focus:ring-1 focus:ring-primary-600 transition placeholder:text-slate-400">
                </div>
                <div>
                    <label class="block text-[12px] font-bold text-slate-600 mb-1">Category</label>
                    <select class="w-full bg-slate-50 border border-slate-200 text-[13px] font-semibold text-slate-700 rounded-lg px-3 py-2.5 outline-none focus:border-primary-600 focus:ring-1 focus:ring-primary-600 transition">
                        <option>B2B</option>
                        <option>Retail</option>
                        <option>Guest</option>
                    </select>
                </div>
            </div>
            <div class="mt-5 flex gap-3 justify-end">
                <button onclick="closeAddCustomerModal()" class="px-5 py-2.5 rounded-xl text-sm font-bold border border-slate-200 text-slate-600 hover:bg-slate-50 transition cursor-pointer">Cancel</button>
                <button class="px-5 py-2.5 rounded-xl text-sm font-bold bg-primary-600 hover:bg-primary-700 text-white shadow-md shadow-primary-600/20 transition cursor-pointer">Create Customer</button>
            </div>
        </div>
    </div>


<style>
    @keyframes fade-in { from { opacity:0; transform:scale(0.96) translateY(8px); } to { opacity:1; transform:scale(1) translateY(0); } }
    .animate-fade-in { animation: fade-in 0.2s ease-out forwards; }
</style>

<script>
(function() {
    // ─── Quick Search ───
    document.getElementById('customer-search')?.addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.directory-row').forEach(row => {
            const match = row.dataset.name.includes(q) || row.dataset.email.includes(q);
            row.style.display = match ? '' : 'none';
        });
    });

    // ─── Add Customer Modal ───
    window.openAddCustomerModal = function() {
        const modal = document.getElementById('add-customer-modal');
        if(modal) { modal.classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
    };
    window.closeAddCustomerModal = function() {
        const modal = document.getElementById('add-customer-modal');
        if(modal) { modal.classList.add('hidden'); document.body.style.overflow = ''; }
    };

    // ─── Manage Modal (stubbed) ───
    window.openManageModal = function(name) {
        alert("Managing " + name);
    };

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') { closeAddCustomerModal(); }
    });
})();
</script>

@endsection

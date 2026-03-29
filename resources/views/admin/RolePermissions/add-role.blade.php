@extends('admin.layout')

@section('title', 'Add System Role - Biogenix Admin')

@section('admin_content')
    @php
        $permissions = [
            ['code' => 'billing.invoice.create', 'description' => 'Generate customer invoices'],
            ['code' => 'lab.sample.approve', 'description' => 'Sign off on biological tests'],
            ['code' => 'user.profile.export', 'description' => 'Export CSV of user data'],
        ];

        $allPermissions = [
            ['name' => 'View Patient Records', 'desc' => 'Allows viewing basic demographic and medical history data.'],
            ['name' => 'Edit Lab Results', 'desc' => 'Ability to input and modify laboratory test values.'],
            ['name' => 'Manage Inventory', 'desc' => 'Full control over chemical and supply stock levels.'],
            ['name' => 'Approve Compliance Reports', 'desc' => 'Final sign-off authority for regulatory documentation.'],
            ['name' => 'User Management', 'desc' => 'Create, edit, and deactivate system user accounts.'],
            ['name' => 'Financial Auditing', 'desc' => 'Access to billing cycles and expenditure analytics.'],
            ['name' => 'Generate Invoices', 'desc' => 'Create and send customer invoices and proforma invoices.'],
            ['name' => 'Export Data', 'desc' => 'Export CSV or PDF reports of system data.'],
        ];
    @endphp

    <div class="space-y-6 text-slate-900">

        {{-- Back Arrow + Breadcrumb --}}
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('admin.role-permission') }}" class="ajax-link h-8 w-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-50 hover:border-slate-300 transition shrink-0 cursor-pointer" title="Back to Roles & Permissions">
                <svg class="h-4 w-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <nav class="flex items-center text-[13px] text-slate-500 font-medium">
            <a href="{{ route('admin.role-permission') }}" class="ajax-link hover:text-slate-900 transition cursor-pointer">Roles &amp; Permissions</a>
            <span class="mx-2 text-slate-300">/</span>
                <span class="text-slate-900 font-semibold cursor-pointer">Add New System Role</span>
            </nav>
        </div>

        {{-- Page Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Add New System Role</h1>
            <p class="text-sm text-slate-500 mt-1">Create a custom role template and define primary permission buckets.</p>
        </div>

        {{-- Role Details Card --}}
        <div class="rounded-[20px] border border-slate-200/80 bg-white shadow-[0_10px_28px_rgba(15,23,42,0.05)] p-6 sm:p-8">
            <div class="flex items-center gap-2.5 mb-6">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-primary-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
                <h2 class="text-[17px] font-extrabold text-slate-950">Role Details</h2>
            </div>

            <div class="space-y-5">
                <div>
                    <label for="roleName" class="block text-[13px] font-bold text-slate-700 mb-2">Role Name</label>
                    <input id="roleName" type="text" placeholder="e.g. Senior Lab Technician" class="h-12 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 text-[14px] text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-300 focus:bg-white focus:ring-4 focus:ring-slate-200/50">
                </div>
                <div>
                    <label for="roleDescription" class="block text-[13px] font-bold text-slate-700 mb-2">Description</label>
                    <textarea id="roleDescription" rows="4" placeholder="Briefly describe the responsibilities and scope of this role..." class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-[14px] text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-300 focus:bg-white focus:ring-4 focus:ring-slate-200/50 resize-none"></textarea>
                </div>
            </div>
        </div>

        {{-- Assign Permissions Card --}}
        <div class="rounded-[20px] border border-slate-200/80 bg-white shadow-[0_10px_28px_rgba(15,23,42,0.05)] p-6 sm:p-8">
            <div class="flex items-center justify-between gap-4 mb-6">
                <div class="flex items-center gap-2.5">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-600">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </span>
                    <h2 class="text-[17px] font-extrabold text-slate-950">Assign Permissions</h2>
                </div>

                <div class="relative w-52">
                    <div class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input id="permissionSearch" type="text" placeholder="Search permissions..." class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50 pl-10 pr-4 text-[13px] text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-300 focus:ring-4 focus:ring-slate-200/50">
                </div>
            </div>

            <div class="divide-y divide-slate-100" id="permissionsList">
                @foreach ($allPermissions as $perm)
                    <label class="permission-item flex items-start gap-4 py-4 cursor-pointer hover:bg-slate-50/50 transition rounded-lg px-2 -mx-2" data-name="{{ strtolower($perm['name']) }}">
                        <input type="checkbox" class="mt-1 h-[18px] w-[18px] rounded border-slate-300 text-slate-600 focus:ring-slate-200 shrink-0 cursor-pointer">
                        <div class="min-w-0">
                            <div class="text-[14px] font-bold text-slate-900">{{ $perm['name'] }}</div>
                            <div class="mt-0.5 text-[12px] text-slate-500">{{ $perm['desc'] }}</div>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center justify-end gap-3 pb-4">
            <a href="{{ route('admin.role-permission') }}" class="ajax-link px-6 py-3 rounded-xl text-[14px] font-bold border border-slate-200 text-slate-600 hover:bg-slate-50 transition cursor-pointer">Cancel</a>
            <button type="button" id="addRoleSaveBtn" class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-100 px-7 py-3 text-[14px] font-extrabold text-white shadow-[0_10px_20px_rgba(11,37,94,0.18)] transition hover:brightness-105 cursor-pointer">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                Create Role
            </button>
        </div>
    </div>

@push('scripts')
<script>
(function() {
    // Permission search filter
    var searchInput = document.getElementById('permissionSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            var q = this.value.toLowerCase();
            document.querySelectorAll('.permission-item').forEach(function(item) {
                var name = item.getAttribute('data-name') || '';
                item.style.display = name.includes(q) ? '' : 'none';
            });
        });
    }

    // Save role (placeholder)
    var saveBtn = document.getElementById('addRoleSaveBtn');
    if (saveBtn) {
        saveBtn.addEventListener('click', function() {
            var roleName = document.getElementById('roleName').value.trim();
            if (!roleName) {
                document.getElementById('roleName').focus();
                document.getElementById('roleName').classList.add('border-rose-400', 'ring-1', 'ring-rose-200');
                setTimeout(function() {
                    document.getElementById('roleName').classList.remove('border-rose-400', 'ring-1', 'ring-rose-200');
                }, 2000);
                return;
            }
            if (window.AdminToast) {
                window.AdminToast.show('Role "' + roleName + '" created successfully!', 'success');
            } else {
                alert('Role "' + roleName + '" created successfully!');
            }
            var tmpLink = document.createElement('a');
            tmpLink.href = "{{ route('admin.role-permission') }}";
            tmpLink.className = 'ajax-link hidden';
            document.body.appendChild(tmpLink);
            tmpLink.click();
            document.body.removeChild(tmpLink);
        });
    }
})();
</script>
@endpush
@endsection

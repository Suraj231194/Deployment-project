@extends('admin.layout')

@section('title', 'Assign Role to Department - Biogenix Admin')

@section('admin_content')
    <div class="space-y-6 text-slate-900">

        {{-- Back Arrow + Breadcrumb --}}
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('admin.role-permission') }}" class="ajax-link h-8 w-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-50 hover:border-slate-300 transition shrink-0 cursor-pointer" title="Back to Roles & Permissions">
                <svg class="h-4 w-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <nav class="flex items-center text-[13px] text-slate-500 font-medium">
            <a href="{{ route('admin.role-permission') }}" class="ajax-link hover:text-slate-900 transition cursor-pointer">Roles &amp; Permissions</a>
            <span class="mx-2 text-slate-300">/</span>
                <span class="text-slate-900 font-semibold cursor-pointer">Assign Role to Department</span>
            </nav>
        </div>

        {{-- Page Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Assign Role to Department</h1>
            <p class="text-sm text-slate-500 mt-1">Configure role mappings and permission defaults for entire departments.</p>
        </div>

        {{-- Form Card --}}
        <div class="rounded-[20px] border border-slate-200/80 bg-white shadow-[0_10px_28px_rgba(15,23,42,0.05)] p-6 sm:p-8">
            <div class="mb-6">
                <h2 class="text-[17px] font-extrabold text-slate-950">Create New Department & Assign Role</h2>
                <p class="text-[13px] text-slate-500 mt-1">Fill in the details below to initialize a new department with its respective system permissions.</p>
            </div>

            <div class="space-y-6">
                {{-- Department Name + Department ID --}}
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label for="deptName" class="block text-[13px] font-bold text-slate-700 mb-2">Department Name <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <input id="deptName" type="text" placeholder="e.g. Molecular Research" class="h-12 w-full rounded-xl border border-slate-200 bg-slate-50 pl-11 pr-4 text-[14px] text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-300 focus:bg-white focus:ring-4 focus:ring-slate-200/50">
                        </div>
                    </div>
                    <div>
                        <label for="deptId" class="block text-[13px] font-bold text-slate-700 mb-2">Department ID</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                </svg>
                            </div>
                            <input id="deptId" type="text" placeholder="e.g. DEPT-8802" class="h-12 w-full rounded-xl border border-slate-200 bg-slate-50 pl-11 pr-4 text-[14px] text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-300 focus:bg-white focus:ring-4 focus:ring-slate-200/50">
                        </div>
                        <p class="mt-1.5 text-[12px] text-slate-400">Unique identifier used for internal system routing.</p>
                    </div>
                </div>

                {{-- Assign System Role --}}
                <div class="pt-2">
                    <div class="flex items-center gap-2.5 mb-4">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-slate-100 text-slate-600">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </span>
                        <h3 class="text-[13px] font-extrabold uppercase tracking-[0.08em] text-slate-900">Assign System Role</h3>
                    </div>

                    <label for="deptRoleType" class="block text-[13px] font-bold text-slate-700 mb-2">System Role Type</label>
                    <div class="relative max-w-md">
                        <select id="deptRoleType" class="h-12 w-full appearance-none rounded-xl border border-slate-200 bg-slate-50 px-4 pr-10 text-[14px] text-slate-900 outline-none transition focus:border-slate-300 focus:bg-white focus:ring-4 focus:ring-slate-200/50 cursor-pointer">
                            <option value="" disabled selected>Select a role for this department...</option>
                            <option value="super_admin">Super Admin</option>
                            <option value="sales_manager">Sales Manager</option>
                            <option value="lab_technician">Lab Technician</option>
                            <option value="inventory_manager">Inventory Manager</option>
                            <option value="billing_admin">Billing Admin</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-slate-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                    <p class="mt-1.5 text-[12px] text-slate-400">This role will define the default permissions for all users joining this department.</p>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-slate-100">
                <a href="{{ route('admin.role-permission') }}" class="ajax-link px-6 py-3 rounded-xl text-[14px] font-bold border border-slate-200 text-slate-600 hover:bg-slate-50 transition cursor-pointer">Cancel</a>
                <button type="button" id="addDeptSaveBtn" class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-100 px-7 py-3 text-[14px] font-extrabold text-white shadow-[0_10px_20px_rgba(11,37,94,0.18)] transition hover:brightness-105 cursor-pointer">
                    Create & Assign
                </button>
            </div>
        </div>
    </div>

@push('scripts')
<script>
(function() {
    var saveDeptBtn = document.getElementById('addDeptSaveBtn');
    if (saveDeptBtn) {
        saveDeptBtn.addEventListener('click', function() {
            var deptName = document.getElementById('deptName').value.trim();
            if (!deptName) {
                document.getElementById('deptName').focus();
                document.getElementById('deptName').classList.add('border-rose-400', 'ring-1', 'ring-rose-200');
                setTimeout(function() {
                    document.getElementById('deptName').classList.remove('border-rose-400', 'ring-1', 'ring-rose-200');
                }, 2000);
                return;
            }
            if (window.AdminToast) {
                window.AdminToast.show('Department "' + deptName + '" created and role assigned!', 'success');
            } else {
                alert('Department "' + deptName + '" created and role assigned!');
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

@extends('admin.layout')

@section('title', 'Add Delegated Role - Biogenix Admin')

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
                <span class="text-slate-900 font-semibold cursor-pointer">Delegate Support Role</span>
            </nav>
        </div>

        {{-- Page Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Delegate Support Role</h1>
            <p class="text-sm text-slate-500 mt-1">Authorize internal tickets management and support proxy access.</p>
        </div>

        {{-- Form Card --}}
        <div class="rounded-[20px] border border-slate-200/80 bg-white shadow-[0_10px_28px_rgba(15,23,42,0.05)] p-6 sm:p-8">
            <div class="space-y-6 max-w-2xl">
                
                {{-- Select User --}}
                <div>
                    <label for="delegateUser" class="block text-[13px] font-bold text-slate-700 mb-2">Select User <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <input id="delegateUser" type="text" placeholder="Search for user by name or email..." class="h-12 w-full rounded-xl border border-slate-200 bg-slate-50 pl-11 pr-4 text-[14px] text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-300 focus:bg-white focus:ring-4 focus:ring-slate-200/50">
                    </div>
                </div>

                {{-- Role to Delegate --}}
                <div>
                    <label for="delegateRole" class="block text-[13px] font-bold text-slate-700 mb-2">Role to Delegate</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <select id="delegateRole" class="h-12 w-full appearance-none rounded-xl border border-slate-200 bg-slate-50 pl-11 pr-10 text-[14px] text-slate-900 outline-none transition focus:border-slate-300 focus:bg-white focus:ring-4 focus:ring-slate-200/50 cursor-pointer">
                            <option value="" disabled selected>Select a role...</option>
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
                </div>

                {{-- Expiry Date --}}
                <div>
                    <label for="delegateExpiry" class="block text-[13px] font-bold text-slate-700 mb-2">Expiry Date</label>
                    <div class="relative max-w-sm">
                        <div class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <input id="delegateExpiry" type="date" class="h-12 w-full rounded-xl border border-slate-200 bg-slate-50 pl-11 pr-4 text-[14px] text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-300 focus:bg-white focus:ring-4 focus:ring-slate-200/50 cursor-pointer">
                    </div>
                    <p class="mt-1.5 text-[12px] text-secondary-700 font-medium">Permissions will automatically revert at 00:00 on this date.</p>
                </div>

                {{-- Reason --}}
                <div>
                    <label for="delegateReason" class="block text-[13px] font-bold text-slate-700 mb-2">Reason for Delegation (Optional)</label>
                    <textarea id="delegateReason" rows="3" placeholder="Provide context for the audit log..." class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-[14px] text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-300 focus:bg-white focus:ring-4 focus:ring-slate-200/50 resize-none"></textarea>
                </div>

                {{-- Audit Notification --}}
                <div class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3">
                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <div class="text-[12.5px] font-bold text-slate-700">Audit Log Note</div>
                        <div class="text-[12px] text-slate-500 leading-relaxed">All actions performed by the user while this delegation is active will be explicitly logged with the "Delegated Role" signature for compliance tracking.</div>
                    </div>
                </div>

            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-slate-100">
                <a href="{{ route('admin.role-permission') }}" class="ajax-link px-6 py-3 rounded-xl text-[14px] font-bold border border-slate-200 text-slate-600 hover:bg-slate-50 transition cursor-pointer">Cancel</a>
                <button type="button" id="addDelegSaveBtn" class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-100 px-7 py-3 text-[14px] font-extrabold text-white shadow-[0_10px_20px_rgba(11,37,94,0.18)] transition hover:brightness-105 cursor-pointer">
                    Add Delegation
                </button>
            </div>
        </div>
    </div>

@push('scripts')
<script>
(function() {
    var saveDelegBtn = document.getElementById('addDelegSaveBtn');
    if (saveDelegBtn) {
        saveDelegBtn.addEventListener('click', function() {
            var userName = document.getElementById('delegateUser').value.trim();
            if (!userName) {
                document.getElementById('delegateUser').focus();
                document.getElementById('delegateUser').classList.add('border-rose-400', 'ring-1', 'ring-rose-200');
                setTimeout(function() {
                    document.getElementById('delegateUser').classList.remove('border-rose-400', 'ring-1', 'ring-rose-200');
                }, 2000);
                return;
            }
            if (window.AdminToast) {
                window.AdminToast.show('Delegation added for "' + userName + '" successfully!', 'success');
            } else {
                alert('Delegation added for "' + userName + '" successfully!');
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

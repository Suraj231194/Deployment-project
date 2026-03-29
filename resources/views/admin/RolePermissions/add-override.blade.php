@extends('admin.layout')

@section('title', 'Add User-Based Override - Biogenix Admin')

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
            <span class="text-slate-500">Overrides</span>
            <span class="mx-2 text-slate-300">/</span>
                <span class="text-slate-900 font-semibold cursor-pointer">Create Exception</span>
            </nav>
        </div>

        {{-- Page Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Create User Exception</h1>
            <p class="text-sm text-slate-500 mt-1">Configure direct permission overrides bypassing default role mappings.</p>
        </div>

        {{-- Override Form Card --}}
        <div class="rounded-[20px] border border-slate-200/80 bg-white shadow-[0_10px_28px_rgba(15,23,42,0.05)] p-6 sm:p-8">

            {{-- Step 1: Select User --}}
            <div class="mb-8">
                <div class="flex items-center gap-2.5 mb-5">
                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-slate-100 text-white text-[12px] font-extrabold">1</span>
                    <h2 class="text-[17px] font-extrabold text-slate-950">Select User</h2>
                </div>

                <label class="block text-[13px] font-bold text-slate-700 mb-2">Search User</label>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <select id="overrideUser" class="h-12 w-full appearance-none rounded-xl border border-slate-200 bg-slate-50 pl-11 pr-10 text-[14px] text-slate-900 outline-none transition focus:border-slate-300 focus:bg-white focus:ring-4 focus:ring-slate-200/50 cursor-pointer">
                            <option value="" disabled selected>Choose a user...</option>
                            <option value="james_wilson">James Wilson</option>
                            <option value="elena_rodriguez">Elena Rodriguez</option>
                            <option value="marcus_thorne">Marcus Thorne</option>
                            <option value="sarah_chen">Sarah Chen</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-slate-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 rounded-xl border border-slate-100 bg-slate-50/50 px-4 py-3">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 text-[13px] font-extrabold text-indigo-700 shrink-0">JW</span>
                        <div>
                            <div class="text-[13px] font-bold text-slate-700">Current Role</div>
                            <div class="text-[12px] text-slate-500">Analyst • Department: R&D</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 2: Configure Override --}}
            <div>
                <div class="flex items-center gap-2.5 mb-5">
                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-slate-100 text-white text-[12px] font-extrabold">2</span>
                    <h2 class="text-[17px] font-extrabold text-slate-950">Configure Override</h2>
                </div>

                <label class="block text-[13px] font-bold text-slate-700 mb-2">Find Permission</label>
                <div class="relative mb-4">
                    <div class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                    <input id="overridePermSearch" type="text" placeholder="Search by name (e.g. data.export, user.delete)..." class="h-12 w-full rounded-xl border border-slate-200 bg-slate-50 pl-11 pr-4 text-[14px] text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-300 focus:bg-white focus:ring-4 focus:ring-slate-200/50">
                </div>

                {{-- Permission radio list --}}
                <div class="divide-y divide-slate-100 mb-6" id="overridePermList">
                    <label class="override-perm-item flex items-center justify-between gap-4 py-4 cursor-pointer" data-name="lab.results.export_raw">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="overridePerm" value="lab.results.export_raw" class="h-[18px] w-[18px] border-slate-300 text-slate-600 focus:ring-slate-200 cursor-pointer" checked>
                            <div>
                                <div class="text-[14px] font-bold font-mono text-slate-900">lab.results.export_raw</div>
                                <div class="text-[12px] text-slate-500">Ability to export raw laboratory research datasets</div>
                            </div>
                        </div>
                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Selected</span>
                    </label>
                    <label class="override-perm-item flex items-center gap-3 py-4 cursor-pointer" data-name="lab.results.view_sensitive">
                        <input type="radio" name="overridePerm" value="lab.results.view_sensitive" class="h-[18px] w-[18px] border-slate-300 text-slate-600 focus:ring-slate-200 cursor-pointer">
                        <div>
                            <div class="text-[14px] font-bold font-mono text-slate-900">lab.results.view_sensitive</div>
                            <div class="text-[12px] text-slate-500">Access to view anonymized patient sensitivity markers</div>
                        </div>
                    </label>
                    <label class="override-perm-item flex items-center gap-3 py-4 cursor-pointer" data-name="billing.invoice.void">
                        <input type="radio" name="overridePerm" value="billing.invoice.void" class="h-[18px] w-[18px] border-slate-300 text-slate-600 focus:ring-slate-200 cursor-pointer">
                        <div>
                            <div class="text-[14px] font-bold font-mono text-slate-900">billing.invoice.void</div>
                            <div class="text-[12px] text-slate-500">Authority to void issued customer invoices</div>
                        </div>
                    </label>
                    <label class="override-perm-item flex items-center gap-3 py-4 cursor-pointer" data-name="user.profile.delete">
                        <input type="radio" name="overridePerm" value="user.profile.delete" class="h-[18px] w-[18px] border-slate-300 text-slate-600 focus:ring-slate-200 cursor-pointer">
                        <div>
                            <div class="text-[14px] font-bold font-mono text-slate-900">user.profile.delete</div>
                            <div class="text-[12px] text-slate-500">Permanently delete user accounts from the system</div>
                        </div>
                    </label>
                </div>

                {{-- Override Action --}}
                <div class="flex items-center justify-between gap-4 rounded-xl border border-slate-200 bg-slate-50/50 px-5 py-4">
                    <div>
                        <div class="text-[14px] font-bold text-slate-900">Override Action</div>
                        <div class="text-[12px] text-slate-500">Decide whether to explicitly allow or strictly deny this permission.</div>
                    </div>
                    <div class="flex items-center gap-1 rounded-lg border border-slate-200 bg-white p-1">
                        <button type="button" id="overrideAllowBtn" class="px-4 py-1.5 rounded-md text-[12px] font-extrabold uppercase tracking-widest bg-slate-100 text-white transition cursor-pointer">Allow</button>
                        <button type="button" id="overrideDenyBtn" class="px-4 py-1.5 rounded-md text-[12px] font-extrabold uppercase tracking-widest text-slate-500 hover:bg-slate-50 transition cursor-pointer">Deny</button>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-slate-100">
                <a href="{{ route('admin.role-permission') }}" class="ajax-link px-6 py-3 rounded-xl text-[14px] font-bold border border-slate-200 text-slate-600 hover:bg-slate-50 transition cursor-pointer">Cancel</a>
                <button type="button" id="addOverrideSaveBtn" class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-100 px-7 py-3 text-[14px] font-extrabold text-white shadow-[0_10px_20px_rgba(11,37,94,0.18)] transition hover:brightness-105 cursor-pointer">
                    Save Override
                </button>
            </div>
        </div>
    </div>

@push('scripts')
<script>
(function() {
    // Override permission search filter
    var overrideSearchInput = document.getElementById('overridePermSearch');
    if (overrideSearchInput) {
        overrideSearchInput.addEventListener('input', function() {
            var q = this.value.toLowerCase();
            document.querySelectorAll('.override-perm-item').forEach(function(item) {
                var name = item.getAttribute('data-name') || '';
                item.style.display = name.includes(q) ? '' : 'none';
            });
        });
    }

    // Override allow/deny toggle
    var allowBtn = document.getElementById('overrideAllowBtn');
    var denyBtn = document.getElementById('overrideDenyBtn');
    if (allowBtn && denyBtn) {
        allowBtn.addEventListener('click', function() {
            allowBtn.classList.add('bg-slate-100', 'text-white');
            allowBtn.classList.remove('text-slate-500', 'hover:bg-slate-50');
            denyBtn.classList.remove('bg-slate-100', 'text-white');
            denyBtn.classList.add('text-slate-500', 'hover:bg-slate-50');
        });
        denyBtn.addEventListener('click', function() {
            denyBtn.classList.add('bg-slate-100', 'text-white');
            denyBtn.classList.remove('text-slate-500', 'hover:bg-slate-50');
            allowBtn.classList.remove('bg-slate-100', 'text-white');
            allowBtn.classList.add('text-slate-500', 'hover:bg-slate-50');
        });
    }

    // Save override (placeholder toast)
    var saveOverrideBtn = document.getElementById('addOverrideSaveBtn');
    if (saveOverrideBtn) {
        saveOverrideBtn.addEventListener('click', function() {
            var selectedPerm = document.querySelector('input[name="overridePerm"]:checked');
            if (!selectedPerm) {
                if (window.AdminToast) window.AdminToast.show('Please select a permission to override.', 'warning');
                return;
            }
            var permVal = selectedPerm.value;
            if (window.AdminToast) {
                window.AdminToast.show('Override for "' + permVal + '" saved successfully!', 'success');
            } else {
                alert('Override for "' + permVal + '" saved successfully!');
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

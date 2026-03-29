@extends('admin.layout')

@section('title', 'Add Permission - Biogenix Admin')

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
                <span class="text-slate-900 font-semibold cursor-pointer">Add Global Permission</span>
            </nav>
        </div>

        {{-- Page Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Add Global Permission</h1>
            <p class="text-sm text-slate-500 mt-1">Define actionable capabilities to be assigned across roles and teams.</p>
        </div>

        {{-- Form Card --}}
        <div class="rounded-[20px] border border-slate-200/80 bg-white shadow-[0_10px_28px_rgba(15,23,42,0.05)] p-6 sm:p-8">
            <div class="space-y-6 max-w-2xl">
                
                {{-- Permission Name --}}
                <div>
                    <label for="permissionName" class="block text-[13px] font-bold text-slate-700 mb-2">Permission Internal Name <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                            </svg>
                        </div>
                        <input id="permissionName" type="text" placeholder="e.g. results.export_raw" class="h-12 w-full rounded-xl border border-slate-200 bg-slate-50 pl-11 pr-4 text-[14px] text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-300 focus:bg-white focus:ring-4 focus:ring-slate-200/50 font-mono">
                    </div>
                    <p class="mt-1.5 text-[12px] text-slate-400">Use dot notation for module grouping. Must be unique.</p>
                </div>

                {{-- Module Association --}}
                <div>
                    <label for="permissionModule" class="block text-[13px] font-bold text-slate-700 mb-2">Module Association</label>
                    <div class="relative">
                        <select id="permissionModule" class="h-12 w-full appearance-none rounded-xl border border-slate-200 bg-slate-50 px-4 pr-10 text-[14px] text-slate-900 outline-none transition focus:border-slate-300 focus:bg-white focus:ring-4 focus:ring-slate-200/50 cursor-pointer">
                            <option value="" disabled selected>Select a target module...</option>
                            <option value="lab">Lab Management System (LMS)</option>
                            <option value="billing">Billing & Finance</option>
                            <option value="users">User Profile & Auth</option>
                            <option value="inventory">Inventory & Reagents</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-slate-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Detailed Description --}}
                <div>
                    <label for="permissionDesc" class="block text-[13px] font-bold text-slate-700 mb-2">Detailed Description</label>
                    <textarea id="permissionDesc" rows="3" placeholder="Explain what this permission allows a user to do..." class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-[14px] text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-300 focus:bg-white focus:ring-4 focus:ring-slate-200/50 resize-none"></textarea>
                </div>

                {{-- Tip --}}
                <div class="rounded-xl border border-indigo-100 bg-indigo-50/50 p-4">
                    <div class="flex gap-3">
                        <span class="mt-0.5 inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-indigo-700">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        <div>
                            <h4 class="text-[13px] font-extrabold text-indigo-900">Admin Tip</h4>
                            <p class="mt-1 text-[13px] text-indigo-700/80 leading-relaxed">After creating this permission, you will still need to map it to the respective roles in the "Permission Mapping" tab.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-slate-100">
                <a href="{{ route('admin.role-permission') }}" class="ajax-link px-6 py-3 rounded-xl text-[14px] font-bold border border-slate-200 text-slate-600 hover:bg-slate-50 transition cursor-pointer">Cancel</a>
                <button type="button" id="addPermSaveBtn" class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-100 px-7 py-3 text-[14px] font-extrabold text-white shadow-[0_10px_20px_rgba(11,37,94,0.18)] transition hover:brightness-105 cursor-pointer">
                    Save Permission
                </button>
            </div>
        </div>
    </div>

@push('scripts')
<script>
(function() {
    var savePermBtn = document.getElementById('addPermSaveBtn');
    if (savePermBtn) {
        savePermBtn.addEventListener('click', function() {
            var permName = document.getElementById('permissionName').value.trim();
            if (!permName) {
                document.getElementById('permissionName').focus();
                document.getElementById('permissionName').classList.add('border-rose-400', 'ring-1', 'ring-rose-200');
                setTimeout(function() {
                    document.getElementById('permissionName').classList.remove('border-rose-400', 'ring-1', 'ring-rose-200');
                }, 2000);
                return;
            }
            if (window.AdminToast) {
                window.AdminToast.show('Permission "' + permName + '" created successfully!', 'success');
            } else {
                alert('Permission "' + permName + '" created successfully!');
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

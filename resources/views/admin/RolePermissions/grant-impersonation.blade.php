@extends('admin.layout')

@section('title', 'Grant Impersonation Access - Biogenix')

@section('admin_content')
    <div class="mx-auto max-w-5xl">
        {{-- Back Arrow + Breadcrumb --}}
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('admin.role-permission') }}" class="ajax-link h-8 w-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-50 hover:border-slate-300 transition shrink-0 cursor-pointer" title="Back to Roles & Permissions">
                <svg class="h-4 w-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <nav class="flex items-center text-[13px] text-slate-500 font-medium">
            <a href="{{ route('admin.role-permission') }}" class="ajax-link hover:text-slate-900 transition cursor-pointer">Roles & Permissions</a>
            <span class="mx-2 text-slate-300">/</span>
                <span class="text-slate-900 font-semibold cursor-pointer">Grant Impersonation Access</span>
            </nav>
        </div>

        {{-- Page Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Grant Impersonation Access</h1>
            <p class="text-sm text-slate-500 mt-1 max-w-2xl">Configure impersonation permissions for administrative users.</p>
        </div>

        {{-- Form Card --}}
        <div class="rounded-[20px] border border-slate-200/80 bg-white shadow-[0_10px_28px_rgba(15,23,42,0.05)] p-6 sm:p-8">
            <div class="mb-6 border-b border-slate-100 pb-5">
                <h2 class="text-[17px] font-extrabold text-slate-950">Access Configuration</h2>
            </div>

            <form>
                {{-- 1. Select Admin User --}}
                <div class="mb-10">
                    <label class="block text-[13px] font-bold text-slate-700 mb-2 uppercase tracking-wide">1. SELECT ADMIN USER</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" placeholder="Search for an admin by name or email (e.g. alex.smith@biogenix.com)" class="h-12 w-full rounded-xl border border-slate-200 bg-slate-50 pl-11 pr-4 text-[14px] text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-300 focus:bg-white focus:ring-4 focus:ring-slate-200/50">
                    </div>
                    <p class="mt-2 text-[12px] text-slate-500">The user who will be given the power to impersonate others.</p>
                </div>

                {{-- 2. Target Impersonation Scope --}}
                <div class="mb-10">
                    <label class="block text-[13px] font-bold text-slate-700 mb-4 uppercase tracking-wide">2. TARGET IMPERSONATION SCOPE</label>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[13px] font-bold text-slate-700 mb-2">By Role</label>
                            <div class="relative">
                                <select aria-label="Select a target role..." class="h-12 w-full appearance-none rounded-xl border border-slate-200 bg-slate-50 px-4 pr-10 text-[14px] text-slate-900 outline-none transition focus:border-slate-300 focus:bg-white focus:ring-4 focus:ring-slate-200/50">
                                    <option value="" disabled selected>Select a target role...</option>
                                    <option value="manager">Manager</option>
                                    <option value="editor">Editor</option>
                                    <option value="viewer">Viewer</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-slate-400">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-[13px] font-bold text-slate-700 mb-2">By Specific User (Optional)</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-400">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <input type="text" placeholder="Target individual username" class="h-12 w-full rounded-xl border border-slate-200 bg-slate-50 pl-11 pr-4 text-[14px] text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-300 focus:bg-white focus:ring-4 focus:ring-slate-200/50">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. Security Controls --}}
                <div class="mb-4">
                    <label class="block text-[13px] font-bold text-slate-700 mb-4 uppercase tracking-wide">3. SECURITY CONTROLS</label>
                    
                    <div class="space-y-4">
                        {{-- Control 1 --}}
                        <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-5 flex items-center justify-between gap-4">
                            <div>
                                <h3 class="text-[14px] font-extrabold text-slate-900">Require Audit Reason</h3>
                                <p class="text-[13px] text-slate-500 mt-1">User must provide a justification before starting an impersonation session.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                <input type="checkbox" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-slate-100"></div>
                            </label>
                        </div>
                        
                        {{-- Control 2 --}}
                        <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-5 flex flex-col gap-4">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <h3 class="text-[14px] font-extrabold text-slate-900">Temporary Access</h3>
                                    <p class="text-[13px] text-slate-500 mt-1">Access will automatically expire after a set duration.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                    <input type="checkbox" class="sr-only peer" checked>
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-slate-100"></div>
                                </label>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-[13px] font-semibold text-slate-600">Duration:</span>
                                <div class="relative w-32">
                                    <select class="h-9 w-full appearance-none rounded-lg border border-slate-200 bg-white px-3 pr-8 text-[13px] font-semibold text-slate-800 outline-none transition focus:border-slate-300">
                                        <option>1 Hour</option>
                                        <option selected>2 Hours</option>
                                        <option>4 Hours</option>
                                        <option>8 Hours</option>
                                        <option>24 Hours</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-2 flex items-center text-slate-400">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-slate-100">
                    <a href="{{ route('admin.role-permission') }}" class="ajax-link px-6 py-3 text-[14px] font-bold border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 transition cursor-pointer">Cancel</a>
                    <button type="button" id="grantImpersonationBtn" class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-100 px-7 py-3 text-[14px] font-extrabold text-white shadow-[0_10px_20px_rgba(11,37,94,0.18)] transition hover:brightness-105 cursor-pointer">
                        Grant Access
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    if (typeof grantImpersonationLoaded === 'undefined') {
        window.grantImpersonationLoaded = true;
        
        document.addEventListener('click', function(e) {
            if (e.target && e.target.id === 'grantImpersonationBtn') {
                e.preventDefault();
                alert('Impersonation access granted successfully!');
                var tmpLink = document.createElement('a');
                tmpLink.href = "{{ route('admin.role-permission') }}";
                tmpLink.className = 'ajax-link hidden';
                document.body.appendChild(tmpLink);
                tmpLink.click();
                document.body.removeChild(tmpLink);
            }
        });
    }
</script>
@endpush

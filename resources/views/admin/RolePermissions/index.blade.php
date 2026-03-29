@extends('admin.layout')

@section('title', 'Roles & Permissions Center - Biogenix Admin')

@section('admin_content')
    @php
        $tabs = [
            ['label' => 'Role Management', 'active' => true],
            ['label' => 'Permission Mapping', 'active' => false],
            ['label' => 'User Overrides', 'active' => false],
            ['label' => 'Department Access', 'active' => false],
            ['label' => 'Delegation & Impersonation', 'active' => false],
        ];

        $systemRoles = [
            ['name' => 'Super Admin', 'summary' => 'Unrestricted system-wide access', 'users' => 4, 'status' => 'Active'],
            ['name' => 'Sales Manager', 'summary' => 'Regional sales & pipeline tools', 'users' => 12, 'status' => 'Active'],
            ['name' => 'Lab Technician', 'summary' => 'R&D and inventory management', 'users' => 48, 'status' => 'Active'],
        ];

        $permissions = [
            ['code' => 'billing.invoice.create', 'description' => 'Generate customer invoices'],
            ['code' => 'lab.sample.approve', 'description' => 'Sign off on biological tests'],
            ['code' => 'user.profile.export', 'description' => 'Export CSV of user data'],
        ];

        $matrixRoles = [
            ['key' => 'super_admin', 'label' => 'Role: Super Admin', 'actions' => ['View', 'Create', 'Edit', 'Del', 'Exp']],
            ['key' => 'sales_manager', 'label' => 'Role: Sales Manager', 'actions' => ['View', 'Create', 'Edit', 'Del']],
        ];

        $matrixRows = [
            ['module' => 'Order Management', 'super_admin' => [true, true, true, true, true], 'sales_manager' => [true, true, true, false]],
            ['module' => 'Pricing Controls', 'super_admin' => [true, true, true, true, true], 'sales_manager' => [true, false, false, false]],
            ['module' => 'Clinical Data Access', 'super_admin' => [true, true, true, true, true], 'sales_manager' => [false, false, false, false]],
        ];

        $departments = [
            ['name' => 'R&D Lab Team', 'inherit' => 'Inherits: Lab Technician Role', 'kind' => 'lab'],
            ['name' => 'APAC Sales', 'inherit' => 'Inherits: Sales Manager Role', 'kind' => 'globe'],
        ];

        $subAdmins = [
            ['name' => 'Marcus Thorne', 'scope' => 'Scope: EMEA Operations Only', 'badge' => 'Limited', 'avatar' => 'MT', 'tone' => 'bg-cyan-100 text-cyan-700'],
            ['name' => 'Elena Rodriguez', 'scope' => 'Scope: Inventory Module Only', 'badge' => 'Module-Based', 'avatar' => 'ER', 'tone' => 'bg-amber-100 text-secondary-700'],
        ];
    @endphp



    {{-- ═══ MAIN ROLES & PERMISSIONS VIEW ═══ --}}
    <div id="roleMainView" class="space-y-6 text-slate-900">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Roles &amp; Permissions Center</h1>
            <p class="text-sm text-slate-500 mt-1 max-w-3xl">Manage global access controls, granular functional mapping, and individual user overrides.</p>
        </div>

        <div class="overflow-x-auto border-b border-slate-200 [&::-webkit-scrollbar]:hidden">
            <nav class="-mb-px flex min-w-max gap-8 pr-4">
                @foreach ($tabs as $tab)
                    <a href="#" class="border-b-2 px-0.5 pb-4 text-[14px] font-semibold transition {{ $tab['active'] ? 'border-slate-900 text-slate-950' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                        {{ $tab['label'] }}
                    </a>
                @endforeach
            </nav>
        </div>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,2.3fr)_minmax(18rem,1fr)]">
            <section class="overflow-hidden rounded-[24px] border border-slate-200/80 bg-white shadow-[0_10px_28px_rgba(15,23,42,0.05)]">
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 px-6 py-5">
                    <div class="flex items-center gap-3">
                        <svg class="h-[18px] w-[18px] text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <h2 class="text-[17px] font-extrabold text-slate-950">System Roles</h2>
                    </div>

                    <a href="{{ route('admin.role-permission.add-role') }}" class="ajax-link inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 hover:bg-primary-700 px-4 py-3 text-[13px] font-extrabold text-white shadow-[0_10px_20px_rgba(6,81,237,0.18)] transition cursor-pointer">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Role
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="text-left">
                                <th class="px-6 py-4 text-[10px] font-extrabold uppercase tracking-[0.16em] text-slate-400">Role Name</th>
                                <th class="px-4 py-4 text-center text-[10px] font-extrabold uppercase tracking-[0.16em] text-slate-400">Users</th>
                                <th class="px-4 py-4 text-center text-[10px] font-extrabold uppercase tracking-[0.16em] text-slate-400">Status</th>
                                <th class="px-6 py-4 text-right text-[10px] font-extrabold uppercase tracking-[0.16em] text-slate-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($systemRoles as $role)
                                <tr class="border-t border-slate-100">
                                    <td class="px-6 py-5">
                                        <div class="text-[15px] font-extrabold text-slate-950">{{ $role['name'] }}</div>
                                        <div class="mt-1 text-[12px] text-slate-500">{{ $role['summary'] }}</div>
                                    </td>
                                    <td class="px-4 py-5 text-center text-[15px] font-semibold text-slate-700">{{ $role['users'] }}</td>
                                    <td class="px-4 py-5 text-center">
                                        <span class="inline-flex rounded-full bg-primary-50 px-2 py-1 text-[9px] font-extrabold uppercase tracking-[0.12em] text-primary-600">{{ $role['status'] }}</span>
                                    </td>
                                    <td class="px-6 py-5 text-right">
                                        <x-ui.action-icon type="edit" aria-label="Edit {{ $role['name'] }}">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </x-ui.action-icon>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="overflow-hidden rounded-[24px] border border-slate-200/80 bg-white shadow-[0_10px_28px_rgba(15,23,42,0.05)]">
                <div class="flex items-center gap-3 border-b border-slate-100 px-6 py-5">
                    <svg class="h-[18px] w-[18px] text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    <h2 class="text-[17px] font-extrabold text-slate-950">Permissions</h2>
                </div>

                <div class="space-y-3 p-4 sm:p-5">
                    @foreach ($permissions as $permission)
                        <button type="button" class="flex w-full items-start justify-between gap-3 rounded-2xl border border-slate-100 bg-white px-4 py-4 text-left transition hover:border-slate-200 hover:shadow-[0_8px_18px_rgba(15,23,42,0.04)] cursor-pointer">
                            <div class="min-w-0">
                                <div class="font-mono text-[14px] font-semibold text-slate-900">{{ $permission['code'] }}</div>
                                <div class="mt-1 text-[12px] text-slate-500">{{ $permission['description'] }}</div>
                            </div>
                            <span class="mt-0.5 inline-flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-slate-100 text-[10px] font-extrabold text-slate-400">i</span>
                        </button>
                    @endforeach

                    <a href="{{ route('admin.role-permission.add-permission') }}" class="ajax-link inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary-600 hover:bg-primary-700 px-4 py-3 text-[13px] font-extrabold text-white shadow-[0_10px_20px_rgba(6,81,237,0.18)] transition cursor-pointer">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Permission
                    </a>
                </div>
            </section>
        </div>

        <section class="overflow-hidden rounded-[24px] border border-slate-200/80 bg-white shadow-[0_10px_28px_rgba(15,23,42,0.05)]">
            <div class="flex flex-col gap-3 border-b border-slate-100 px-6 py-5 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center gap-3">
                    <svg class="h-[18px] w-[18px] text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <h2 class="text-[17px] font-extrabold text-slate-950">Permission Mapping Matrix</h2>
                </div>

                <div class="flex items-center gap-3 text-[12px] text-slate-500">
                    <span class="inline-flex h-2.5 w-2.5 rounded-full bg-slate-100 shadow-[0_0_0_4px_rgba(18,54,125,0.06)]"></span>
                    <span>Mapping specific functional permissions (Rows) to System Roles (Columns)</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left" style="table-layout: fixed;">
                    <thead>
                        <tr class="bg-slate-50/80">
                            <th rowspan="2" class="w-[31%] border-r border-b border-slate-200 px-6 py-5 align-bottom text-[10px] font-extrabold uppercase tracking-[0.16em] text-slate-500">Functional Permission (Module)</th>
                            @foreach ($matrixRoles as $roleIndex => $role)
                                <th colspan="{{ count($role['actions']) }}" class="border-b border-slate-200 px-4 py-4 text-center text-[13px] font-extrabold uppercase tracking-[0.1em] text-slate-600 {{ $roleIndex < count($matrixRoles) - 1 ? 'border-r' : '' }}">
                                    {{ $role['label'] }}
                                </th>
                            @endforeach
                        </tr>
                        <tr class="bg-slate-50/80">
                            @foreach ($matrixRoles as $roleIndex => $role)
                                @foreach ($role['actions'] as $actionIndex => $action)
                                    <th class="border-b border-slate-200 px-2 py-4 text-center text-[10px] font-extrabold uppercase tracking-[0.16em] text-slate-400 {{ $actionIndex === count($role['actions']) - 1 && $roleIndex < count($matrixRoles) - 1 ? 'border-r' : '' }}">
                                        {{ $action }}
                                    </th>
                                @endforeach
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="text-[16px] font-semibold text-slate-700">
                        @foreach ($matrixRows as $row)
                            <tr>
                                <th class="border-r border-b border-slate-100 px-6 py-6 text-left font-bold text-slate-700">{{ $row['module'] }}</th>
                                @foreach ($matrixRoles as $roleIndex => $role)
                                    @foreach ($row[$role['key']] as $actionIndex => $enabled)
                                        <td class="border-b border-slate-100 px-2 py-5 text-center {{ $actionIndex === count($row[$role['key']]) - 1 && $roleIndex < count($matrixRoles) - 1 ? 'border-r' : '' }}">
                                            <span class="mx-auto inline-flex h-[18px] w-[18px] items-center justify-center rounded-full border-[1.6px] {{ $enabled ? 'border-slate-200 bg-slate-100 text-white' : 'border-slate-300 bg-white text-transparent' }}">
                                                @if ($enabled)
                                                    <svg class="h-[11px] w-[11px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                @endif
                                            </span>
                                        </td>
                                    @endforeach
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end px-4 py-4 sm:px-6">
                <button type="button" class="inline-flex items-center justify-center rounded-xl bg-primary-600 hover:bg-primary-700 px-7 py-3 text-[14px] font-extrabold text-white shadow-[0_10px_20px_rgba(6,81,237,0.18)] transition cursor-pointer">
                    Save Matrix Changes
                </button>
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-2">
            <section class="overflow-hidden rounded-[24px] border border-slate-200/80 bg-white shadow-[0_10px_28px_rgba(15,23,42,0.05)]">
                <div class="flex items-center gap-3 border-b border-slate-100 px-6 py-5">
                    <svg class="h-[18px] w-[18px] text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H7m6-8v3m0 10v3M3 4h7v7H3V4zm0 9h7v7H3v-7z" />
                    </svg>
                    <h2 class="text-[17px] font-extrabold text-slate-950">User-Based Overrides</h2>
                </div>

                <div class="space-y-5 p-6">
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" placeholder="Search user to apply exception..." class="h-11 w-full rounded-xl border border-slate-200 bg-slate-50 pl-11 pr-4 text-[14px] text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-300 focus:ring-4 focus:ring-slate-200/50">
                    </div>

                    <div class="flex flex-col gap-4 border-b border-dashed border-slate-200 pb-5 md:flex-row md:items-center md:justify-between">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-indigo-100 text-[15px] font-extrabold text-indigo-700">JW</span>
                            <div>
                                <div class="text-[15px] font-extrabold text-slate-950">James Wilson</div>
                                <div class="mt-1 text-[12px] text-slate-500">Sales Representative (Global)</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="inline-flex rounded-full bg-primary-50 px-2 py-1 text-[9px] font-extrabold uppercase tracking-[0.12em] text-primary-600">1 Add</span>
                            <span class="inline-flex rounded-full bg-rose-50 px-2 py-1 text-[9px] font-extrabold uppercase tracking-[0.12em] text-rose-600">0 Revoke</span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <div class="text-[14px] font-bold text-slate-900">Override: <span class="font-mono text-slate-600">lab.audit.read</span></div>
                            <div class="mt-1 text-[12px] text-slate-500">Special grant for quarterly compliance audit</div>
                        </div>

                        <a href="{{ route('admin.role-permission.add-override') }}" class="ajax-link inline-flex items-center gap-1 text-[12px] font-extrabold uppercase tracking-[0.12em] text-primary-600 cursor-pointer hover:text-primary-600 transition">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            Add
                        </a>
                    </div>
                </div>
            </section>

            <section class="overflow-hidden rounded-[24px] border border-slate-200/80 bg-white shadow-[0_10px_28px_rgba(15,23,42,0.05)]">
                <div class="flex items-center gap-3 border-b border-slate-100 px-6 py-5">
                    <svg class="h-[18px] w-[18px] text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <h2 class="text-[17px] font-extrabold text-slate-950">Group &amp; Department Access</h2>
                </div>

                <div class="space-y-4 p-6">
                    @foreach ($departments as $department)
                        <div class="flex items-center justify-between gap-4 rounded-2xl border border-slate-100 px-4 py-4">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-500">
                                    @if ($department['kind'] === 'lab')
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5C21.846 17.846 20.953 20 19.172 20H4.828c-1.781 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                        </svg>
                                    @else
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @endif
                                </span>
                                <div>
                                    <div class="text-[15px] font-extrabold text-slate-950">{{ $department['name'] }}</div>
                                    <div class="mt-1 text-[12px] text-slate-500">{{ $department['inherit'] }}</div>
                                </div>
                            </div>

                            <x-ui.action-icon type="edit">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </x-ui.action-icon>
                        </div>
                    @endforeach

                    <a href="{{ route('admin.role-permission.assign-dept-role') }}" class="ajax-link flex min-h-[52px] w-full items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-white text-[15px] font-semibold text-slate-500 transition hover:border-slate-400 hover:text-slate-700 cursor-pointer">
                        + Assign Role to Department
                    </a>
                </div>
            </section>
        </div>

        <section class="overflow-hidden rounded-[24px] border border-slate-200/80 bg-white shadow-[0_10px_28px_rgba(15,23,42,0.05)]">
            <div class="flex items-center gap-3 border-b border-slate-100 px-6 py-5">
                <svg class="h-[18px] w-[18px] text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <h2 class="text-[17px] font-extrabold text-slate-950">Delegation &amp; Impersonation</h2>
            </div>

            <div class="grid xl:grid-cols-2">
                <div class="space-y-5 p-6 xl:border-r xl:border-slate-100">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <h3 class="text-[13px] font-extrabold uppercase tracking-[0.08em] text-slate-900">Regional Sub-Admins</h3>
                        <a href="{{ route('admin.role-permission.add-delegation') }}" class="ajax-link inline-flex items-center gap-2 text-[13px] font-bold text-slate-600 cursor-pointer hover:text-slate-600 transition">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Add Delegated Role to User
                        </a>
                    </div>

                    @foreach ($subAdmins as $admin)
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex h-11 w-11 items-center justify-center rounded-full text-[14px] font-extrabold {{ $admin['tone'] }}">{{ $admin['avatar'] }}</span>
                                <div>
                                    <div class="text-[15px] font-extrabold text-slate-950">{{ $admin['name'] }}</div>
                                    <div class="mt-1 text-[12px] text-slate-500">{{ $admin['scope'] }}</div>
                                </div>
                            </div>

                            <span class="inline-flex rounded-full bg-indigo-50 px-3 py-1 text-[9px] font-extrabold uppercase tracking-[0.12em] text-indigo-600">{{ $admin['badge'] }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="space-y-5 p-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <h3 class="text-[13px] font-extrabold uppercase tracking-[0.08em] text-slate-900">Impersonation Access</h3>
                        <label class="inline-flex items-center gap-2 text-[12px] font-bold text-slate-700">
                            <input type="checkbox" checked class="h-4 w-4 rounded border-slate-300 text-slate-600 focus:ring-slate-200">
                            Grant Impersonation Access
                        </label>
                    </div>

                    <div class="flex items-start gap-3 rounded-2xl border border-rose-100 bg-rose-50 px-4 py-4 text-[12px] leading-5 text-rose-700">
                        <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <p>Authorized Super Admins can securely log in as another user for troubleshooting. Every session is recorded in immutable audit logs.</p>
                    </div>

                    <div class="space-y-3">
                        <div class="flex flex-col gap-1 text-[13px] sm:flex-row sm:items-center sm:justify-between">
                            <span class="text-slate-500">Today, 09:12 AM</span>
                            <span class="text-slate-700">Admin logged in as <a href="#" class="font-bold text-slate-600 underline">r.taylor@biogenix.com</a></span>
                        </div>
                        <div class="flex flex-col gap-1 text-[13px] sm:flex-row sm:items-center sm:justify-between">
                            <span class="text-slate-500">Yesterday, 04:45 PM</span>
                            <span class="text-slate-700">Admin logged in as <a href="#" class="font-bold text-slate-600 underline">k.mills@biogenix.com</a></span>
                        </div>
                    </div>

                    <a href="{{ route('admin.role-permission.grant-impersonation') }}" class="ajax-link inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary-600 hover:bg-primary-700 px-4 py-3 text-[14px] font-extrabold text-white shadow-[0_10px_20px_rgba(6,81,237,0.18)] transition cursor-pointer">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        Start New Impersonation Session
                    </a>
                </div>
            </div>
        </section>
    </div>

@push('scripts')
<script>
    // Permission search filter (Add Role form)
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
</script>
@endpush
@endsection

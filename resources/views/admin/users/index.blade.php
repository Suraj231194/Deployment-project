@extends('layouts.app')

@php
    $panelClass = 'rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm md:p-8';
    $sectionTitleClass = 'text-xl font-semibold text-slate-950';
    $sectionCopyClass = 'mt-1 text-sm leading-6 text-slate-500';
    $fieldLabelClass = 'mb-2 block text-sm font-semibold text-slate-700';
    $fieldClass = 'h-11 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm text-slate-900 shadow-sm outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10';
    $textareaClass = 'min-h-[7rem] w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10';
    $primaryButtonClass = 'inline-flex h-11 items-center justify-center rounded-xl bg-primary-600 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700';
    $secondaryButtonClass = 'inline-flex h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50';
    $dangerButtonClass = 'inline-flex h-10 items-center justify-center rounded-xl border border-rose-200 bg-rose-50 px-4 text-sm font-semibold text-rose-700 transition hover:bg-rose-100';
    $tableWrapClass = 'overflow-hidden rounded-2xl border border-slate-200';
    $tableHeadClass = 'bg-slate-50 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500';
    $tableCellClass = 'px-4 py-4 align-top text-sm text-slate-700';
@endphp

@section('content')
    <div class="mx-auto w-full max-w-none space-y-6 px-4 py-6 sm:px-6 lg:px-8 xl:px-10">
        <section class="rounded-[32px] border border-slate-200 bg-[linear-gradient(135deg,#ffffff_0%,#f8fbff_58%,#dbeafe_100%)] p-6 shadow-sm md:p-8">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">Admin Console</p>
                    <h1 class="mt-3 text-3xl font-bold tracking-tight text-slate-950 md:text-4xl">Admin Access Control Console</h1>
                    <p class="mt-3 text-sm leading-7 text-slate-600 md:text-base">
                        Manage B2B approvals, internal users, delegated admins, permission overrides, scope assignments, and impersonation from one streamlined workspace.
                    </p>
                </div>
                <div class="grid gap-3 sm:grid-cols-3">
                    <div class="rounded-2xl border border-white/80 bg-white/80 px-4 py-3 shadow-sm backdrop-blur">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Pending B2B</p>
                        <p class="mt-2 text-2xl font-bold text-slate-950">{{ $pendingB2bUsers->count() }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/80 bg-white/80 px-4 py-3 shadow-sm backdrop-blur">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Overrides</p>
                        <p class="mt-2 text-2xl font-bold text-slate-950">{{ $userOverrides->count() }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/80 bg-white/80 px-4 py-3 shadow-sm backdrop-blur">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Delegated Scopes</p>
                        <p class="mt-2 text-2xl font-bold text-slate-950">{{ $delegatedScopes->count() }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="{{ $panelClass }}">
            <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="{{ $sectionTitleClass }}">Pending B2B Approvals</h2>
                    <p class="{{ $sectionCopyClass }}">Review pending requests and approve or reject them without leaving the management console.</p>
                </div>
                <span class="inline-flex items-center rounded-full border border-primary-100 bg-primary-50 px-3 py-1 text-xs font-semibold text-primary-700">{{ $pendingB2bUsers->count() }} pending</span>
            </div>

            @if ($pendingB2bUsers->count())
                <div class="mt-6 {{ $tableWrapClass }}">
                    <table class="min-w-full divide-y divide-slate-200 bg-white">
                        <thead class="{{ $tableHeadClass }}">
                            <tr>
                                <th class="px-4 py-3">Name</th>
                                <th class="px-4 py-3">Email</th>
                                <th class="px-4 py-3">B2B Type</th>
                                <th class="px-4 py-3">Company</th>
                                <th class="px-4 py-3">Requested At</th>
                                <th class="px-4 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($pendingB2bUsers as $pending)
                                <tr>
                                    <td class="{{ $tableCellClass }} font-semibold text-slate-950">{{ $pending->name }}</td>
                                    <td class="{{ $tableCellClass }}">{{ $pending->email }}</td>
                                    <td class="{{ $tableCellClass }}">{{ strtoupper($pending->b2b_type ?? '-') }}</td>
                                    <td class="{{ $tableCellClass }}">{{ $pending->company_name ?? '-' }}</td>
                                    <td class="{{ $tableCellClass }}">{{ $pending->created_at }}</td>
                                    <td class="{{ $tableCellClass }}">
                                        <div class="flex flex-wrap gap-2">
                                            <form method="POST" action="{{ route('admin.users.b2b.approve', $pending->id) }}">
                                                @csrf
                                                <button class="{{ $primaryButtonClass }}" type="submit">Approve</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.users.b2b.reject', $pending->id) }}">
                                                @csrf
                                                <button class="{{ $secondaryButtonClass }}" type="submit">Reject</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm text-slate-600">No pending B2B approvals.</div>
            @endif
        </section>

        <div class="grid gap-6 xl:grid-cols-2">
            <section class="{{ $panelClass }}">
                <div>
                    <h2 class="{{ $sectionTitleClass }}">Create Internal User</h2>
                    <p class="{{ $sectionCopyClass }}">Internal users are operational users and can belong to multiple departments.</p>
                </div>

                <form method="POST" action="{{ route('admin.users.internal.store') }}" class="mt-6 space-y-5">
                    @csrf
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="internal_name" class="{{ $fieldLabelClass }}">Name</label>
                            <input id="internal_name" name="name" class="{{ $fieldClass }}" required>
                        </div>
                        <div>
                            <label for="internal_email" class="{{ $fieldLabelClass }}">Email</label>
                            <input id="internal_email" type="email" name="email" class="{{ $fieldClass }}" required>
                        </div>
                        <div>
                            <label for="internal_password" class="{{ $fieldLabelClass }}">Password</label>
                            <input id="internal_password" type="password" name="password" class="{{ $fieldClass }}" required>
                        </div>
                        <div>
                            <label for="internal_password_confirmation" class="{{ $fieldLabelClass }}">Confirm Password</label>
                            <input id="internal_password_confirmation" type="password" name="password_confirmation" class="{{ $fieldClass }}" required>
                        </div>
                    </div>
                    <div>
                        <label for="department_ids" class="{{ $fieldLabelClass }}">Departments</label>
                        <select id="department_ids" name="department_ids[]" multiple size="6" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10" required>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="{{ $primaryButtonClass }}" type="submit">Create Internal User</button>
                </form>
            </section>

            <section class="{{ $panelClass }}">
                <div>
                    <h2 class="{{ $sectionTitleClass }}">Create Delegated Admin</h2>
                    <p class="{{ $sectionCopyClass }}">Delegated admins receive limited admin-level access with explicit scope controls.</p>
                </div>

                <form method="POST" action="{{ route('admin.users.delegated.store') }}" class="mt-6 space-y-5">
                    @csrf
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="delegated_name" class="{{ $fieldLabelClass }}">Name</label>
                            <input id="delegated_name" name="name" class="{{ $fieldClass }}" required>
                        </div>
                        <div>
                            <label for="delegated_email" class="{{ $fieldLabelClass }}">Email</label>
                            <input id="delegated_email" type="email" name="email" class="{{ $fieldClass }}" required>
                        </div>
                        <div>
                            <label for="delegated_password" class="{{ $fieldLabelClass }}">Password</label>
                            <input id="delegated_password" type="password" name="password" class="{{ $fieldClass }}" required>
                        </div>
                        <div>
                            <label for="delegated_password_confirmation" class="{{ $fieldLabelClass }}">Confirm Password</label>
                            <input id="delegated_password_confirmation" type="password" name="password_confirmation" class="{{ $fieldClass }}" required>
                        </div>
                    </div>
                    <button class="{{ $primaryButtonClass }}" type="submit">Create Delegated Admin</button>
                </form>
            </section>
        </div>

        <section class="{{ $panelClass }}">
            <div>
                <h2 class="{{ $sectionTitleClass }}">User-Level Permission Override</h2>
                <p class="{{ $sectionCopyClass }}">Assign allow or deny overrides to specific users without changing the underlying role model.</p>
            </div>

            <form method="POST" action="{{ route('admin.users.permissions.set', 0) }}" id="override-form" class="mt-6 grid gap-4 xl:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_minmax(0,0.7fr)_auto]">
                @csrf
                <div>
                    <label for="override_user_id" class="{{ $fieldLabelClass }}">User</label>
                    <select id="override_user_id" name="override_user_id" class="{{ $fieldClass }}" required>
                        <option value="">Select user</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }}) [{{ $user->user_type }}]</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="permission_id" class="{{ $fieldLabelClass }}">Permission</label>
                    <select id="permission_id" name="permission_id" class="{{ $fieldClass }}" required>
                        <option value="">Select permission</option>
                        @foreach ($permissions as $permission)
                            <option value="{{ $permission->id }}">{{ $permission->slug }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="grant_type" class="{{ $fieldLabelClass }}">Override Type</label>
                    <select id="grant_type" name="grant_type" class="{{ $fieldClass }}" required>
                        <option value="allow">Allow</option>
                        <option value="deny">Deny</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button class="{{ $primaryButtonClass }} w-full" type="submit">Save Override</button>
                </div>
            </form>

            <div class="mt-8">
                <h3 class="text-lg font-semibold text-slate-950">Existing Overrides</h3>
                @if ($userOverrides->count())
                    <div class="mt-4 {{ $tableWrapClass }}">
                        <table class="min-w-full divide-y divide-slate-200 bg-white">
                            <thead class="{{ $tableHeadClass }}">
                                <tr>
                                    <th class="px-4 py-3">User</th>
                                    <th class="px-4 py-3">Permission</th>
                                    <th class="px-4 py-3">Type</th>
                                    <th class="px-4 py-3">Granted By</th>
                                    <th class="px-4 py-3">Updated At</th>
                                    <th class="px-4 py-3">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($userOverrides as $override)
                                    <tr>
                                        <td class="{{ $tableCellClass }}">
                                            <p class="font-semibold text-slate-950">{{ $override->user_name }}</p>
                                            <p class="mt-1 text-xs text-slate-500">{{ $override->user_email }}</p>
                                        </td>
                                        <td class="{{ $tableCellClass }}">{{ $override->permission_slug }}</td>
                                        <td class="{{ $tableCellClass }}">{{ strtoupper($override->grant_type) }}</td>
                                        <td class="{{ $tableCellClass }}">{{ $override->granted_by_name ?? '-' }}</td>
                                        <td class="{{ $tableCellClass }}">{{ $override->updated_at }}</td>
                                        <td class="{{ $tableCellClass }}">
                                            <form method="POST" action="{{ route('admin.users.permissions.delete', $override->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="{{ $dangerButtonClass }}" type="submit">Remove</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm text-slate-600">No user-level overrides yet.</div>
                @endif
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-2">
            <section class="{{ $panelClass }}">
                <div>
                    <h2 class="{{ $sectionTitleClass }}">Delegated Admin Scope</h2>
                    <p class="{{ $sectionCopyClass }}">Assign company-level scope to delegated admins and review all active scope mappings.</p>
                </div>

                <form method="POST" action="{{ route('admin.users.scopes.set', 0) }}" id="scope-form" class="mt-6 grid gap-4 md:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto]">
                    @csrf
                    <div>
                        <label for="scope_user_id" class="{{ $fieldLabelClass }}">Delegated Admin User</label>
                        <select id="scope_user_id" name="scope_user_id" class="{{ $fieldClass }}" required>
                            <option value="">Select delegated admin</option>
                            @foreach ($delegatedAdmins as $delegatedAdmin)
                                <option value="{{ $delegatedAdmin->id }}">{{ $delegatedAdmin->name }} ({{ $delegatedAdmin->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="company_id" class="{{ $fieldLabelClass }}">Company Scope</label>
                        <select id="company_id" name="company_id" class="{{ $fieldClass }}" required>
                            <option value="">Select company</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button class="{{ $primaryButtonClass }} w-full" type="submit">Assign Scope</button>
                    </div>
                </form>

                @if ($delegatedScopes->count())
                    <div class="mt-6 {{ $tableWrapClass }}">
                        <table class="min-w-full divide-y divide-slate-200 bg-white">
                            <thead class="{{ $tableHeadClass }}">
                                <tr>
                                    <th class="px-4 py-3">Delegated Admin</th>
                                    <th class="px-4 py-3">Scope</th>
                                    <th class="px-4 py-3">Assigned By</th>
                                    <th class="px-4 py-3">Updated At</th>
                                    <th class="px-4 py-3">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($delegatedScopes as $scope)
                                    <tr>
                                        <td class="{{ $tableCellClass }}">
                                            <p class="font-semibold text-slate-950">{{ $scope->delegated_name }}</p>
                                            <p class="mt-1 text-xs text-slate-500">{{ $scope->delegated_email }}</p>
                                        </td>
                                        <td class="{{ $tableCellClass }}">{{ strtoupper($scope->scope_type) }}: {{ $scope->company_name ?? $scope->scope_value }}</td>
                                        <td class="{{ $tableCellClass }}">{{ $scope->assigned_by_name ?? '-' }}</td>
                                        <td class="{{ $tableCellClass }}">{{ $scope->updated_at }}</td>
                                        <td class="{{ $tableCellClass }}">
                                            <form method="POST" action="{{ route('admin.users.scopes.delete', $scope->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="{{ $dangerButtonClass }}" type="submit">Remove</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm text-slate-600">No delegated scopes configured.</div>
                @endif
            </section>

            <section class="{{ $panelClass }}">
                <div>
                    <h2 class="{{ $sectionTitleClass }}">Impersonate User</h2>
                    <p class="{{ $sectionCopyClass }}">Support and debugging only. Every impersonation remains audited with start and end timestamps.</p>
                </div>

                <div class="mt-6 {{ $tableWrapClass }}">
                    <table class="min-w-full divide-y divide-slate-200 bg-white">
                        <thead class="{{ $tableHeadClass }}">
                            <tr>
                                <th class="px-4 py-3">Name</th>
                                <th class="px-4 py-3">Email</th>
                                <th class="px-4 py-3">User Type</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($users as $user)
                                <tr>
                                    <td class="{{ $tableCellClass }} font-semibold text-slate-950">{{ $user->name }}</td>
                                    <td class="{{ $tableCellClass }}">{{ $user->email }}</td>
                                    <td class="{{ $tableCellClass }}">{{ strtoupper($user->user_type) }}</td>
                                    <td class="{{ $tableCellClass }}">{{ strtoupper($user->status) }}</td>
                                    <td class="{{ $tableCellClass }}">
                                        <button class="{{ $secondaryButtonClass }}" type="button" disabled>Impersonate</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <script>
            const adminUsersBase = "{{ url('/admin/users') }}";
            const overrideForm = document.getElementById('override-form');
            const overrideUserSelect = document.getElementById('override_user_id');
            overrideForm.addEventListener('submit', function () {
                const selectedUserId = overrideUserSelect.value;
                overrideForm.action = `${adminUsersBase}/${selectedUserId || '0'}/permissions`;
            });

            const scopeForm = document.getElementById('scope-form');
            const scopeUserSelect = document.getElementById('scope_user_id');
            scopeForm.addEventListener('submit', function () {
                const selectedUserId = scopeUserSelect.value;
                scopeForm.action = `${adminUsersBase}/${selectedUserId || '0'}/scopes/company`;
            });
        </script>
    </div>
@endsection

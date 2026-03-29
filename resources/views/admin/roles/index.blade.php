@extends('layouts.app')

@section('title', 'Role And Permission Management')

@php
    $panelClass = 'rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm md:p-8';
    $sectionTitleClass = 'text-xl font-semibold text-slate-950';
    $sectionCopyClass = 'mt-1 text-sm leading-6 text-slate-500';
    $fieldLabelClass = 'mb-2 block text-sm font-semibold text-slate-700';
    $fieldClass = 'h-11 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm text-slate-900 shadow-sm outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10';
    $primaryButtonClass = 'inline-flex h-11 items-center justify-center rounded-xl bg-primary-600 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700';
    $secondaryButtonClass = 'inline-flex h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50';
    $dangerButtonClass = 'inline-flex h-10 items-center justify-center rounded-xl border border-rose-200 bg-rose-50 px-4 text-sm font-semibold text-rose-700 transition hover:bg-rose-100';
    $tableWrapClass = 'overflow-hidden rounded-2xl border border-slate-200';
    $tableHeadClass = 'bg-slate-50 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500';
    $tableCellClass = 'px-4 py-4 align-top text-sm text-slate-700';
@endphp

@section('content')
    <div class="mx-auto w-full max-w-none space-y-6 px-4 py-6 sm:px-6 lg:px-8 xl:px-10">
        <section class="rounded-[32px] border border-slate-200 bg-[linear-gradient(135deg,#ffffff_0%,#f8fbff_55%,#dbeafe_100%)] p-6 shadow-sm md:p-8">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">Access Control</p>
                    <h1 class="mt-3 text-3xl font-bold tracking-tight text-slate-950 md:text-4xl">Role And Permission Management</h1>
                    <p class="mt-3 text-sm leading-7 text-slate-600 md:text-base">
                        Create roles, organize permissions, and assign access without leaving this single management surface.
                    </p>
                </div>
                <div class="grid gap-3 sm:grid-cols-3">
                    <div class="rounded-2xl border border-white/80 bg-white/80 px-4 py-3 shadow-sm backdrop-blur">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Roles</p>
                        <p class="mt-2 text-2xl font-bold text-slate-950">{{ $roles->count() }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/80 bg-white/80 px-4 py-3 shadow-sm backdrop-blur">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Permissions</p>
                        <p class="mt-2 text-2xl font-bold text-slate-950">{{ $permissions->count() }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/80 bg-white/80 px-4 py-3 shadow-sm backdrop-blur">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Selected Role</p>
                        <p class="mt-2 text-base font-bold text-slate-950">{{ $editingRole->name ?? 'None' }}</p>
                    </div>
                </div>
            </div>
        </section>

        @if (session('status'))
            <div class="rounded-2xl border border-primary-200 bg-primary-50 px-4 py-4 text-sm font-medium text-primary-600">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-4 text-sm text-rose-700">
                <p class="font-semibold">Please resolve the following validation issues:</p>
                <ul class="mt-2 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid gap-6 xl:grid-cols-2">
            <section class="{{ $panelClass }}">
                <div class="flex items-start gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-primary-50 text-primary-600">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5.25v13.5m6.75-6.75H5.25" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="{{ $sectionTitleClass }}">{{ $editingRole ? 'Update Role' : 'Add Role' }}</h2>
                        <p class="{{ $sectionCopyClass }}">Use clear names and slugs so access groups remain easy to scan as the system grows.</p>
                    </div>
                </div>

                <form method="POST" action="{{ $editingRole ? route('admin.roles.update', $editingRole->id) : route('admin.roles.store') }}" class="mt-6 space-y-5">
                    @csrf

                    @if ($editingRole)
                        @method('PUT')
                    @endif

                    <div>
                        <label for="role_name" class="{{ $fieldLabelClass }}">Role Name</label>
                        <input
                            id="role_name"
                            name="name"
                            value="{{ old('name', $editingRole->name ?? '') }}"
                            class="{{ $fieldClass }}"
                            required
                        >
                    </div>

                    <div>
                        <label for="role_slug" class="{{ $fieldLabelClass }}">Role Slug</label>
                        <input
                            id="role_slug"
                            name="slug"
                            value="{{ old('slug', $editingRole->slug ?? '') }}"
                            class="{{ $fieldClass }}"
                        >
                        <p class="mt-2 text-sm text-slate-500">Leave blank to auto-create a slug from the role name.</p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <button class="{{ $primaryButtonClass }}" type="submit">
                            {{ $editingRole ? 'Update Role' : 'Add Role' }}
                        </button>

                        @if ($editingRole)
                            <a class="{{ $secondaryButtonClass }}" href="{{ route('admin.roles.index') }}">Cancel</a>
                        @endif
                    </div>
                </form>
            </section>

            <section class="{{ $panelClass }}">
                <div class="flex items-start gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-secondary-50 text-secondary-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="{{ $sectionTitleClass }}">{{ $editingPermission ? 'Update Permission' : 'Add Permission' }}</h2>
                        <p class="{{ $sectionCopyClass }}">Keep permission slugs focused on one action so overrides stay predictable and auditable.</p>
                    </div>
                </div>

                <form method="POST" action="{{ $editingPermission ? route('admin.roles.permissions.update', $editingPermission->id) : route('admin.roles.permissions.store') }}" class="mt-6 space-y-5">
                    @csrf

                    @if ($editingPermission)
                        @method('PUT')
                    @endif

                    <div>
                        <label for="permission_name" class="{{ $fieldLabelClass }}">Permission Name</label>
                        <input
                            id="permission_name"
                            name="name"
                            value="{{ old('name', $editingPermission->name ?? '') }}"
                            class="{{ $fieldClass }}"
                            required
                        >
                    </div>

                    <div>
                        <label for="permission_slug" class="{{ $fieldLabelClass }}">Permission Slug</label>
                        <input
                            id="permission_slug"
                            name="slug"
                            value="{{ old('slug', $editingPermission->slug ?? '') }}"
                            class="{{ $fieldClass }}"
                        >
                        <p class="mt-2 text-sm text-slate-500">Leave blank to auto-create a slug from the permission name.</p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <button class="{{ $primaryButtonClass }}" type="submit">
                            {{ $editingPermission ? 'Update Permission' : 'Add Permission' }}
                        </button>

                        @if ($editingPermission)
                            <a class="{{ $secondaryButtonClass }}" href="{{ $editingRole ? route('admin.roles.show', $editingRole->id) : route('admin.roles.index') }}">Cancel</a>
                        @endif
                    </div>
                </form>
            </section>
        </div>

        <section class="{{ $panelClass }}">
            <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="{{ $sectionTitleClass }}">Existing Roles</h2>
                    <p class="{{ $sectionCopyClass }}">Review adoption, usage counts, and access entry points before making structural changes.</p>
                </div>
                <span class="inline-flex items-center rounded-full border border-primary-100 bg-primary-50 px-3 py-1 text-xs font-semibold text-primary-700">{{ $roles->count() }} configured</span>
            </div>

            @if ($roles->count())
                <div class="mt-6 {{ $tableWrapClass }}">
                    <table class="min-w-full divide-y divide-slate-200 bg-white">
                        <thead class="{{ $tableHeadClass }}">
                            <tr>
                                <th class="px-4 py-3">Name</th>
                                <th class="px-4 py-3">Slug</th>
                                <th class="px-4 py-3">Users</th>
                                <th class="px-4 py-3">Permissions</th>
                                <th class="px-4 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($roles as $role)
                                <tr>
                                    <td class="{{ $tableCellClass }} font-semibold text-slate-950">{{ $role->name }}</td>
                                    <td class="{{ $tableCellClass }}">{{ $role->slug }}</td>
                                    <td class="{{ $tableCellClass }}">{{ $role->users_count }}</td>
                                    <td class="{{ $tableCellClass }}">{{ $role->permissions_count }}</td>
                                    <td class="{{ $tableCellClass }}">
                                        <div class="flex flex-wrap gap-2">
                                            <a class="{{ $secondaryButtonClass }}" href="{{ route('admin.roles.show', $role->id) }}">Edit</a>
                                            <a class="{{ $secondaryButtonClass }}" href="{{ route('admin.roles.show', $role->id) }}">Manage Permissions</a>

                                            <form method="POST" action="{{ route('admin.roles.delete', $role->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="{{ $dangerButtonClass }}" type="submit">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm text-slate-600">No roles found.</div>
            @endif
        </section>

        <section class="{{ $panelClass }}">
            <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="{{ $sectionTitleClass }}">Existing Permissions</h2>
                    <p class="{{ $sectionCopyClass }}">Compare reuse across roles and watch for overrides that may indicate missing role-level rules.</p>
                </div>
                <span class="inline-flex items-center rounded-full border border-amber-100 bg-secondary-50 px-3 py-1 text-xs font-semibold text-secondary-700">{{ $permissions->count() }} available</span>
            </div>

            @if ($permissions->count())
                <div class="mt-6 {{ $tableWrapClass }}">
                    <table class="min-w-full divide-y divide-slate-200 bg-white">
                        <thead class="{{ $tableHeadClass }}">
                            <tr>
                                <th class="px-4 py-3">Name</th>
                                <th class="px-4 py-3">Slug</th>
                                <th class="px-4 py-3">Roles</th>
                                <th class="px-4 py-3">User Overrides</th>
                                <th class="px-4 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($permissions as $permission)
                                <tr>
                                    <td class="{{ $tableCellClass }} font-semibold text-slate-950">{{ $permission->name }}</td>
                                    <td class="{{ $tableCellClass }}">{{ $permission->slug }}</td>
                                    <td class="{{ $tableCellClass }}">{{ $permission->roles_count }}</td>
                                    <td class="{{ $tableCellClass }}">{{ $permission->user_overrides_count }}</td>
                                    <td class="{{ $tableCellClass }}">
                                        <div class="flex flex-wrap gap-2">
                                            <a
                                                class="{{ $secondaryButtonClass }}"
                                                href="{{ $editingRole ? route('admin.roles.show', ['roleId' => $editingRole->id, 'edit_permission_id' => $permission->id]) : route('admin.roles.index', ['edit_permission_id' => $permission->id]) }}"
                                            >
                                                Edit
                                            </a>

                                            <form method="POST" action="{{ route('admin.roles.permissions.delete', $permission->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="{{ $dangerButtonClass }}" type="submit">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm text-slate-600">No permissions found.</div>
            @endif
        </section>

        <section class="{{ $panelClass }}">
            <div class="flex items-start gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-primary-50 text-primary-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <div>
                    <h2 class="{{ $sectionTitleClass }}">Permissions For Selected Role</h2>
                    <p class="{{ $sectionCopyClass }}">Pick the permissions that should remain attached to the selected role, then save the access set in one step.</p>
                </div>
            </div>

            @if ($editingRole)
                <div class="mt-6 rounded-2xl border border-primary-100 bg-primary-50 px-4 py-4 text-sm text-primary-700">
                    Checked permissions will be kept for <strong>{{ $editingRole->name }}</strong>. Unchecked permissions will be removed when you save.
                </div>

                <form method="POST" action="{{ route('admin.roles.permissions.upsert', $editingRole->id) }}" class="mt-6 space-y-6">
                    @csrf

                    <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($permissions as $permission)
                            <label class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50/70 px-4 py-4 text-sm text-slate-700 transition hover:border-primary-200 hover:bg-primary-50/60">
                                <input
                                    type="checkbox"
                                    name="permission_ids[]"
                                    value="{{ $permission->id }}"
                                    class="mt-1 h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                                    {{ in_array($permission->id, $editingRolePermissionIds, true) ? 'checked' : '' }}
                                >
                                <span>
                                    <span class="block font-semibold text-slate-950">{{ $permission->name }}</span>
                                    <span class="mt-1 block text-xs uppercase tracking-[0.18em] text-slate-400">{{ $permission->slug }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>

                    <button class="{{ $primaryButtonClass }}" type="submit">Save Role Permissions</button>
                </form>
            @else
                <div class="mt-6 rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-sm text-slate-600">
                    Select a role from the role table to manage its permissions.
                </div>
            @endif
        </section>
    </div>
@endsection

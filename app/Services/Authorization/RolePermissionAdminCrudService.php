<?php

namespace App\Services\Authorization;

use App\Models\Authorization\Permission;
use App\Models\Authorization\Role;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class RolePermissionAdminCrudService
{
    // This builds the role page data for list and edit mode.
    public function rolePageData(?int $editRoleId = null, ?int $editPermissionId = null): array
    {
        try {
            // Step 1: load all roles with usage counts for the list table.
            $roles = Role::query()
                ->withCount(['users', 'permissions'])
                ->orderBy('name')
                ->get();

            // Step 2: load all permissions with usage counts for the permission table.
            $permissions = Permission::query()
                ->withCount(['roles', 'userOverrides'])
                ->orderBy('name')
                ->get();

            // Step 3: load one role when the page is opened in edit mode.
            $editingRole = $editRoleId
                ? Role::query()->with('permissions:id')->find($editRoleId)
                : null;

            // Step 4: load one permission when the page is opened in permission edit mode.
            $editingPermission = $editPermissionId
                ? Permission::query()->find($editPermissionId)
                : null;

            // Step 5: collect checked permission ids for the selected role.
            $editingRolePermissionIds = $editingRole
                ? $editingRole->permissions->pluck('id')->map(fn ($id) => (int) $id)->all()
                : [];

            return [
                'roles' => $roles,
                'permissions' => $permissions,
                'editingRole' => $editingRole,
                'editingPermission' => $editingPermission,
                'editingRolePermissionIds' => $editingRolePermissionIds,
            ];
        } catch (Throwable $exception) {
            Log::error('Failed to build role and permission page data.', ['edit_role_id' => $editRoleId, 'edit_permission_id' => $editPermissionId, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This creates a new role record.
    public function addRole(array $validated): int
    {
        try {
            // Step 1: build a unique slug from submitted data.
            $roleSlug = $this->resolveRoleSlug((string) ($validated['slug'] ?? ''), (string) $validated['name']);

            // Step 2: create the role row.
            $role = Role::query()->create([
                'name' => trim((string) $validated['name']),
                'slug' => $roleSlug,
            ]);

            return (int) $role->id;
        } catch (Throwable $exception) {
            Log::error('Failed to add role.', ['name' => $validated['name'] ?? null, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This updates an existing role record.
    public function updateRole(int $roleId, array $validated): void
    {
        try {
            // Step 1: load the role that should be updated.
            $role = Role::query()->find($roleId);

            if (! $role) {
                throw ValidationException::withMessages([
                    'role' => 'Role not found.',
                ]);
            }

            // Step 2: build a unique slug and save the updated fields.
            $roleSlug = $this->resolveRoleSlug((string) ($validated['slug'] ?? ''), (string) $validated['name'], $roleId);

            $role->fill([
                'name' => trim((string) $validated['name']),
                'slug' => $roleSlug,
            ])->save();
        } catch (Throwable $exception) {
            Log::error('Failed to update role.', ['role_id' => $roleId, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This deletes a role and clears its pivot relations first.
    public function deleteRole(int $roleId): void
    {
        try {
            // Step 1: load the role that should be removed.
            $role = Role::query()->find($roleId);

            if (! $role) {
                throw ValidationException::withMessages([
                    'role' => 'Role not found.',
                ]);
            }

            // Step 2: remove linked users and permissions from pivot tables.
            $role->users()->detach();
            $role->permissions()->detach();

            // Step 3: delete the role row itself.
            $role->delete();
        } catch (Throwable $exception) {
            Log::error('Failed to delete role.', ['role_id' => $roleId, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This creates a new permission record.
    public function createPermission(array $validated): int
    {
        try {
            // Step 1: build a unique permission slug from submitted data.
            $permissionSlug = $this->resolvePermissionSlug((string) ($validated['slug'] ?? ''), (string) $validated['name']);

            // Step 2: create the permission row.
            $permission = Permission::query()->create([
                'name' => trim((string) $validated['name']),
                'slug' => $permissionSlug,
            ]);

            return (int) $permission->id;
        } catch (Throwable $exception) {
            Log::error('Failed to create permission.', ['name' => $validated['name'] ?? null, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This updates an existing permission record.
    public function updatePermission(int $permissionId, array $validated): void
    {
        try {
            // Step 1: load the permission that should be updated.
            $permission = Permission::query()->find($permissionId);

            if (! $permission) {
                throw ValidationException::withMessages([
                    'permission' => 'Permission not found.',
                ]);
            }

            // Step 2: build a unique slug and save the updated fields.
            $permissionSlug = $this->resolvePermissionSlug((string) ($validated['slug'] ?? ''), (string) $validated['name'], $permissionId);

            $permission->fill([
                'name' => trim((string) $validated['name']),
                'slug' => $permissionSlug,
            ])->save();
        } catch (Throwable $exception) {
            Log::error('Failed to update permission.', ['permission_id' => $permissionId, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This deletes one permission and clears its linked rows first.
    public function deletePermission(int $permissionId): void
    {
        try {
            // Step 1: load the permission that should be removed.
            $permission = Permission::query()->find($permissionId);

            if (! $permission) {
                throw ValidationException::withMessages([
                    'permission' => 'Permission not found.',
                ]);
            }

            // Step 2: remove linked role and user override rows.
            $permission->roles()->detach();
            $permission->userOverrides()->delete();

            // Step 3: delete the permission row itself.
            $permission->delete();
        } catch (Throwable $exception) {
            Log::error('Failed to delete permission.', ['permission_id' => $permissionId, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This saves the checked permission list for one role.
    public function upsertPermissionsForRole(int $roleId, array $permissionIds): void
    {
        try {
            // Step 1: load the role that should receive permission changes.
            $role = Role::query()->find($roleId);

            if (! $role) {
                throw ValidationException::withMessages([
                    'role' => 'Role not found.',
                ]);
            }

            // Step 2: keep only valid permission ids before syncing the pivot table.
            $validPermissionIds = Permission::query()
                ->whereIn('id', collect($permissionIds)->map(fn ($id) => (int) $id)->filter(fn ($id) => $id > 0)->all())
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();

            // Step 3: sync will add checked rows and remove unchecked rows.
            $role->permissions()->sync($validPermissionIds);
        } catch (Throwable $exception) {
            Log::error('Failed to sync permissions for role.', ['role_id' => $roleId, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This creates a readable unique slug for the role.
    protected function resolveRoleSlug(string $slugInput, string $name, ?int $ignoreRoleId = null): string
    {
        try {
            $seedValue = trim($slugInput) !== '' ? $slugInput : $name;
            $baseSlug = Str::slug($seedValue, '_');
            $baseSlug = $baseSlug !== '' ? $baseSlug : 'role';
            $roleSlug = $baseSlug;
            $counter = 1;

            while (Role::query()
                ->where('slug', $roleSlug)
                ->when($ignoreRoleId, fn ($query) => $query->where('id', '!=', $ignoreRoleId))
                ->exists()) {
                $roleSlug = $baseSlug.'_'.$counter;
                $counter++;
            }

            return $roleSlug;
        } catch (Throwable $exception) {
            Log::error('Failed to resolve role slug.', ['name' => $name, 'ignore_role_id' => $ignoreRoleId, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This creates a readable unique slug for the permission.
    protected function resolvePermissionSlug(string $slugInput, string $name, ?int $ignorePermissionId = null): string
    {
        try {
            $seedValue = trim($slugInput) !== '' ? $slugInput : $name;
            $baseSlug = Str::slug($seedValue, '.');
            $baseSlug = $baseSlug !== '' ? $baseSlug : 'permission';
            $permissionSlug = $baseSlug;
            $counter = 1;

            while (Permission::query()
                ->where('slug', $permissionSlug)
                ->when($ignorePermissionId, fn ($query) => $query->where('id', '!=', $ignorePermissionId))
                ->exists()) {
                $permissionSlug = $baseSlug.'.'.$counter;
                $counter++;
            }

            return $permissionSlug;
        } catch (Throwable $exception) {
            Log::error('Failed to resolve permission slug.', ['name' => $name, 'ignore_permission_id' => $ignorePermissionId, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }
}

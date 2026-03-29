<?php

namespace App\Services\Authorization;

use App\Models\Authorization\Permission;
use App\Models\Authorization\Role;
use App\Models\Authorization\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class RolePermissionService
{
    // This returns role slugs for the current user and auto-assigns a default role when needed.
    public function roleSlugsForUser(?User $user): array
    {
        try {
            if (! $user) {
                return ['guest'];
            }

            $slugs = $user->roles()->pluck('roles.slug')->all();

            if ($slugs !== []) {
                return $slugs;
            }

            $this->assignDefaultRole($user);

            return $user->fresh()->roles()->pluck('roles.slug')->all();
        } catch (Throwable $exception) {
            Log::error('Failed to resolve role slugs.', ['user_id' => $user?->id, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This checks whether a user has the requested role.
    public function hasRole(?User $user, string $roleSlug): bool
    {
        try {
            return in_array($roleSlug, $this->roleSlugsForUser($user), true);
        } catch (Throwable $exception) {
            Log::error('Failed to check role.', ['user_id' => $user?->id, 'role' => $roleSlug, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This checks whether a user has the requested permission after role and override rules are applied.
    public function hasPermission(?User $user, string $permissionSlug): bool
    {
        try {
            // Step 1: load the active role slugs for this request.
            $roleSlugs = $this->roleSlugsForUser($user);

            // Step 2: allow guest permissions through the guest role matrix.
            if (! $user) {
                return Permission::query()
                    ->where('slug', $permissionSlug)
                    ->whereHas('roles', fn ($query) => $query->whereIn('roles.slug', $roleSlugs))
                    ->exists();
            }

            // Step 3: stop early when the user has a direct deny override.
            $userOverride = $user->permissionOverrides()
                ->whereHas('permission', fn ($query) => $query->where('slug', $permissionSlug))
                ->first();

            if ($userOverride?->grant_type === 'deny') {
                return false;
            }

            if ($userOverride?->grant_type === 'allow') {
                return true;
            }

            if (in_array('admin', $roleSlugs, true)) {
                return true;
            }

            return Permission::query()
                ->where('slug', $permissionSlug)
                ->whereHas('roles', fn ($query) => $query->whereIn('roles.slug', $roleSlugs))
                ->exists();
        } catch (Throwable $exception) {
            Log::error('Failed to check permission.', ['user_id' => $user?->id, 'permission' => $permissionSlug, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This returns the final permission slug list after role permissions and user overrides are merged.
    public function permissionSlugsForUser(?User $user): array
    {
        try {
            // Step 1: load the active role slugs for this request.
            $roleSlugs = $this->roleSlugsForUser($user);

            // Step 2: start with permissions coming from the current role matrix.
            $permissions = Permission::query()
                ->whereHas('roles', fn ($query) => $query->whereIn('roles.slug', $roleSlugs))
                ->pluck('slug')
                ->flip()
                ->map(fn () => true)
                ->all();

            // Step 3: guests do not have user-level overrides.
            if (! $user) {
                $result = array_keys($permissions);
                sort($result);

                return $result;
            }

            // Step 4: admins always receive the full permission list.
            if (in_array('admin', $roleSlugs, true)) {
                $permissions = Permission::query()
                    ->pluck('slug')
                    ->flip()
                    ->map(fn () => true)
                    ->all();
            }

            // Step 5: apply user-level allow and deny overrides after the role matrix.
            foreach ($user->permissionOverrides()->with('permission:id,slug')->get() as $override) {
                $permissionSlug = $override->permission?->slug;

                if (! $permissionSlug) {
                    continue;
                }

                if ($override->grant_type === 'deny') {
                    unset($permissions[$permissionSlug]);
                    continue;
                }

                $permissions[$permissionSlug] = true;
            }

            // Step 6: return one clean sorted list for the caller.
            $result = array_keys($permissions);
            sort($result);

            return $result;
        } catch (Throwable $exception) {
            Log::error('Failed to resolve permissions.', ['user_id' => $user?->id, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This assigns the default role based on the user type.
    public function assignDefaultRole(User $user): void
    {
        try {
            $roleSlug = match ($user->user_type) {
                'b2b' => 'b2b_user',
                'internal' => 'internal_user',
                'admin' => 'admin',
                'delegated_admin' => 'delegated_admin',
                default => 'b2c_customer',
            };

            $this->assignRole($user, $roleSlug);
        } catch (Throwable $exception) {
            Log::error('Failed to assign default role.', ['user_id' => $user->id, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This assigns a single role to the user and creates the role first when needed.
    public function assignRole(User $user, string $roleSlug): void
    {
        try {
            $role = Role::query()->firstOrCreate(
                ['slug' => $roleSlug],
                ['name' => Str::of($roleSlug)->replace('_', ' ')->title()->toString()],
            );

            $user->roles()->syncWithoutDetaching([$role->id]);
        } catch (Throwable $exception) {
            Log::error('Failed to assign role.', ['user_id' => $user->id, 'role' => $roleSlug, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This removes a role from the user when it exists.
    public function detachRole(User $user, string $roleSlug): void
    {
        try {
            $roleId = Role::query()->where('slug', $roleSlug)->value('id');

            if (! $roleId) {
                return;
            }

            $user->roles()->detach($roleId);
        } catch (Throwable $exception) {
            Log::error('Failed to detach role.', ['user_id' => $user->id, 'role' => $roleSlug, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }
}

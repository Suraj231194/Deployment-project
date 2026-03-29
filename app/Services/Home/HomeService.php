<?php

namespace App\Services\Home;

use App\Models\Authorization\User;
use App\Services\Authorization\RolePermissionService;
use Illuminate\Support\Facades\Log;
use Throwable;

class HomeService
{
    public function __construct(
        protected RolePermissionService $rolePermissionService,
    ) {
    }

    // This prepares the small amount of role data needed on the homepage.
    public function viewData(?User $user): array
    {
        try {
            return [
                'roleSlugs' => $this->rolePermissionService->roleSlugsForUser($user),
            ];
        } catch (Throwable $exception) {
            Log::error('Failed to build homepage data.', ['user_id' => $user?->id, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }
}

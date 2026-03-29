<?php

namespace App\Http\Middleware;

use App\Services\Authorization\RolePermissionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePermission
{
    protected RolePermissionService $rolePermissionService;

    public function __construct(RolePermissionService $rolePermissionService)
    {
        $this->rolePermissionService = $rolePermissionService;
    }

    // This checks if the current request can access the route through the permission matrix.
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        // Step 1: load the signed-in user.
        $currentUser = $request->user();

        // Step 2: stop when the user does not have the required permission.
        $hasPermission = $this->rolePermissionService->hasPermission($currentUser, $permission);

        if (! $hasPermission) {
            abort(403, 'You do not have permission for this action.');
        }

        // Step 3: continue the request when the permission check passes.
        $response = $next($request);

        return $response;
    }
}

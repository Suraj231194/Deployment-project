<?php

namespace App\Services\Authorization;

use App\Models\Authorization\B2bClientAssignment;
use App\Models\Authorization\DelegatedAdminScope;
use App\Models\Authorization\User;
use App\Models\Product\Product;
use App\Services\Pricing\PriceService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Throwable;

class DataVisibilityService
{
    public function __construct(
        protected RolePermissionService $rolePermissionService,
        protected PriceService $priceService,
    ) {
    }

    // This returns the allowed product visibility scopes for the current user.
    public function productScopes(?User $user): array
    {
        try {
            if (! $user) {
                return ['public'];
            }

            if ($this->isFullAdmin($user) || $this->isDelegatedAdmin($user)) {
                return ['public', 'b2c', 'b2b', 'internal'];
            }

            return match ($user->user_type) {
                'b2b' => ['public', 'b2b'],
                'internal' => ['public', 'internal'],
                default => ['public', 'b2c'],
            };
        } catch (Throwable $exception) {
            Log::error('Failed to resolve product scopes.', ['user_id' => $user?->id, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This builds the base visible product query using product relations and variant-price scope checks.
    public function visibleProductQuery(?User $user): Builder
    {
        try {
            // Business step: read the visible price ladder once so the storefront query only considers purchasable products.
            $allowedPriceTypes = $this->priceService->visiblePriceTypes($user);
            $genericPriceTypes = array_values(array_filter($allowedPriceTypes, fn ($type) => $type !== 'company_price'));

            return Product::query()
                ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
                ->leftJoin('subcategories', 'subcategories.id', '=', 'products.subcategory_id')
                ->leftJoin('product_image', 'product_image.id', '=', 'products.product_image_id')
                ->select([
                    'products.*',
                    'categories.name as category_name',
                    'categories.slug as category_slug',
                    'subcategories.name as subcategory_name',
                    'subcategories.slug as subcategory_slug',
                    'product_image.file_path as image_path',
                ])
                ->where('is_active', true) // product must be active
                ->whereIn('visibility_scope', $this->productScopes($user)) // it must match the user's visibility scopes
                ->whereHas('variants', function (Builder $variantQuery) use ($genericPriceTypes, $user): void {
                    $variantQuery->where('is_active', true)  // variant must be active
                        ->whereHas('prices', function (Builder $priceQuery) use ($genericPriceTypes, $user): void {
                            $priceQuery->where('is_active', true) // price must be active
                                ->where(function (Builder $visiblePriceQuery) use ($genericPriceTypes, $user): void {
                                    // Business step: generic price rows 
                                    if ($genericPriceTypes !== []) {
                                        $visiblePriceQuery->where(function (Builder $genericPriceQuery) use ($genericPriceTypes): void {
                                            $genericPriceQuery
                                                ->whereNull('company_id')
                                                ->whereIn('price_type', $genericPriceTypes);
                                        });
                                    }

                                    // Business step: company prices are visible to the logged-in B2B
                                    if ($user && $user->isB2b() && $user->company_id) {
                                        $visiblePriceQuery->orWhere(function (Builder $companyPriceQuery) use ($user): void {
                                            $companyPriceQuery
                                                ->where('price_type', 'company_price')
                                                ->where('company_id', $user->company_id);
                                        });
                                    }
                                });
                        });
                });
        } catch (Throwable $exception) {
            Log::error('Failed to build visible product query.', ['user_id' => $user?->id, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This checks whether the user can generate PI for other customers.
    public function canGeneratePiForOther(?User $user): bool
    {
        try {
            if (! $user) {
                return false;
            }

            if ($this->isFullAdmin($user)) {
                return true;
            }

            if ($user->isB2c()) {
                return false;
            }

            return $this->rolePermissionService->hasPermission($user, 'pi.generate.other.client');
        } catch (Throwable $exception) {
            Log::error('Failed to check PI permission.', ['user_id' => $user?->id, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This checks whether the user can access data for the given company.
    public function canAccessCompanyData(User $user, ?int $companyId): bool
    {
        try {
            if (! $companyId) {
                return true;
            }

            if ($this->isFullAdmin($user)) {
                return true;
            }

            if ($this->isDelegatedAdmin($user)) {
                return in_array($companyId, $this->delegatedAdminCompanyScopeIds($user), true);
            }

            if ($user->company_id === $companyId) {
                return true;
            }

            return in_array($companyId, $this->assignedClientCompanyIds($user), true);
        } catch (Throwable $exception) {
            Log::error('Failed to check company access.', ['user_id' => $user->id, 'company_id' => $companyId, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This returns assigned client company IDs for a B2B user.
    public function assignedClientCompanyIds(User $user): array
    {
        try {
            return B2bClientAssignment::query()
                ->where('b2b_user_id', $user->id)
                ->pluck('client_company_id')
                ->map(fn ($id) => (int) $id)
                ->all();
        } catch (Throwable $exception) {
            Log::error('Failed to load assigned client companies.', ['user_id' => $user->id, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This returns company IDs assigned to the delegated admin scope.
    public function delegatedAdminCompanyScopeIds(User $user): array
    {
        try {
            if (! $this->isDelegatedAdmin($user)) {
                return [];
            }

            return DelegatedAdminScope::query()
                ->where('delegated_admin_user_id', $user->id)
                ->where('scope_type', 'company')
                ->pluck('scope_value')
                ->map(fn ($scopeValue) => (int) $scopeValue)
                ->filter(fn ($scopeValue) => $scopeValue > 0)
                ->values()
                ->all();
        } catch (Throwable $exception) {
            Log::error('Failed to load delegated admin scopes.', ['user_id' => $user->id, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This checks whether the user is a full admin.
    protected function isFullAdmin(User $user): bool
    {
        try {
            return $this->rolePermissionService->hasRole($user, 'admin') || $user->user_type === 'admin';
        } catch (Throwable $exception) {
            Log::error('Failed to check full-admin state.', ['user_id' => $user->id, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This checks whether the user is a delegated admin.
    protected function isDelegatedAdmin(User $user): bool
    {
        try {
            return $this->rolePermissionService->hasRole($user, 'delegated_admin') || $user->user_type === 'delegated_admin';
        } catch (Throwable $exception) {
            Log::error('Failed to check delegated-admin state.', ['user_id' => $user->id, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

}

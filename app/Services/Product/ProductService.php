<?php

namespace App\Services\Product;

use App\Models\Authorization\User;
use App\Models\Order\OrderItem;
use App\Models\Product\Category;
use App\Models\Product\Product;
use App\Models\Product\ProductImage;
use App\Models\Product\ProductPrice;
use App\Models\Product\ProductTechnicalResource;
use App\Models\Product\ProductVariant;
use App\Models\Product\Subcategory;
use App\Models\Product\UserActivityLog;
use App\Models\Product\VariantAttribute;
use App\Services\Authorization\DataVisibilityService;
use App\Services\Pricing\PriceService;
use App\Services\Utility\FileHandlingService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ProductService
{
    public function __construct(  protected DataVisibilityService $dataVisibilityService,  protected PriceService $priceService,
        protected FileHandlingService $fileHandlingService ) {
    }

     // This returns the catalog page data expected by the public catalog view.
    public function getProductListToBeDisplayed(?User $user, array $filters = []): array
    {
        try {
            // Step 0: prepare the raw filter values and prebuilt lookup tables needed by the catalog flow.
            $catalogFilters = $this->prepareProductListFilters($filters); 

            // Step 1: load all visible products with price data.
               $visibleProducts = $this->dataVisibilityService->visibleProductQuery($user)
                ->orderBy('products.name')
                ->get()
                ->map(fn ($product) => $this->attachResolvedPriceData($product, $user))
                ->values(); 

            // Step 2: scope sidebar option counts to the current search term.
            $searchScopedProducts = $this->getProductsToBeDisplayedBySearch($visibleProducts, $catalogFilters['search']); 
           
           // Build the catalog sidebar data from the search-scoped products.
            $catalogOptions = $this->getAvailableProductFilters($searchScopedProducts); 

            // Step 3: apply all selected filters in a single pass.
            $filteredProducts = $searchScopedProducts
                ->filter(fn ($product): bool => $this->getProductsAfterApplyingFilters($product, $catalogFilters))
                ->values(); // Check category, subcategory, brand, and max-price filters together for each product.

            // Step 4: sort and paginate the final product list for the view.
            $sortedProducts = $this->sortProducts($filteredProducts, $catalogFilters['sort'])->values();

            // Step 5: paginate the final product list first so any extra catalog-card data is attached only to products visible on the current page.
            $paginatedProducts = $this->paginateProducts($sortedProducts, 15);

            // Step 6: attach compact card-level commerce details only to the paginated product set to keep catalog rendering efficient.
            $paginatedProducts->setCollection(
                $paginatedProducts->getCollection()
                    ->map(fn ($product) => $this->attachCatalogCardCommercialData($product, $user))
                    ->values()
            );

            return [
                'products' => $paginatedProducts, // Paginate the final product list for the catalog page.
                'catalogOptions' => $catalogOptions, // Return the sidebar filter metadata beside the paginated products.
            ]; // Return the complete catalog payload expected by the controller.
        } catch (Throwable $exception) {
            Log::error('Failed to build catalog listing data.', ['user_id' => $user?->id, 'filters' => $filters, 'error' => $exception->getMessage()]); // Record the failure with the current user and filter context.
            throw $exception; 
        }
    }

    // This attaches the resolved storefront price fields used by the catalog and detail pages.
    protected function attachResolvedPriceData(object $product, ?User $user): object
    {
        $price = $this->priceService->resolveProductPrice((int) $product->id, $user);

        // Step 1: keep the original saved base amount so the storefront can show the real MRP when needed.
        $product->visible_base_price = $price['base_amount'] ?? null;
        $product->visible_price = $price['amount'] ?? null;
        // Step 2: keep the resolved discount amount so the storefront can decide whether to show a discounted price state.
        $product->visible_discount_amount = $price['discount_amount'] ?? 0;
        $product->gst_rate = $price['gst_rate'] ?? 0;
        $product->tax_amount = $price['tax_amount'] ?? null;
        $product->price_with_gst = $price['price_after_gst'] ?? null;
        $product->visible_currency = $price['currency'] ?? null;
        $product->visible_price_type = $price['price_type'] ?? null;
        $product->visible_variant_id = $price['product_variant_id'] ?? null;
        $product->visible_variant_sku = $price['variant_sku'] ?? null;
        $product->visible_variant_name = $price['variant_name'] ?? null;
        // Step 3: keep the resolved minimum order quantity ready for storefront quantity messaging.
        $product->visible_min_order_quantity = $price['min_order_quantity'] ?? 1;
        // Step 4: keep the resolved maximum order quantity ready for compact storefront quantity guidance.
        $product->visible_max_order_quantity = $price['max_order_quantity'] ?? null;
        // Step 5: keep the lot size ready so storefront quantity pickers can stay aligned with selling rules.
        $product->visible_lot_size = $price['lot_size'] ?? 1;
        return $product;
    }

    // This attaches the compact bulk and quantity details needed by one catalog product card.
    protected function attachCatalogCardCommercialData(object $product, ?User $user): object
    {
        // Step 1: default the compact bulk summary to null so the card stays clean when no real tier applies.
        $product->catalog_bulk_summary = null;

        // Step 2: stop early when the catalog card does not have a visible sellable variant.
        if (! filled($product->visible_variant_id ?? null)) {
            return $product;
        }

        // Step 3: read the shopper-visible bulk ladder from the shared pricing service.
        $bulkPriceTiers = $this->priceService->listBulkPriceTiers((int) $product->visible_variant_id, $user);

        // Step 4: keep the card clean when only the standard price row exists and there is no real bulk pricing benefit to show.
        if ($bulkPriceTiers->count() <= 1) {
            return $product;
        }

        // Step 5: show only the first real qualifying bulk row because catalog cards need one short commercial hint, not the full ladder.
        $firstBulkTier = $bulkPriceTiers->slice(1)->first();

        if (! $firstBulkTier) {
            return $product;
        }

        $product->catalog_bulk_summary = [
            'label' => $firstBulkTier['label'] ?? null,
            'discount' => $firstBulkTier['discount'] ?? null,
            'price' => $firstBulkTier['price'] ?? null,
            'min' => $firstBulkTier['min'] ?? null,
        ];

        return $product;
    }

    // This normalizes the incoming catalog filters into one readable structure.
    protected function prepareProductListFilters(array $filters): array
    {
        // Read the first available search value and trim extra whitespace.
        $search = trim((string) ($filters['search'] ?? $filters['search_text'] ?? $filters['search_value'] ?? '')); 
        
        // remove empty and duplicate by case for category filters.
        $selectedCategories = $this->getCleanFilterValues(
            $filters['category_name'] ?? ($filters['category'] ?? ($filters['category_id'] ?? [])),
        ); 

        // same way remove empty and duplicate by case for subcategory filters.
        $selectedApplications = $this->getCleanFilterValues(
            $filters['application_name'] ?? ($filters['subcategory_name'] ?? ($filters['subcategory'] ?? ($filters['subcategory_id'] ?? []))),
        ); 

         // same way remove empty and duplicate by case for brand filters.
        $selectedBrands = $this->getCleanFilterValues($filters['brand_name'] ?? []); 
         // Keep only numeric max-price values because invalid input should be ignored.
        $selectedMaxPrice = is_numeric($filters['max_price'] ?? null) ? (float) $filters['max_price'] : null;
        // Default to "relevant" sorting when no sort parameter is provided.
        $sort = trim((string) ($filters['sort'] ?? 'relevant')); 

        return [
            'search' => $search,
            'sort' => $sort,
            'maxPrice' => $selectedMaxPrice,
            'categoryLookup' => $this->prepareSelectedValuesMap($selectedCategories),
            'applicationLookup' => $this->prepareSelectedValuesMap($selectedApplications),
            'brandLookup' => $this->prepareSelectedValuesMap($selectedBrands),
        ]; 
    }

    // This keeps catalog array-style filters normalized and trimmed.
    protected function getCleanFilterValues(mixed $value): Collection
    {
        // convert to array 
        $values = is_array($value) ? $value : [$value]; 

        // Remove empty values and case-insensitive duplicates so later checks do less work.
        return collect($values)
            ->map(fn ($item): string => trim((string) $item))
            ->filter()
            ->unique(fn (string $item): string => Str::lower($item))
            ->values(); 
    }

    // This turns selected filter values into a fast case-insensitive lookup map(key-value).
    protected function prepareSelectedValuesMap(Collection $selectedValues): array
    {
        // Build lookup table so product filtering avoids repeated collection scans.
        return $selectedValues
            ->mapWithKeys(fn (string $value): array => [Str::lower(trim($value)) => true])
            ->all(); 
    }

    // This returns only products matching the free-text catalog search.
    protected function getProductsToBeDisplayedBySearch(Collection $products, string $search): Collection
    {
        $searchText = Str::lower(trim($search)); 
        if ($searchText === '') {
            // Return every product when no search is active.
            return $products->values(); 
        }

        return $products
            ->filter(fn ($product): bool => $this->isProductMatchingSearchText($product, $searchText))
            ->values(); // Keep only the products whose searchable text contains the normalized search term.
    }

    // This checks whether one catalog product matches the current free-text search.
    protected function isProductMatchingSearchText(object $product, string $searchText): bool
    {
        // Walk through each searchable field so the matching logic stays explicit for new developers.
        foreach ([
            $product->name ?? null,
            $product->sku ?? null,
            $product->description ?? null,
            $product->brand ?? null,
            $product->category_name ?? null,
            $product->subcategory_name ?? null,
            $product->visible_variant_sku ?? null,
        ] as $productFields) { 
            $productFields = Str::lower(trim((string) $productFields)); // Normalize the current field to make the search case-insensitive.

            if ($productFields !== '' && Str::contains($productFields, $searchText)) {
                // Accept the product as soon as one searchable field contains the search term.
                return true; 
            }
        }
        // Reject the product when none of the searchable fields contain the search text.
        return false; 
    }

    // This builds the sidebar filter counts and price bounds for the catalog page.
    protected function getAvailableProductFilters(Collection $products): array
    {
        [$minPrice, $maxPrice] = $this->getProductPriceRange($products);

        return [
            'categoryOptions' => $this->getFilterWiseProductCount($products, 'category_name'),
            'applicationOptions' => $this->getFilterWiseProductCount($products, 'subcategory_name'),
            'brandOptions' => $this->getFilterWiseProductCount($products, 'brand'),
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
        ];
    }

    // This counts visible product labels for catalog sidebar filters.
    protected function getFilterWiseProductCount(Collection $products, string $field): Collection
    {
        return $products
            ->map(fn ($product): string => trim((string) data_get($product, $field)))
            ->filter()
            ->countBy()
            ->sortKeys();
    }

    // This computes safe price-range bounds for the catalog slider.
    protected function getProductPriceRange(Collection $products): array
    {
        $prices = $products
            ->pluck('visible_price')
            ->filter(fn ($price) => $price !== null)
            ->map(fn ($price): float => (float) $price)
            ->values();

        if ($prices->isEmpty()) {
            return [0, 1000];
        }

        return [
            (int) floor($prices->min()),
            (int) ceil($prices->max()),
        ];
    }

    // This checks whether one product satisfies all selected sidebar filters.
    protected function getProductsAfterApplyingFilters(object $product, array $catalogFilters): bool
    {
        // Reject the product as soon as its category does not match the selected categories.
        if (! $this->doesProductMatchFilter([
            $product->category_name ?? null,
            $product->category_slug ?? null,
            $product->category_id ?? null,
        ], $catalogFilters['categoryLookup'])) {
            return false; 
        }

         // Reject the product as soon as its subcategory_name does not match the selected subcategory_name.
        if (! $this->doesProductMatchFilter([
            $product->subcategory_name ?? null,
            $product->subcategory_slug ?? null,
            $product->subcategory_id ?? null,
        ], $catalogFilters['applicationLookup'])) {
            return false;
        }

        // Reject the product as soon as its brand does not match the selected brands.
        if (! $this->doesProductMatchFilter([
            $product->brand ?? null,
        ], $catalogFilters['brandLookup'])) {
            return false; 
        }

        // Read the visible price once before comparing it to the selected max price.
        if ($catalogFilters['maxPrice'] !== null) {
            $price = $product->visible_price; 
           // Reject products without a visible price or above the selected price ceiling.
            if ($price === null || (float) $price > $catalogFilters['maxPrice']) {
                return false; 
            }
        }

        return true; // Keep the product when it passes every selected sidebar filter.
    }

    // This checks whether any candidate value matches the selected filter lookup.
    protected function doesProductMatchFilter(array $candidates, array $selectedLookup): bool
    {
        if ($selectedLookup === []) {
            return true; // Treat an empty selection as "no filter" so every product can pass this check.
        }

        foreach ($candidates as $candidate) {
            $normalizedCandidate = Str::lower(trim((string) $candidate)); // Normalize the candidate value before checking the lookup map.

            if ($normalizedCandidate !== '' && isset($selectedLookup[$normalizedCandidate])) {
                return true; // Stop immediately when one candidate value matches the selected lookup.
            }
        }

        return false; // Reject the product when none of the candidate values match the selected lookup.
    }

    // This sorts catalog products according to the selected storefront option.
    protected function sortProducts(Collection $products, string $sort): Collection
    {
        return match ($sort) {
            'name_az' => $products->sortBy(fn ($product) => Str::lower((string) ($product->name ?? '')), SORT_NATURAL),
            'price_low' => $products->sortBy(fn ($product) => $product->visible_price ?? PHP_FLOAT_MAX),
            'price_high' => $products->sortByDesc(fn ($product) => $product->visible_price ?? -1),
            default => $products->sortBy(fn ($product) => Str::lower((string) ($product->name ?? '')), SORT_NATURAL),
        };
    }

    // This paginates an in-memory product collection while preserving the query string.
    protected function paginateProducts(Collection $products, int $perPage): LengthAwarePaginator
    {
        $currentPage = max(1, (int) LengthAwarePaginator::resolveCurrentPage('page'));
        $items = $products->forPage($currentPage, $perPage)->values();
        $paginator = new LengthAwarePaginator(
            $items,
            $products->count(),
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ],
        );

        $paginator->appends(request()->query());

        return $paginator;
    }

    ////////////////////////////////// FOR HOME PAGE /////////////////////////////////////
    // This returns product categories for home and other pages.
    public function categories(): Collection
    {
        try {
            return Category::query()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get()
                ->values();
        } catch (Throwable $exception) {
            Log::error('Failed to load product categories.', ['error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This returns only the configured home page categories in config order.
    public function GetConfiguredCategories(): Collection
    {
        try {
            $configuredSlugs = collect(config('common.home_page_category_slugs', []))
                ->filter(fn (mixed $slug): bool => is_string($slug) && trim($slug) !== '')
                ->map(fn (string $slug): string => trim($slug))
                ->unique()
                ->values();

            if ($configuredSlugs->isEmpty()) {
                return Category::query()
                    ->where('IsDisplayedOnHomePage', true)
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->get()
                    ->values();
            }

            $categories = Category::query()
                ->where('IsDisplayedOnHomePage', true)
                ->whereIn('slug', $configuredSlugs->all())
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get()
                ->keyBy('slug');

            return $configuredSlugs
                ->map(fn (string $slug) => $categories->get($slug))
                ->filter()
                ->values();
        } catch (Throwable $exception) {
            Log::error('Failed to load configured product categories.', ['error' => $exception->getMessage()]);
            throw $exception;
        }
    }
    

    /////////////////////////////// FOR PRODUCT DETAIL PAGE //////////////////////////////////
    // This loads one product only when it is visible to the current user.
    public function getAccessibleProductByProductId(?User $user, int $productId): ?object
    {
        try {
            // Load the visible product row using the same storefront visibility rules as the catalog page.
            $product = $this->dataVisibilityService->visibleProductQuery($user)
                ->where('products.id', $productId)
                ->first();

            // Return nothing when the product is outside the current user's visibility scope.
            if (! $product) {
                return null;
            }

            // Step 1: attach the visible price fields needed by the product detail page.
            $product = $this->attachResolvedPriceData($product, $user);

            // Step 2: attach the visible variant technical specs so the detail page can read them directly from database content.
            $product = $this->attachVisibleVariantTechnicalSpecifications($product);

            // Step 3: attach the active technical download files so the detail page can render real product documents.
            $product = $this->attachActiveTechnicalResources($product);

            // Step 4: attach the visible bulk pricing ladder so the product detail page uses live database pricing slabs.
            return $this->attachVisibleVariantBulkPriceTiers($product, $user);
        } catch (Throwable $exception) {
            Log::error('Failed to load visible product.', ['product_id' => $productId, 'user_id' => $user?->id, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This downloads one active technical resource after confirming the product is visible to the current viewer.
    public function downloadTechnicalResourceForViewer(?User $user, int $productId, int $resourceId): BinaryFileResponse
    {
        try {
            // Step 1: load the visible product using the same detail-page rules so hidden products cannot expose files.
            $product = $this->getAccessibleProductByProductId($user, $productId);

            if (! $product) {
                throw new NotFoundHttpException('Technical resource not found.');
            }

            // Step 2: find the requested file inside the already-filtered product resource list.
            $technicalResource = collect($product->technical_resources ?? [])
                ->first(fn ($resource): bool => (int) ($resource->id ?? 0) === $resourceId);

            if (! $technicalResource) {
                throw new NotFoundHttpException('Technical resource not found.');
            }

            $storedFilePath = trim((string) ($technicalResource->stored_file_path ?? ''));

            // Step 3: stop the download when the saved public document path no longer exists on disk.
            if ($storedFilePath === '' || ! $this->fileHandlingService->fileExists($storedFilePath)) {
                throw new NotFoundHttpException('Technical resource file is not available.');
            }

            // Step 4: return the file as a normal download so the browser saves the original uploaded document name.
            return $this->fileHandlingService->downloadPublicFile(
                $storedFilePath,
                (string) ($technicalResource->original_file_name ?? basename($storedFilePath)),
            );
        } catch (Throwable $exception) {
            Log::error('Failed to download product technical resource.', [
                'product_id' => $productId,
                'resource_id' => $resourceId,
                'user_id' => $user?->id,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    // This attaches the technical specs for the currently visible product variant.
    protected function attachVisibleVariantTechnicalSpecifications(object $product): object
    {
        // Step 1: keep the detail page predictable by defaulting to an empty specs list.
        $product->technical_specification_json = [];

        // Step 2: stop early when no visible variant is available for the current product view.
        if (! filled($product->visible_variant_id ?? null)) {
            return $product;
        }

        // Step 3: load only the visible variant specs needed by the product detail page.
        $visibleVariant = ProductVariant::query()
            ->select(['id', 'technical_specification_json'])
            ->find((int) $product->visible_variant_id);

        // Step 4: attach the saved variant specs directly to the product payload for the Blade view.
        $product->technical_specification_json = $visibleVariant?->technical_specification_json ?? [];

        return $product;
    }

    // This attaches active product documents so the detail page can show real downloadable technical resources.
    protected function attachActiveTechnicalResources(object $product): object
    {
        // Step 1: default the detail payload to an empty document list when no files exist yet.
        $product->technical_resources = collect();

        $visibleVariantId = filled($product->visible_variant_id ?? null)
            ? (int) $product->visible_variant_id
            : null;

        // Step 2: load product-level files plus the current visible variant files in one simple query.
        $product->technical_resources = ProductTechnicalResource::query()
            ->select([
                'id',
                'product_id',
                'product_variant_id',
                'title',
                'resource_type',
                'description',
                'stored_file_path',
                'original_file_name',
                'mime_type',
                'file_size',
                'sort_order',
            ])
            ->where('product_id', (int) $product->id)
            ->where('is_active', true)
            ->where(function ($builder) use ($visibleVariantId): void {
                // Step 3: always keep product-level files available for every buyer who can view the product.
                $builder->whereNull('product_variant_id');

                // Step 4: include variant-level files only for the variant currently visible to the buyer.
                if ($visibleVariantId !== null) {
                    $builder->orWhere('product_variant_id', $visibleVariantId);
                }
            })
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get()
            ->values();

        return $product;
    }

    // This attaches the live bulk pricing ladder for the currently visible variant.
    protected function attachVisibleVariantBulkPriceTiers(object $product, ?User $user): object
    {
        // Step 1: default the detail page ladder to an empty collection when no visible variant exists.
        $product->bulk_price_tiers = collect();

        // Step 2: stop early when the current product view has no visible sellable variant.
        if (! filled($product->visible_variant_id ?? null)) {
            return $product;
        }

        // Step 3: read the database-driven pricing ladder for the visible variant from the shared pricing service.
        $product->bulk_price_tiers = $this->priceService->listBulkPriceTiers((int) $product->visible_variant_id, $user);

        return $product;
    }

    // This returns the top frequently bought together products for one product.
    public function frequentlyBoughtTogetherProducts(int $productId, ?User $user): Collection
    {
        try {
            // Step 1: read the configured top product limit from the configuration.
            $limit = max(1, (int) config('common.frequently_bought_together_limit', 4));

            // Step 2: load the current product because category and subcategory are used for fallback.
            $currentProduct = Product::query()
                ->select(['id', 'category_id', 'subcategory_id'])
                ->find($productId);

            // Step 3: return an empty collection when the current product does not exist.
            if (! $currentProduct) {
                return collect();
            }

            // Step 4: start one ordered list that will hold the final ranked related product ids.
            $selectedProductIds = collect();

            // Step 5: count the top related products directly in the database.
            $topProductFrequencyMap = OrderItem::query()
                ->selectRaw('order_items.product_id, COUNT(*) as frequency_count')
                ->whereIn('order_items.order_id', function ($builder) use ($productId): void {
                    $builder->select('order_items.order_id')
                        ->from('order_items')
                        ->join('orders', 'orders.id', '=', 'order_items.order_id')
                        ->where('order_items.product_id', $productId)
                        ->whereIn('orders.status', ['submitted', 'approved']);
                })
                ->whereNotNull('order_items.product_id')
                ->where('order_items.product_id', '!=', $productId)
                ->groupBy('order_items.product_id')
                ->orderByDesc('frequency_count')
                ->limit($limit)
                ->pluck('frequency_count', 'order_items.product_id');

            // Step 6: keep the ranked frequently-bought ids first in the final ordered list.
            $selectedProductIds = $selectedProductIds->concat(
                $topProductFrequencyMap
                    ->keys()
                    ->map(fn ($relatedProductId) => (int) $relatedProductId)
                    ->values()
            );

            // Step 7: fill the remaining slots from the same subcategory when needed(in case desired no of frequently bought together products is not met).
            if ($selectedProductIds->count() < $limit && $currentProduct->subcategory_id) {
                $remainingCount = $limit - $selectedProductIds->count();

                $sameSubcategoryProductIds = Product::query()
                    ->where('subcategory_id', $currentProduct->subcategory_id)
                    ->where('id', '!=', $productId)
                    ->whereNotIn('id', $selectedProductIds->all())
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->limit($remainingCount)
                    ->pluck('id')
                    ->map(fn ($relatedProductId) => (int) $relatedProductId);

                $selectedProductIds = $selectedProductIds->concat($sameSubcategoryProductIds);
            }

            // Step 8: fill the remaining slots from the same category when needed.
            if ($selectedProductIds->count() < $limit && $currentProduct->category_id) {
                $remainingCount = $limit - $selectedProductIds->count();

                $sameCategoryProductIds = Product::query()
                    ->where('category_id', $currentProduct->category_id)
                    ->where('id', '!=', $productId)
                    ->whereNotIn('id', $selectedProductIds->all())
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->limit($remainingCount)
                    ->pluck('id')
                    ->map(fn ($relatedProductId) => (int) $relatedProductId);

                $selectedProductIds = $selectedProductIds->concat($sameCategoryProductIds);
            }

            // Step 9: keep the final ids unique and stop at the configured limit.
            $topProductIds = $selectedProductIds
                ->unique()
                ->take($limit)
                ->values();

            // Step 10: return an empty collection when no related products remain after fallback.
            if ($topProductIds->isEmpty()) {
                return collect();
            }

            // Step 11: load the related product rows with common relations using Eloquent.
            $products = Product::query()
                ->with([
                    'category:id,name',
                    'subcategory:id,name',
                    'primaryImage:id,file_path',
                ])
                ->whereIn('id', $topProductIds->all())
                ->where('is_active', true)
                ->get();

            // Step 12: key the loaded products by id so ranked lookup stays simple.
            $products = $products->keyBy('id');

            // Step 13: rebuild the final list in ranked order and attach the current visible price fields.
            return $topProductIds
                ->map(function (int $relatedProductId) use ($products, $topProductFrequencyMap, $user) {
                    $product = $products->get($relatedProductId);

                    if (! $product) {
                        return null;
                    }

                    $price = $this->priceService->resolveProductPrice($relatedProductId, $user);

                    if (! $price) {
                        return null;
                    }

                    $product->frequency_count = (int) ($topProductFrequencyMap[$relatedProductId] ?? 0);
                    $product->visible_price = $price['amount'] ?? null;
                    $product->gst_rate = $price['gst_rate'] ?? 0;
                    $product->tax_amount = $price['tax_amount'] ?? null;
                    $product->price_with_gst = $price['price_after_gst'] ?? null;
                    $product->visible_currency = $price['currency'] ?? null;
                    $product->visible_price_type = $price['price_type'] ?? null;
                    $product->visible_variant_id = $price['product_variant_id'] ?? null;
                    $product->visible_variant_sku = $price['variant_sku'] ?? null;
                    $product->visible_variant_name = $price['variant_name'] ?? null;
                    $product->visible_min_order_quantity = $price['min_order_quantity'] ?? 1;
                    $product->visible_max_order_quantity = $price['max_order_quantity'] ?? null;
                    $product->visible_lot_size = $price['lot_size'] ?? 1;
                    return $product;
                })
                ->filter()
                ->values();
        } catch (Throwable $exception) {
            Log::error('Failed to load frequently bought together products.', ['product_id' => $productId, 'user_id' => $user?->id, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }


    // This stores one activity row for guest and logged-in users in the same table.
    public function logUserActivity(?User $user, string $sessionId, string $path, string $activityType, array $payload = []): void
    {
        try {
            // Step 1: save user details when a user is logged in, otherwise keep guest values.
            UserActivityLog::query()->create([
                'session_id' => $sessionId,
                'user_id' => $user?->id,
                'user_type' => $user?->user_type ?: 'guest',
                'user_name' => $user?->name,
                'user_email' => $user?->email,
                'activity_type' => $activityType,
                'path' => $path,
                'payload' => $payload,
                'created_at' => now(),
            ]);
        } catch (Throwable $exception) {
            Log::error('Failed to log user product activity.', ['session_id' => $sessionId, 'user_id' => $user?->id, 'path' => $path, 'activity_type' => $activityType, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

}

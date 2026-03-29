<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product\ProductVariant;
use App\Services\Product\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class ProductController extends Controller
{
    // This renders the public product listing page.
    public function index(Request $request, ProductService $productService): View
    {
        try {
            // Step 1: read the first available search input key.
            $search = null;
            foreach (['search_text', 'search_value', 'search'] as $searchKey) {
                if ($request->filled($searchKey)) {
                    $search = trim((string) $request->input($searchKey));
                    break;
                }
            }

            Log::info('Product search initiated', ['search' => $search]);

            // Step 2: normalize the current storefront catalog filters.
            $catalogFilters = $request->query();
            $catalogFilters['search'] = $search;

            // Step 3: load the catalog products and sidebar options for the current user.
            $user = $request->user();
            $catalogData = $productService->getProductListToBeDisplayed($user, $catalogFilters);

            // Step 4: track user browsing activity.
            $productService->logUserActivity($user, $request->session()->getId(), $request->path(), 'product_browse', [
                'search' => $search,
                'filters' => [
                    'category_name' => $request->input('category_name', []),
                    'application_name' => $request->input('application_name', $request->input('subcategory_name', [])),
                    'brand_name' => $request->input('brand_name', []),
                    'category' => $request->input('category', $request->input('category_id')),
                    'subcategory' => $request->input('subcategory', $request->input('subcategory_id')),
                    'max_price' => $request->input('max_price'),
                    'sort' => $request->input('sort', 'relevant'),
                ],
            ]);

            Log::info('productController.index Product search results Is:', [$catalogData['products']]);

            return view('product.index', [
                'products' => $catalogData['products'],
                'catalogOptions' => $catalogData['catalogOptions'],
            ]);
        } catch (Throwable $exception) {
            Log::error('Failed to load product index.', ['error' => $exception->getMessage()]);

            return $this->viewWithError('product.index', [
                'products' => new LengthAwarePaginator([], 0, 15),
                'catalogOptions' => [
                    'categoryOptions' => collect(),
                    'applicationOptions' => collect(),
                    'brandOptions' => collect(),
                    'minPrice' => 150,
                    'maxPrice' => 2500,
                ],
            ], $exception, 'Unable to load products right now.');
        }
    }

    // This renders the public product details page.
    public function productDetails(int $productId, Request $request, ProductService $productService): View
    {
        try {
            // Step 1: load the visible product details for the user.
            $user = $request->user();
            $product = $productService->getAccessibleProductByProductId($user, $productId);

            abort_if(! $product, 404);

            $stockStatus = 'Out of Stock';
            if ($product->visible_variant_id) {
                $visibleVariant = ProductVariant::query()
                    ->select(['stock_quantity', 'min_order_quantity'])
                    ->find($product->visible_variant_id);

                if ($visibleVariant) {
                    if ((int) $visibleVariant->stock_quantity > ((int) $visibleVariant->min_order_quantity * 10)) {
                        $stockStatus = 'Limited Availability';
                    } elseif ((int) $visibleVariant->stock_quantity >= (int) $visibleVariant->min_order_quantity) {
                        $stockStatus = 'In Stock';
                    }
                }
            }

            $product->stock_status = $stockStatus;
            $relatedProducts = $productService->frequentlyBoughtTogetherProducts($productId, $user);

            // Step 2: track user product view activity.
            $productService->logUserActivity($user, $request->session()->getId(), $request->path(), 'product_view', [
                'product_id' => $productId,
            ]);

            Log::info('productController.productDetails Product details:', [$product]);

            return view('product.detail', [
                'id' => $productId,
                'product' => $product,
                'related_products' => $relatedProducts,
                'gst_rate' => (float) ($product->gst_rate ?? 0),
                'tax_amount' => $product->tax_amount ?? null,
                'price_with_gst' => $product->price_with_gst ?? null,
            ]);
        } catch (Throwable $exception) {
            Log::error('Failed to load product details.', ['product_id' => $productId, 'error' => $exception->getMessage()]);
            return $this->viewWithError('product.detail', ['id' => $productId], $exception, 'Unable to load product details.');
        }
    }

    // This downloads one product document after checking that the current viewer can access the product.
    public function downloadTechnicalResource(int $productId, int $resourceId, Request $request, ProductService $productService): BinaryFileResponse|RedirectResponse
    {
        try {
            // Step 1: ask the product service to validate visibility and stream the requested document.
            return $productService->downloadTechnicalResourceForViewer($request->user(), $productId, $resourceId);
        } catch (Throwable $exception) {
            Log::error('Failed to download product technical resource.', [
                'product_id' => $productId,
                'resource_id' => $resourceId,
                'error' => $exception->getMessage(),
            ]);

            // Step 2: return the shopper to the product page with a simple business-friendly error.
            return $this->redirectBackWithError($exception, 'Unable to download the product document right now.');
        }
    }
}

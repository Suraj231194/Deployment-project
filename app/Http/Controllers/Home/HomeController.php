<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Services\Product\ProductService;
use App\Services\Home\HomeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class HomeController extends Controller
{
    public function index(Request $request, HomeService $homeService, ProductService $productService): View
    {
        try {
            // Step 1: load the product categories used on the home page.
            $productCategories = $productService->GetConfiguredCategories();
            $heroSlides = $this->getHomeHeroSlides();

            // Step 2: merge home page data with the category list.
            return view('home', array_merge(
                $homeService->viewData($request->user()),
                [
                    'productCategories' => $productCategories,
                    'heroSlides' => $heroSlides,
                ],
            ));
        } catch (Throwable $exception) {
            Log::error('Failed to load home page.', ['error' => $exception->getMessage()]);

            return $this->viewWithError('home', [
                'roleSlugs' => [],
                'productCategories' => collect(),
                'heroSlides' => $this->getHomeHeroSlides(),
            ], $exception, 'Unable to load the home page.');
        }
    }



    // This reads the configured home hero slides as configured for the view.
    protected function getHomeHeroSlides(): array
    {
        return collect(config('common.home_hero_slides', []))
            ->filter(fn (mixed $slide): bool => is_array($slide))
            ->map(function (array $slide): array {
                $imagePath = trim((string) ($slide['image'] ?? ''));

                return [
                    'tag' => trim((string) ($slide['tag'] ?? '')),
                    'title' => trim((string) ($slide['title'] ?? '')),
                    'copy' => trim((string) ($slide['copy'] ?? '')),
                    'image' => $imagePath,
                ];
            })
            ->filter(fn (array $slide): bool => $slide['tag'] !== '' && $slide['title'] !== '' && $slide['copy'] !== '' && $slide['image'] !== '')
            ->values()
            ->all();
    }
}

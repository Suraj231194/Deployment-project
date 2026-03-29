<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name', 'description', 'application', 'slug', 'default_image_path', 'IsDisplayedOnHomePage', 'gst_rate', 'sort_order'];

    protected function casts(): array
    {
        return [
            'IsDisplayedOnHomePage' => 'boolean',
            'gst_rate' => 'decimal:2',
            'sort_order' => 'integer',
        ];
    }

    // This loads all subcategories under the category.
    public function subcategories(): HasMany
    {
        return $this->hasMany(Subcategory::class);
    }

    // This loads all products under the category.
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}

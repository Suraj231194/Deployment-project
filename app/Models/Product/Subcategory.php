<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subcategory extends Model
{
    protected $fillable = ['category_id', 'name', 'slug', 'default_image_path', 'sort_order'];

    protected function casts(): array
    {
        return [
            'category_id' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    // This links the subcategory to its parent category.
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // This loads products assigned to the subcategory.
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}

<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductSpecification extends Model
{
    protected $fillable = ['specs'];

    protected function casts(): array
    {
        return [
            'specs' => 'array',
        ];
    }

    // This loads products using the specification row.
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'product_specifications_id');
    }
}

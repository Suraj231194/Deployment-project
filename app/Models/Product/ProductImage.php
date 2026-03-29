<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    protected $table = 'product_image';

    protected $fillable = ['product_id', 'file_path', 'is_primary', 'sort_order'];

    protected function casts(): array
    {
        return [
            'product_id' => 'integer',
            'is_primary' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    // This links the image to its product.
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

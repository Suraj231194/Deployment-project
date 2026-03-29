<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Pricing\ProductBulkPrice;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'variant_name',
        'technical_specification_json',
        'min_order_quantity',
        'max_order_quantity',
        'b2c_min_order_quantity',
        'b2c_max_order_quantity',
        'b2b_min_order_quantity',
        'b2b_max_order_quantity',
        'lot_size',
        'model_number',
        'catalog_number',
        'stock_quantity',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'product_id' => 'integer',
            'technical_specification_json' => 'array',
            'min_order_quantity' => 'integer',
            'max_order_quantity' => 'integer',
            'b2c_min_order_quantity' => 'integer',
            'b2c_max_order_quantity' => 'integer',
            'b2b_min_order_quantity' => 'integer',
            'b2b_max_order_quantity' => 'integer',
            'lot_size' => 'integer',
            'stock_quantity' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    // This links the variant to its parent product.
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // This loads all prices for the variant.
    public function prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class, 'product_variant_id');
    }

    // This loads all bulk pricing slabs linked to the variant.
    public function bulkPrices(): HasMany
    {
        return $this->hasMany(ProductBulkPrice::class, 'product_variant_id');
    }

    // This loads technical files that are specific to this variant configuration.
    public function technicalResources(): HasMany
    {
        return $this->hasMany(ProductTechnicalResource::class, 'product_variant_id');
    }

    // This loads legacy attribute rows for the variant.
    public function attributes(): HasMany
    {
        return $this->hasMany(VariantAttribute::class, 'product_variant_id');
    }
}

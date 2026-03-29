<?php

namespace App\Models\Product;

use App\Models\Proforma\ProformaInvoiceItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'subcategory_id',
        'product_specifications_id',
        'slug',
        'base_sku',
        'is_published',
        'product_image_id',
        'sku',
        'name',
        'brand',
        'description',
        'badges',
        'product_overview',
        'gst_rate',
        'visibility_scope',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'category_id' => 'integer',
            'subcategory_id' => 'integer',
            'product_specifications_id' => 'integer',
            'product_image_id' => 'integer',
            'gst_rate' => 'decimal:2',
            'is_published' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    // This links the product to its category.
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // This links the product to its subcategory.
    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    // This links the product to its specification record.
    public function specification(): BelongsTo
    {
        return $this->belongsTo(ProductSpecification::class, 'product_specifications_id');
    }

    // This links the product to the selected primary image row.
    public function primaryImage(): BelongsTo
    {
        return $this->belongsTo(ProductImage::class, 'product_image_id');
    }

    // This loads all images for the product.
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    // This loads all variants for the product.
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    // This loads all technical download files linked to the product.
    public function technicalResources(): HasMany
    {
        return $this->hasMany(ProductTechnicalResource::class);
    }

    // This loads the oldest variant, used as the default variant.
    public function defaultVariant(): HasOne
    {
        return $this->hasOne(ProductVariant::class)->oldestOfMany();
    }

    // This loads PI line items that reference the product.
    public function proformaItems(): HasMany
    {
        return $this->hasMany(ProformaInvoiceItem::class);
    }
}

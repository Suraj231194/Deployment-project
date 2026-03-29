<?php

namespace App\Models\Product;

use App\Models\Authorization\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPrice extends Model
{
    protected $fillable = ['product_variant_id', 'price_type', 'company_id', 'amount', 'DiscountType', 'Discount', 'gst_rate', 'tax_amount', 'price_after_gst', 'currency', 'is_active'];

    protected function casts(): array
    {
        return [
            'product_variant_id' => 'integer',
            'company_id' => 'integer',
            'amount' => 'decimal:2',
            'Discount' => 'decimal:2',
            'gst_rate' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'price_after_gst' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    // This links the price row to its variant.
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    // This links the price row to a company when it is company-specific.
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}

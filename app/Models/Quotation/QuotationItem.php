<?php

namespace App\Models\Quotation;

use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationItem extends Model
{
    protected $fillable = [
        'quotation_id',
        'product_id',
        'product_variant_id',
        'product_name',
        'sku',
        'variant_name',
        'price_type',
        'currency',
        'quantity',
        'unit_price',
        'gst_rate',
        'unit_tax_amount',
        'unit_price_after_gst',
        'discount_percent',
        'unit_discount_amount',
        'line_subtotal',
        'line_tax_amount',
        'line_price_after_gst',
        'line_discount_amount',
        'line_total',
    ];

    protected function casts(): array
    {
        return [
            'quotation_id' => 'integer',
            'product_id' => 'integer',
            'product_variant_id' => 'integer',
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'gst_rate' => 'decimal:2',
            'unit_tax_amount' => 'decimal:2',
            'unit_price_after_gst' => 'decimal:2',
            'discount_percent' => 'decimal:2',
            'unit_discount_amount' => 'decimal:2',
            'line_subtotal' => 'decimal:2',
            'line_tax_amount' => 'decimal:2',
            'line_price_after_gst' => 'decimal:2',
            'line_discount_amount' => 'decimal:2',
            'line_total' => 'decimal:2',
        ];
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

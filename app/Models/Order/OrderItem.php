<?php

namespace App\Models\Order;

use App\Models\Product\Product;
use App\Models\Product\ProductVariant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'sku',
        'product_name',
        'variant_name',
        'description',
        'quantity',
        'unit_price',
        'subtotal_amount',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'sort_order',
        'item_snapshot',
    ];

    protected function casts(): array
    {
        return [
            'order_id' => 'integer',
            'product_id' => 'integer',
            'product_variant_id' => 'integer',
            'quantity' => 'integer',
            'unit_price' => 'decimal:4',
            'subtotal_amount' => 'decimal:4',
            'discount_amount' => 'decimal:4',
            'tax_amount' => 'decimal:4',
            'total_amount' => 'decimal:4',
            'sort_order' => 'integer',
            'item_snapshot' => 'array',
        ];
    }

    // This links the item row to its parent order.
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // This links the item row to the referenced product.
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // This links the item row to the selected product variant.
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}

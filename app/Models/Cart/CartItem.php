<?php

namespace App\Models\Cart;

use App\Models\Product\ProductVariant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'product_variant_id',
        'quantity',
    ];

    protected function casts(): array
    {
        return [
            'cart_id' => 'integer',
            'product_variant_id' => 'integer',
            'quantity' => 'integer',
        ];
    }

    // This links the cart item to its parent cart.
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    // This links the cart item to the selected product variant.
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}

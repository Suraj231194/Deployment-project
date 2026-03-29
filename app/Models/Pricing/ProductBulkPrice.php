<?php

namespace App\Models\Pricing;

use App\Models\Authorization\Role;
use App\Models\Authorization\User;
use App\Models\Product\ProductVariant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductBulkPrice extends Model
{
    protected $fillable = [
        'product_variant_id',
        'user_id',
        'role_id',
        'applies_to_user_type',
        'min_quantity',
        'max_quantity',
        'amount',
        'currency',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'product_variant_id' => 'integer',
            'user_id' => 'integer',
            'role_id' => 'integer',
            'min_quantity' => 'integer',
            'max_quantity' => 'integer',
            'amount' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    // This links the bulk price slab to the sellable product variant.
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    // This links the bulk slab to one specific user when the offer is user-specific.
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // This links the bulk slab to one role when the offer is role-specific.
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}

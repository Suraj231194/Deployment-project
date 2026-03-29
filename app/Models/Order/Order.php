<?php

namespace App\Models\Order;

use App\Models\Authorization\Company;
use App\Models\Authorization\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'placed_by_user_id',
        'company_id',
        'status',
        'currency',
        'subtotal_amount',
        'tax_amount',
        'discount_amount',
        'shipping_amount',
        'adjustment_amount',
        'rounding_amount',
        'total_amount',
        'pricing_snapshot',
        'notes',
        'submitted_at',
        'approved_at',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'placed_by_user_id' => 'integer',
            'company_id' => 'integer',
            'subtotal_amount' => 'decimal:4',
            'tax_amount' => 'decimal:4',
            'discount_amount' => 'decimal:4',
            'shipping_amount' => 'decimal:4',
            'adjustment_amount' => 'decimal:4',
            'rounding_amount' => 'decimal:4',
            'total_amount' => 'decimal:4',
            'pricing_snapshot' => 'array',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // This links the order to the logged-in user who placed it.
    public function placedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'placed_by_user_id');
    }

    // This links the order to the company of the user when one exists.
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // This loads all item rows stored under the order.
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // This loads all stored shipping and billing address rows for the order.
    public function addresses(): HasMany
    {
        return $this->hasMany(OrderAddress::class);
    }

    // This loads the saved shipping address for quick order display screens.
    public function shippingAddress(): HasOne
    {
        return $this->hasOne(OrderAddress::class)->where('address_type', 'shipping');
    }

    // This loads the saved billing address for invoice-facing flows.
    public function billingAddress(): HasOne
    {
        return $this->hasOne(OrderAddress::class)->where('address_type', 'billing');
    }
}

<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderAddress extends Model
{
    protected $fillable = [
        'order_id',
        'address_type',
        'contact_name',
        'company_name',
        'email',
        'phone',
        'gstin',
        'line1',
        'line2',
        'landmark',
        'city',
        'state',
        'postal_code',
        'country_code',
    ];

    protected function casts(): array
    {
        return [
            'order_id' => 'integer',
        ];
    }

    // This links one stored shipping or billing address back to the parent order.
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}

<?php

namespace App\Models\Pricing;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'allow_with_bulk',
        'allow_with_product_discount',
        'allow_on_company_price',
        'is_active',
        'valid_from',
        'valid_to',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'allow_with_bulk' => 'boolean',
            'allow_with_product_discount' => 'boolean',
            'allow_on_company_price' => 'boolean',
            'is_active' => 'boolean',
            'valid_from' => 'datetime',
            'valid_to' => 'datetime',
        ];
    }
}

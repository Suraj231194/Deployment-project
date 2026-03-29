<?php

namespace App\Models\Quotation;

use App\Models\Authorization\Company;
use App\Models\Authorization\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quotation extends Model
{
    protected $fillable = [
        'quotation_number',
        'requester_type',
        'created_by_user_id',
        'owner_user_id',
        'owner_company_id',
        'target_type',
        'target_name',
        'target_email',
        'target_phone',
        'target_company_id',
        'status',
        'currency',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'price_after_gst',
        'total_amount',
        'guest_session_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'created_by_user_id' => 'integer',
            'owner_user_id' => 'integer',
            'owner_company_id' => 'integer',
            'target_company_id' => 'integer',
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'price_after_gst' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function ownerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function ownerCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'owner_company_id');
    }

    public function targetCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'target_company_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }
}

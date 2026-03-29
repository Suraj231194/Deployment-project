<?php

namespace App\Models\Authorization;

use App\Models\Proforma\ProformaInvoice;
use App\Models\Product\ProductPrice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = [
        'name',
        'legal_name',
        'gst_number',
        'pan_number',
        'registration_number',
        'established_year',
        'website',
        'company_type',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'established_year' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    // This loads all users attached to the company.
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // This loads company-scoped price rows.
    public function productPrices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }

    // This loads proformas where the company is the owner.
    public function ownedProformas(): HasMany
    {
        return $this->hasMany(ProformaInvoice::class, 'owner_company_id');
    }

    // This loads proformas where the company is the target.
    public function targetProformas(): HasMany
    {
        return $this->hasMany(ProformaInvoice::class, 'target_company_id');
    }
}

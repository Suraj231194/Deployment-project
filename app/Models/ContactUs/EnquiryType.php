<?php

namespace App\Models\ContactUs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EnquiryType extends Model
{
    protected $table = 'enquiry_types';

    protected $fillable = [
        'name',
        'slug',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    // This links one enquiry type to all website enquiries that use it.
    public function contactUsEnquiries(): HasMany
    {
        return $this->hasMany(ContactUsEnquiry::class, 'enquiry_type_id');
    }
}

<?php

namespace App\Models\ContactUs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactUsEnquiry extends Model
{
    protected $table = 'contact_us_enquiries';

    protected $fillable = [
        'enquiry_type_id',
        'full_name',
        'email',
        'phone',
        'message',
        'status',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'enquiry_type_id' => 'integer',
            'submitted_at' => 'datetime',
        ];
    }

    // This links the enquiry back to the selected enquiry type master.
    public function enquiryType(): BelongsTo
    {
        return $this->belongsTo(EnquiryType::class, 'enquiry_type_id');
    }
}

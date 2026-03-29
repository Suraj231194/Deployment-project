<?php

namespace App\Models\SupportTicket;

use App\Models\Authorization\Company;
use App\Models\Authorization\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    protected $fillable = [
        'ticket_number',
        'owner_user_id',
        'owner_company_id',
        'created_by_user_id',
        'category',
        'priority',
        'description',
        'status',
        'last_activity_at',
    ];

    protected function casts(): array
    {
        return [
            'owner_user_id' => 'integer',
            'owner_company_id' => 'integer',
            'created_by_user_id' => 'integer',
            'last_activity_at' => 'datetime',
        ];
    }

    // This links the ticket to the owner user.
    public function ownerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    // This links the ticket to the owner company.
    public function ownerCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'owner_company_id');
    }

    // This links the ticket to the creating user.
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    // This loads all comments for the ticket.
    public function comments(): HasMany
    {
        return $this->hasMany(SupportTicketComment::class);
    }

    // This loads all history rows for the ticket.
    public function history(): HasMany
    {
        return $this->hasMany(SupportTicketHistory::class);
    }

    // This loads all attachments for the ticket.
    public function attachments(): HasMany
    {
        return $this->hasMany(SupportTicketAttachment::class);
    }
}

<?php

namespace App\Models\SupportTicket;

use App\Models\Authorization\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicketComment extends Model
{
    protected $fillable = ['support_ticket_id', 'commenter_user_id', 'comment'];

    // This links the comment to its ticket.
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'support_ticket_id');
    }

    // This links the comment to the commenting user.
    public function commenter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'commenter_user_id');
    }

    // This loads attachments linked to the comment.
    public function attachments(): HasMany
    {
        return $this->hasMany(SupportTicketAttachment::class, 'support_ticket_comment_id');
    }

    // This loads history rows linked to the comment.
    public function history(): HasMany
    {
        return $this->hasMany(SupportTicketHistory::class, 'support_ticket_comment_id');
    }
}

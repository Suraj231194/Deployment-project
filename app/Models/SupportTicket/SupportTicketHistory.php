<?php

namespace App\Models\SupportTicket;

use App\Models\Authorization\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicketHistory extends Model
{
    public $timestamps = false;

    // This keeps the model aligned with the current database table name used by the support ticket history migration.
    protected $table = 'support_ticket_history';

    protected $fillable = [
        'support_ticket_id',
        'event_type',
        'actor_user_id',
        'from_status',
        'to_status',
        'support_ticket_comment_id',
        'message',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'support_ticket_id' => 'integer',
            'actor_user_id' => 'integer',
            'support_ticket_comment_id' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    // This links the history row to its ticket.
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'support_ticket_id');
    }

    // This links the history row to the acting user.
    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }

    // This links the history row to a related comment.
    public function comment(): BelongsTo
    {
        return $this->belongsTo(SupportTicketComment::class, 'support_ticket_comment_id');
    }
}

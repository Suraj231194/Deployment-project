<?php

namespace App\Models\SupportTicket;

use App\Models\Authorization\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicketAttachment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'support_ticket_id',
        'support_ticket_comment_id',
        'original_file_name',
        'stored_file_path',
        'file_size',
        'mime_type',
        'uploaded_by_user_id',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'support_ticket_id' => 'integer',
            'support_ticket_comment_id' => 'integer',
            'file_size' => 'integer',
            'uploaded_by_user_id' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    // This links the attachment to its ticket.
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'support_ticket_id');
    }

    // This links the attachment to its comment when present.
    public function comment(): BelongsTo
    {
        return $this->belongsTo(SupportTicketComment::class, 'support_ticket_comment_id');
    }

    // This links the attachment to the uploading user.
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }
}

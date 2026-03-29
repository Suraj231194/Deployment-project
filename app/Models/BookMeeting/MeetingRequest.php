<?php

namespace App\Models\BookMeeting;

use Illuminate\Database\Eloquent\Model;

class MeetingRequest extends Model
{
    protected $table = 'meeting_requests';

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'organization_name',
        'preferred_date',
        'start_time',
        'end_time',
        'status',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'preferred_date' => 'date',
            'submitted_at' => 'datetime',
        ];
    }
}

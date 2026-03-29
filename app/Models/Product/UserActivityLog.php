<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class UserActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['session_id', 'user_id', 'user_type', 'user_name', 'user_email', 'activity_type', 'path', 'payload', 'created_at'];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'payload' => 'array',
            'created_at' => 'datetime',
        ];
    }
}

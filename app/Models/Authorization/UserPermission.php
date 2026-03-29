<?php

namespace App\Models\Authorization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPermission extends Model
{
    protected $table = 'user_permissions';

    protected $fillable = ['user_id', 'permission_id', 'grant_type', 'granted_by_user_id'];

    // This links the override to its user.
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // This links the override to its permission.
    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }

    // This links the override to the user who granted it.
    public function grantedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'granted_by_user_id');
    }
}

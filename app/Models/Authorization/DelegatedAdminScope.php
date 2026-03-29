<?php

namespace App\Models\Authorization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DelegatedAdminScope extends Model
{
    protected $table = 'delegated_admin_scopes';

    protected $fillable = ['delegated_admin_user_id', 'scope_type', 'scope_value', 'assigned_by_user_id'];

    // This links the scope to the delegated admin user.
    public function delegatedAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delegated_admin_user_id');
    }

    // This links the scope to the assigning admin.
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by_user_id');
    }
}

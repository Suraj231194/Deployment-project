<?php

namespace App\Models\Authorization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Permission extends Model
{
    protected $fillable = ['name', 'slug'];

    // This loads roles that grant the permission.
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'permission_role')->withTimestamps();
    }

    // This loads user-level overrides for the permission.
    public function userOverrides(): HasMany
    {
        return $this->hasMany(UserPermission::class);
    }
}

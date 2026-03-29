<?php

namespace App\Models\Authorization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Department extends Model
{
    protected $fillable = ['name', 'slug', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // This loads users assigned to the department.
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'department_user')->withTimestamps();
    }
}

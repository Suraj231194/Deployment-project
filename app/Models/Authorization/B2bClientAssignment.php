<?php

namespace App\Models\Authorization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class B2bClientAssignment extends Model
{
    protected $table = 'b2b_client_assignments';

    protected $fillable = ['b2b_user_id', 'client_company_id', 'assigned_by_user_id'];

    // This links the assignment to the B2B user.
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'b2b_user_id');
    }

    // This links the assignment to the client company.
    public function clientCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'client_company_id');
    }

    // This links the assignment to the user who created it.
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by_user_id');
    }
}

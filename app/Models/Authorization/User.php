<?php

namespace App\Models\Authorization;

use App\Models\Proforma\ProformaInvoice;
use App\Services\Notification\EmailNotificationService;
use App\Models\SupportTicket\SupportTicket;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Throwable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'user_type',
        'b2b_type',
        'designation',
        'phone',
        'alt_phone',
        'company_id',
        'status',
        'approved_at',
        'approved_by_user_id',
        'created_by_user_id',
        'password',
        'password_updated_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'company_id' => 'integer',
            'approved_at' => 'datetime',
            'password_updated_at' => 'datetime',
            'approved_by_user_id' => 'integer',
            'created_by_user_id' => 'integer',
            'password' => 'hashed',
        ];
    }

    // This links the user to the company used for B2B and delegated access.
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // This links the user to the approving admin.
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(self::class, 'approved_by_user_id');
    }

    // This links the user to the creating admin.
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(self::class, 'created_by_user_id');
    }

    // This loads the roles assigned to the user.
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user')->withTimestamps();
    }

    // This loads the departments assigned to the user.
    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'department_user')->withTimestamps();
    }

    // This loads user-level permission override rows.
    public function permissionOverrides(): HasMany
    {
        return $this->hasMany(UserPermission::class);
    }

    // This loads delegated admin scope rows for the user.
    public function delegatedScopes(): HasMany
    {
        return $this->hasMany(DelegatedAdminScope::class, 'delegated_admin_user_id');
    }

    // This loads assigned client companies for the user.
    public function clientAssignments(): HasMany
    {
        return $this->hasMany(B2bClientAssignment::class, 'b2b_user_id');
    }

    // This loads proformas owned by the user.
    public function ownedProformas(): HasMany
    {
        return $this->hasMany(ProformaInvoice::class, 'owner_user_id');
    }

    // This loads support tickets owned by the user.
    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, 'owner_user_id');
    }

    // This loads saved addresses linked to the user.
    public function addresses(): HasMany
    {
        return $this->hasMany(UserAddress::class, 'user_id');
    }

    // This checks whether the user is a B2C account.
    public function isB2c(): bool
    {
        return $this->user_type === 'b2c';
    }

    // This checks whether the user is a B2B account.
    public function isB2b(): bool
    {
        return $this->user_type === 'b2b';
    }

    // This checks whether the user is an admin-capable account.
    public function isAdmin(): bool
    {
        return in_array($this->user_type, ['admin', 'delegated_admin'], true);
    }

    // This sends the password reset link through the shared notification service so provider changes stay centralized.
    public function sendPasswordResetNotification($token): void
    {
        try {
            // Step 1: build the standard Fortify password reset URL with the token and customer email.
            $resetUrl = url(route('password.reset', [
                'token' => $token,
                'email' => $this->getEmailForPasswordReset(),
            ], false));

            // Step 2: send the email through the shared provider-aware notification service.
            app(EmailNotificationService::class)->sendForgotPasswordResetLink($this, $resetUrl);
        } catch (Throwable $exception) {
            Log::error('Failed to start password reset notification send.', [  'user_id' => $this->id,  'email' => $this->email,  'error' => $exception->getMessage(),  ]);
            throw $exception;
        }
    }
}

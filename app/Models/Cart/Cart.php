<?php

namespace App\Models\Cart;

use App\Models\Authorization\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'currency',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
        ];
    }

    // This links the cart to the logged-in user when the cart belongs to an account.
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // This loads all item rows saved under the cart.
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
}

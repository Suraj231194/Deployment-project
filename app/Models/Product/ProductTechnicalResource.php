<?php

namespace App\Models\Product;

use App\Models\Authorization\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductTechnicalResource extends Model
{
    protected $fillable = [
        'product_id',
        'product_variant_id',
        'title',
        'resource_type',
        'description',
        'stored_file_path',
        'original_file_name',
        'mime_type',
        'file_size',
        'sort_order',
        'is_active',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'product_id' => 'integer',
            'product_variant_id' => 'integer',
            'file_size' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
            'created_by_user_id' => 'integer',
        ];
    }

    // This links the technical file back to its product master row.
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // This links the technical file to a specific sellable variant when the document is variant-specific.
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    // This links the technical file to the user who uploaded it for audit visibility.
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}

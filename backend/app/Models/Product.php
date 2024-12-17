<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    protected $fillable = [
        'name',
        'price',
        'stock',
        'min_stock',
        'expiration_date',
        'category_id',
        'supplier_id',
        'active_compound',
        'prescription_required',
        'storage_conditions',
        'barcode'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'expiration_date' => 'date',
        'prescription_required' => 'boolean',
        'stock' => 'integer',
        'min_stock' => 'integer'
    ];

    // Scope para productos con bajo stock
    public function scopeLowStock(Builder $query, $threshold = null)
    {
        return $query->whereColumn('stock', '<=', 'min_stock');
    }

    // Scope para productos próximos a vencer
    public function scopeNearExpiration(Builder $query, $days = 90)
    {
        return $query->whereDate('expiration_date', '<=', now()->addDays($days));
    }

    // Scope para búsqueda
    public function scopeSearch(Builder $query, $term)
    {
        return $query->where(function($query) use ($term) {
            $query->where('name', 'like', "%{$term}%")
                  ->orWhere('active_compound', 'like', "%{$term}%")
                  ->orWhere('barcode', 'like', "%{$term}%");
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'sku',
        'name',
        'unit',
        'minimum_stock',
        'current_stock',
        'cost_price',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'minimum_stock' => 'decimal:2',
            'current_stock' => 'decimal:2',
            'cost_price' => 'decimal:2',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function transactionItems()
    {
        return $this->hasMany(StockTransactionItem::class);
    }

    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->minimum_stock;
    }

    public function isOutOfStock(): bool
    {
        return $this->current_stock <= 0;
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_stock', '<=', 'minimum_stock');
    }
}

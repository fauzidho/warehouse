<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransactionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_transaction_id',
        'item_id',
        'quantity',
        'unit_cost',
        'subtotal',
        'expiry_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_cost' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'expiry_date' => 'date',
        ];
    }

    public function transaction()
    {
        return $this->belongsTo(StockTransaction::class, 'stock_transaction_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}

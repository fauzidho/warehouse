<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_code',
        'type', // in, out, adjustment
        'date',
        'supplier_id',
        'user_id',
        'destination_or_reason',
        'reference_number',
        'notes',
        'total_cost',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'total_cost' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (StockTransaction $transaction) {
            try {
                app(\App\Services\FirestoreService::class)->syncTransaction($transaction);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Firestore transaction sync failed: ' . $e->getMessage());
            }
        });
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(StockTransactionItem::class);
    }

    public function getTypeBadgeAttribute(): array
    {
        return match($this->type) {
            'in' => [
                'label' => 'Stok Masuk',
                'bg' => 'bg-zinc-800/90 text-zinc-300 border-zinc-700/70',
                'dot' => 'bg-emerald-400',
                'icon' => 'arrow-down-left'
            ],
            'out' => [
                'label' => 'Stok Keluar',
                'bg' => 'bg-zinc-800/90 text-zinc-300 border-zinc-700/70',
                'dot' => 'bg-amber-400',
                'icon' => 'arrow-up-right'
            ],
            'adjustment' => [
                'label' => 'Opname Stok',
                'bg' => 'bg-zinc-800/90 text-zinc-300 border-zinc-700/70',
                'dot' => 'bg-zinc-400',
                'icon' => 'refresh-cw'
            ],
            default => [
                'label' => ucfirst($this->type),
                'bg' => 'bg-zinc-800/90 text-zinc-300 border-zinc-700/70',
                'dot' => 'bg-zinc-400',
                'icon' => 'activity'
            ]
        };
    }
}

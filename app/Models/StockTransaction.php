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
                'bg' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                'icon' => 'arrow-down-left'
            ],
            'out' => [
                'label' => 'Stok Keluar',
                'bg' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                'icon' => 'arrow-up-right'
            ],
            'adjustment' => [
                'label' => 'Penyesuaian (Opname)',
                'bg' => 'bg-cyan-500/10 text-cyan-400 border-cyan-500/20',
                'icon' => 'refresh-cw'
            ],
            default => [
                'label' => ucfirst($this->type),
                'bg' => 'bg-zinc-500/10 text-zinc-400 border-zinc-500/20',
                'icon' => 'activity'
            ]
        };
    }
}

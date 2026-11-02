<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
    ];

    protected static function booted(): void
    {
        static::saved(function (Supplier $supplier) {
            try {
                app(\App\Services\FirestoreService::class)->syncSupplier($supplier);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Firestore supplier sync failed: ' . $e->getMessage());
            }
        });

        static::deleted(function (Supplier $supplier) {
            try {
                app(\App\Services\FirestoreService::class)->deleteSupplier($supplier->id);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Firestore supplier delete failed: ' . $e->getMessage());
            }
        });
    }

    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class);
    }
}

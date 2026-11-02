<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
    ];

    protected static function booted(): void
    {
        static::saved(function (Category $category) {
            try {
                app(\App\Services\FirestoreService::class)->syncCategory($category);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Firestore category sync failed: ' . $e->getMessage());
            }
        });

        static::deleted(function (Category $category) {
            try {
                app(\App\Services\FirestoreService::class)->deleteCategory($category->id);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Firestore category delete failed: ' . $e->getMessage());
            }
        });
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}

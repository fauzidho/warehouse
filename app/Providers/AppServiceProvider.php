<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Pagination\Paginator::useTailwind();

        // Ensure sqlite database file exists if sqlite is configured
        if (config('database.default') === 'sqlite') {
            $sqlitePath = config('database.connections.sqlite.database');
            if ($sqlitePath && $sqlitePath !== ':memory:' && !file_exists($sqlitePath)) {
                $dir = dirname($sqlitePath);
                if (!is_dir($dir)) {
                    @mkdir($dir, 0755, true);
                }
                @touch($sqlitePath);
            }
        }

        // Auto-migrate & seed when running on fresh/serverless environments (Vercel) without git database file
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('users')) {
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
                \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
            }

            // Auto-pull from Firestore if local inventory is empty
            if (\Illuminate\Support\Facades\Schema::hasTable('items') && \App\Models\Item::count() === 0) {
                app(\App\Services\FirestoreService::class)->pullFromFirestore();
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Auto-provisioning notice: ' . $e->getMessage());
        }

        view()->composer('*', function ($view) {
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('items')) {
                    $lowCount = \App\Models\Item::whereColumn('current_stock', '<=', 'minimum_stock')->count();
                    $view->with('globalLowStockCount', $lowCount);
                    return;
                }
            } catch (\Throwable) {
                // Ignore any DB exception if connection/file isn't ready
            }
            $view->with('globalLowStockCount', 0);
        });
    }
}

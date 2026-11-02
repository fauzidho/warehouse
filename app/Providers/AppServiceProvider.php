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

        // Auto-migrate & seed when running on fresh/serverless environments (Vercel) without git database file
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('users')) {
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
                \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Auto-provisioning notice: ' . $e->getMessage());
        }

        view()->composer('*', function ($view) {
            if (\Illuminate\Support\Facades\Schema::hasTable('items')) {
                $lowCount = \App\Models\Item::whereColumn('current_stock', '<=', 'minimum_stock')->count();
                $view->with('globalLowStockCount', $lowCount);
            } else {
                $view->with('globalLowStockCount', 0);
            }
        });
    }
}

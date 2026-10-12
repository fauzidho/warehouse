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

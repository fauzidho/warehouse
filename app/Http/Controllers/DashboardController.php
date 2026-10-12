<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\StockTransaction;
use App\Models\StockTransactionItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Total items
        $totalItems = Item::count();

        // Total inventory valuation
        $totalValuation = Item::select(DB::raw('SUM(current_stock * cost_price) as total'))
            ->value('total') ?? 0;

        // Low stock items
        $lowStockItems = Item::with('category')
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->orderBy('current_stock', 'asc')
            ->get();

        // Out of stock count
        $outOfStockCount = Item::where('current_stock', '<=', 0)->count();

        // Transactions today
        $todayStockInCount = StockTransaction::where('type', 'in')
            ->whereDate('date', $today)
            ->count();

        $todayStockOutCount = StockTransaction::where('type', 'out')
            ->whereDate('date', $today)
            ->count();

        // Expiring batches in next 30 days
        $expiringBatches = StockTransactionItem::with(['item', 'transaction.supplier'])
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '>=', $today)
            ->where('expiry_date', '<=', $today->copy()->addDays(30))
            ->orderBy('expiry_date', 'asc')
            ->take(5)
            ->get();

        // Recent 7 transactions
        $recentTransactions = StockTransaction::with(['user', 'supplier', 'items.item'])
            ->latest('date')
            ->latest('id')
            ->take(7)
            ->get();

        // Categories summary
        $categories = Category::withCount('items')->get();

        // Monthly transaction volume (last 6 months) for quick sparkline / comparison
        $monthlyIn = StockTransaction::where('type', 'in')
            ->where('date', '>=', Carbon::now()->subMonths(6))
            ->sum('total_cost');

        $monthlyOut = StockTransaction::where('type', 'out')
            ->where('date', '>=', Carbon::now()->subMonths(6))
            ->sum('total_cost');

        return view('dashboard.index', compact(
            'totalItems',
            'totalValuation',
            'lowStockItems',
            'outOfStockCount',
            'todayStockInCount',
            'todayStockOutCount',
            'expiringBatches',
            'recentTransactions',
            'categories',
            'monthlyIn',
            'monthlyOut'
        ));
    }
}

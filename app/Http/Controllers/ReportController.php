<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\StockTransaction;
use App\Models\StockTransactionItem;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());
        $categoryId = $request->get('category_id');
        $type = $request->get('type'); // in, out, adjustment, or all

        $query = StockTransactionItem::with(['transaction.user', 'transaction.supplier', 'item.category'])
            ->whereHas('transaction', function ($q) use ($startDate, $endDate, $type) {
                $q->whereDate('date', '>=', $startDate)
                  ->whereDate('date', '<=', $endDate);
                if ($type) {
                    $q->where('type', $type);
                }
            });

        if ($categoryId) {
            $query->whereHas('item', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        $records = $query->join('stock_transactions', 'stock_transaction_items.stock_transaction_id', '=', 'stock_transactions.id')
            ->orderBy('stock_transactions.date', 'desc')
            ->orderBy('stock_transactions.id', 'desc')
            ->select('stock_transaction_items.*')
            ->paginate(20)
            ->withQueryString();

        // Rekapitulasi Summary
        $summaryQuery = StockTransactionItem::whereHas('transaction', function ($q) use ($startDate, $endDate) {
            $q->whereDate('date', '>=', $startDate)
              ->whereDate('date', '<=', $endDate);
        });

        if ($categoryId) {
            $summaryQuery->whereHas('item', fn($q) => $q->where('category_id', $categoryId));
        }

        $totalInQty = (clone $summaryQuery)->whereHas('transaction', fn($q) => $q->where('type', 'in'))->sum('quantity');
        $totalInCost = (clone $summaryQuery)->whereHas('transaction', fn($q) => $q->where('type', 'in'))->sum('subtotal');

        $totalOutQty = (clone $summaryQuery)->whereHas('transaction', fn($q) => $q->where('type', 'out'))->sum('quantity');
        $totalOutCost = (clone $summaryQuery)->whereHas('transaction', fn($q) => $q->where('type', 'out'))->sum('subtotal');

        $categories = Category::orderBy('name')->get();

        return view('reports.index', compact(
            'records',
            'startDate',
            'endDate',
            'categoryId',
            'type',
            'totalInQty',
            'totalInCost',
            'totalOutQty',
            'totalOutCost',
            'categories'
        ));
    }

    public function print(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());
        $categoryId = $request->get('category_id');
        $type = $request->get('type');

        $query = StockTransactionItem::with(['transaction.user', 'transaction.supplier', 'item.category'])
            ->whereHas('transaction', function ($q) use ($startDate, $endDate, $type) {
                $q->whereDate('date', '>=', $startDate)
                  ->whereDate('date', '<=', $endDate);
                if ($type) {
                    $q->where('type', $type);
                }
            });

        if ($categoryId) {
            $query->whereHas('item', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        $records = $query->join('stock_transactions', 'stock_transaction_items.stock_transaction_id', '=', 'stock_transactions.id')
            ->orderBy('stock_transactions.date', 'asc')
            ->orderBy('stock_transactions.id', 'asc')
            ->select('stock_transaction_items.*')
            ->get();

        $selectedCategory = $categoryId ? Category::find($categoryId) : null;

        return view('reports.print', compact(
            'records',
            'startDate',
            'endDate',
            'selectedCategory',
            'type'
        ));
    }
}

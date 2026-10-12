<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockTransaction;
use App\Models\StockTransactionItem;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockInController extends Controller
{
    public function index(Request $request)
    {
        $query = StockTransaction::with(['supplier', 'user', 'items.item'])
            ->where('type', 'in');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_code', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhere('destination_or_reason', 'like', "%{$search}%")
                  ->orWhereHas('supplier', fn($sq) => $sq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        $transactions = $query->latest('date')->latest('id')->paginate(12)->withQueryString();

        return view('stock_in.index', compact('transactions'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $items = Item::with('category')->orderBy('name')->get();
        $today = Carbon::today()->toDateString();

        return view('stock_in.create', compact('suppliers', 'items', 'today'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'destination_or_reason' => ['required', 'string', 'max:255'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'exists:items,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
            'items.*.expiry_date' => ['nullable', 'date'],
            'items.*.notes' => ['nullable', 'string'],
        ]);

        return DB::transaction(function () use ($validated) {
            $datePrefix = Carbon::parse($validated['date'])->format('Ymd');
            $latestTx = StockTransaction::where('type', 'in')
                ->where('transaction_code', 'like', "IN-{$datePrefix}-%")
                ->count();
            $seqNumber = str_pad($latestTx + 1, 4, '0', STR_PAD_LEFT);
            $transactionCode = "IN-{$datePrefix}-{$seqNumber}";

            $totalCost = 0;
            foreach ($validated['items'] as $it) {
                $totalCost += ($it['quantity'] * $it['unit_cost']);
            }

            $transaction = StockTransaction::create([
                'transaction_code' => $transactionCode,
                'type' => 'in',
                'date' => $validated['date'],
                'supplier_id' => $validated['supplier_id'] ?? null,
                'user_id' => Auth::id(),
                'destination_or_reason' => $validated['destination_or_reason'],
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'total_cost' => $totalCost,
            ]);

            foreach ($validated['items'] as $itemData) {
                $subtotal = $itemData['quantity'] * $itemData['unit_cost'];

                StockTransactionItem::create([
                    'stock_transaction_id' => $transaction->id,
                    'item_id' => $itemData['item_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_cost' => $itemData['unit_cost'],
                    'subtotal' => $subtotal,
                    'expiry_date' => !empty($itemData['expiry_date']) ? $itemData['expiry_date'] : null,
                    'notes' => $itemData['notes'] ?? null,
                ]);

                // Update Item current_stock and cost_price
                $item = Item::find($itemData['item_id']);
                if ($item) {
                    $item->current_stock += $itemData['quantity'];
                    if ($itemData['unit_cost'] > 0) {
                        $item->cost_price = $itemData['unit_cost'];
                    }
                    $item->save();
                }
            }

            return redirect()->route('stock-in.show', $transaction)
                ->with('success', "Stok Masuk {$transaction->transaction_code} berhasil dicatat!");
        });
    }

    public function show(StockTransaction $stockIn)
    {
        $stockIn->load(['supplier', 'user', 'items.item.category']);

        return view('stock_in.show', compact('stockIn'));
    }
}

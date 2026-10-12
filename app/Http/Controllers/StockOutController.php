<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockTransaction;
use App\Models\StockTransactionItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockOutController extends Controller
{
    public function index(Request $request)
    {
        $query = StockTransaction::with(['user', 'items.item'])
            ->where('type', 'out');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_code', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhere('destination_or_reason', 'like', "%{$search}%");
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        $transactions = $query->latest('date')->latest('id')->paginate(12)->withQueryString();

        return view('stock_out.index', compact('transactions'));
    }

    public function create()
    {
        $items = Item::with('category')->where('current_stock', '>', 0)->orderBy('name')->get();
        $today = Carbon::today()->toDateString();

        $destinations = [
            'Pemakaian Bar Espresso' => 'Pemakaian Bar Espresso (Kopi, Susu, Sirup)',
            'Pemakaian Kitchen / Dapur' => 'Pemakaian Kitchen / Dapur (Makanan & Snack)',
            'Bahan Rusak / Basi / Spillage' => 'Bahan Rusak / Basi / Tercecer (Waste)',
            'Sampel / Tasting / Kalibrasi' => 'Sampel / Tasting / Kalibrasi Resep Barista',
            'Event & Catering Luar' => 'Event & Catering Luar Kedai',
            'Lainnya' => 'Lainnya / Non-rutin',
        ];

        return view('stock_out.create', compact('items', 'today', 'destinations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'destination_or_reason' => ['required', 'string', 'max:255'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'exists:items,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.notes' => ['nullable', 'string'],
        ]);

        // Cek stok tiap item sebelum eksekusi
        foreach ($validated['items'] as $it) {
            $item = Item::find($it['item_id']);
            if ($item && $item->current_stock < $it['quantity']) {
                return back()->withInput()->withErrors([
                    'items' => "Stok untuk {$item->name} tidak mencukupi! Tersedia: {$item->current_stock} {$item->unit}, diminta: {$it['quantity']} {$item->unit}."
                ]);
            }
        }

        return DB::transaction(function () use ($validated) {
            $datePrefix = Carbon::parse($validated['date'])->format('Ymd');
            $latestTx = StockTransaction::where('type', 'out')
                ->where('transaction_code', 'like', "OUT-{$datePrefix}-%")
                ->count();
            $seqNumber = str_pad($latestTx + 1, 4, '0', STR_PAD_LEFT);
            $transactionCode = "OUT-{$datePrefix}-{$seqNumber}";

            $totalCost = 0;
            foreach ($validated['items'] as $it) {
                $item = Item::find($it['item_id']);
                $totalCost += ($it['quantity'] * ($item->cost_price ?? 0));
            }

            $transaction = StockTransaction::create([
                'transaction_code' => $transactionCode,
                'type' => 'out',
                'date' => $validated['date'],
                'user_id' => Auth::id(),
                'destination_or_reason' => $validated['destination_or_reason'],
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'total_cost' => $totalCost,
            ]);

            foreach ($validated['items'] as $itemData) {
                $item = Item::find($itemData['item_id']);
                $subtotal = $itemData['quantity'] * ($item->cost_price ?? 0);

                StockTransactionItem::create([
                    'stock_transaction_id' => $transaction->id,
                    'item_id' => $itemData['item_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_cost' => $item->cost_price ?? 0,
                    'subtotal' => $subtotal,
                    'notes' => $itemData['notes'] ?? null,
                ]);

                // Kurangi stok
                $item->current_stock -= $itemData['quantity'];
                if ($item->current_stock < 0) {
                    $item->current_stock = 0;
                }
                $item->save();
            }

            return redirect()->route('stock-out.show', $transaction)
                ->with('success', "Pengeluaran stok {$transaction->transaction_code} berhasil dicatat!");
        });
    }

    public function show(StockTransaction $stockOut)
    {
        $stockOut->load(['user', 'items.item.category']);

        return view('stock_out.show', compact('stockOut'));
    }
}

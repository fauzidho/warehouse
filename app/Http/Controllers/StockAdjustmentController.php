<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\StockTransaction;
use App\Models\StockTransactionItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    public function index(Request $request)
    {
        $query = StockTransaction::with(['user', 'items.item'])
            ->where('type', 'adjustment');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_code', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhere('destination_or_reason', 'like', "%{$search}%");
            });
        }

        $adjustments = $query->latest('date')->latest('id')->paginate(12)->withQueryString();

        return view('adjustments.index', compact('adjustments'));
    }

    public function create(Request $request)
    {
        $categoryId = $request->get('category_id');
        $query = Item::with('category')->orderBy('name');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $items = $query->get();
        $categories = Category::orderBy('name')->get();
        $today = Carbon::today()->toDateString();

        return view('adjustments.create', compact('items', 'categories', 'today', 'categoryId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            'adjustments' => ['required', 'array', 'min:1'],
            'adjustments.*.item_id' => ['required', 'exists:items,id'],
            'adjustments.*.physical_stock' => ['required', 'numeric', 'min:0'],
            'adjustments.*.reason' => ['nullable', 'string'],
        ]);

        return DB::transaction(function () use ($validated) {
            $datePrefix = Carbon::parse($validated['date'])->format('Ymd');
            $latestTx = StockTransaction::where('type', 'adjustment')
                ->where('transaction_code', 'like', "ADJ-{$datePrefix}-%")
                ->count();
            $seqNumber = str_pad($latestTx + 1, 4, '0', STR_PAD_LEFT);
            $transactionCode = "ADJ-{$datePrefix}-{$seqNumber}";

            $adjustedItems = [];
            $totalCostImpact = 0;

            foreach ($validated['adjustments'] as $adj) {
                $item = Item::find($adj['item_id']);
                $physicalStock = floatval($adj['physical_stock']);
                $systemStock = floatval($item->current_stock);
                $diff = $physicalStock - $systemStock;

                // Hanya simpan item yang ada selisih, atau semua jika sengaja di-opname
                if (abs($diff) > 0.001 || !empty($adj['reason'])) {
                    $adjustedItems[] = [
                        'item' => $item,
                        'diff' => $diff,
                        'physical_stock' => $physicalStock,
                        'system_stock' => $systemStock,
                        'reason' => $adj['reason'] ?? 'Opname Fisik',
                    ];

                    $totalCostImpact += abs($diff * ($item->cost_price ?? 0));
                }
            }

            if (empty($adjustedItems)) {
                return back()->withInput()->with('warning', 'Tidak ada selisih stok fisik vs sistem yang dicatat.');
            }

            $transaction = StockTransaction::create([
                'transaction_code' => $transactionCode,
                'type' => 'adjustment',
                'date' => $validated['date'],
                'user_id' => Auth::id(),
                'destination_or_reason' => 'Stock Opname Fisik Gudang',
                'reference_number' => $validated['reference_number'] ?? 'SO-' . $datePrefix,
                'notes' => $validated['notes'] ?? null,
                'total_cost' => $totalCostImpact,
            ]);

            foreach ($adjustedItems as $adjData) {
                $item = $adjData['item'];
                $diff = $adjData['diff'];

                StockTransactionItem::create([
                    'stock_transaction_id' => $transaction->id,
                    'item_id' => $item->id,
                    'quantity' => $diff, // Bisa positif (lebih) atau negatif (kurang)
                    'unit_cost' => $item->cost_price ?? 0,
                    'subtotal' => abs($diff * ($item->cost_price ?? 0)),
                    'notes' => "Sistem: {$adjData['system_stock']} {$item->unit} -> Fisik: {$adjData['physical_stock']} {$item->unit} (" . ($diff >= 0 ? "+{$diff}" : "{$diff}") . "). Ket: {$adjData['reason']}",
                ]);

                // Update actual stock
                $item->current_stock = $adjData['physical_stock'];
                $item->save();
            }

            return redirect()->route('adjustments.show', $transaction)
                ->with('success', "Stock Opname {$transaction->transaction_code} berhasil diproses dan stok disesuaikan.");
        });
    }

    public function show(StockTransaction $adjustment)
    {
        $adjustment->load(['user', 'items.item.category']);

        return view('adjustments.show', compact('adjustment'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\StockTransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            if ($request->status === 'low') {
                $query->whereColumn('current_stock', '<=', 'minimum_stock')
                      ->where('current_stock', '>', 0);
            } elseif ($request->status === 'out') {
                $query->where('current_stock', '<=', 0);
            } elseif ($request->status === 'safe') {
                $query->whereColumn('current_stock', '>', 'minimum_stock');
            }
        }

        $items = $query->orderBy('name')->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('items.index', compact('items', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'sku' => ['required', 'string', 'max:50', 'unique:items,sku'],
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:50'],
            'minimum_stock' => ['required', 'numeric', 'min:0'],
            'current_stock' => ['required', 'numeric', 'min:0'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        $item = Item::create($validated);

        return redirect()->route('items.show', $item)
            ->with('success', 'Bahan baku baru "' . $item->name . '" berhasil ditambahkan.');
    }

    public function show(Item $item)
    {
        $item->load('category');

        // Kartu stok / Mutasi stok item ini
        $stockHistory = StockTransactionItem::with(['transaction.user', 'transaction.supplier'])
            ->where('item_id', $item->id)
            ->whereHas('transaction')
            ->join('stock_transactions', 'stock_transaction_items.stock_transaction_id', '=', 'stock_transactions.id')
            ->orderBy('stock_transactions.date', 'desc')
            ->orderBy('stock_transactions.id', 'desc')
            ->select('stock_transaction_items.*')
            ->paginate(15);

        return view('items.show', compact('item', 'stockHistory'));
    }

    public function edit(Item $item)
    {
        $categories = Category::orderBy('name')->get();
        return view('items.edit', compact('item', 'categories'));
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'sku' => ['required', 'string', 'max:50', 'unique:items,sku,' . $item->id],
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:50'],
            'minimum_stock' => ['required', 'numeric', 'min:0'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        $item->update($validated);

        return redirect()->route('items.show', $item)
            ->with('success', 'Data bahan baku "' . $item->name . '" berhasil diperbarui.');
    }

    public function destroy(Item $item)
    {
        $name = $item->name;
        $item->delete();

        return redirect()->route('items.index')
            ->with('success', 'Bahan baku "' . $name . '" telah dihapus dari sistem.');
    }
}

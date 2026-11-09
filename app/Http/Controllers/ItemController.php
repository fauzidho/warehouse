<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\StockTransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    /**
     * Impor & konversi data bahan baku dari Excel/CSV
     */
    public function import(Request $request)
    {
        // 1. Jika dikirim dalam format JSON dari tabel konversi realtime
        if ($request->has('items') && is_array($request->items)) {
            $itemsData = $request->items;
        } elseif ($request->hasFile('file')) {
            // Jika dikirim langsung via file upload (CSV / Excel sederhana)
            $file = $request->file('file');
            $extension = strtolower($file->getClientOriginalExtension());
            $itemsData = [];

            if ($extension === 'csv' || $extension === 'txt') {
                $handle = fopen($file->getRealPath(), 'r');
                $header = fgetcsv($handle, 1000, ',');
                if ($header) {
                    // Normalisasi header
                    $headerMap = [];
                    foreach ($header as $idx => $col) {
                        $c = strtolower(trim($col));
                        if (str_contains($c, 'nama')) $headerMap['name'] = $idx;
                        elseif (str_contains($c, 'kategori')) $headerMap['category'] = $idx;
                        elseif (str_contains($c, 'satuan')) $headerMap['unit'] = $idx;
                        elseif (str_contains($c, 'awal') || str_contains($c, 'stok')) $headerMap['current_stock'] = $idx;
                        elseif (str_contains($c, 'min')) $headerMap['minimum_stock'] = $idx;
                        elseif (str_contains($c, 'harga') || str_contains($c, 'beli')) $headerMap['cost_price'] = $idx;
                        elseif (str_contains($c, 'ket') || str_contains($c, 'deskripsi')) $headerMap['description'] = $idx;
                    }

                    while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                        if (empty($row) || !isset($row[$headerMap['name'] ?? 0])) continue;
                        $itemsData[] = [
                            'name' => $row[$headerMap['name'] ?? 0] ?? '',
                            'category' => isset($headerMap['category']) ? ($row[$headerMap['category']] ?? '') : '',
                            'unit' => isset($headerMap['unit']) ? ($row[$headerMap['unit']] ?? 'pcs') : 'pcs',
                            'current_stock' => isset($headerMap['current_stock']) ? ($row[$headerMap['current_stock']] ?? 0) : 0,
                            'minimum_stock' => isset($headerMap['minimum_stock']) ? ($row[$headerMap['minimum_stock']] ?? 5) : 5,
                            'cost_price' => isset($headerMap['cost_price']) ? ($row[$headerMap['cost_price']] ?? 0) : 0,
                            'description' => isset($headerMap['description']) ? ($row[$headerMap['description']] ?? '') : '',
                        ];
                    }
                }
                fclose($handle);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Data impor tidak ditemukan. Harap unggah file Excel/CSV atau kirim baris data.'
            ], 422);
        }

        if (empty($itemsData)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada baris data yang valid untuk diimpor.'
            ], 422);
        }

        $imported = 0;
        $updated = 0;

        DB::transaction(function () use ($itemsData, &$imported, &$updated) {
            $categories = Category::all()->keyBy(function ($cat) {
                return strtolower(trim($cat->name));
            });

            foreach ($itemsData as $row) {
                $name = trim($row['name'] ?? '');
                if (!$name) continue;

                $categoryName = trim($row['category'] ?? '');
                if (!$categoryName) {
                    $categoryName = 'Bahan Baku';
                }

                $catKey = strtolower($categoryName);
                if (!isset($categories[$catKey])) {
                    $newCat = Category::create([
                        'name' => $categoryName,
                        'slug' => Str::slug($categoryName) . '-' . Str::random(4),
                        'description' => 'Dibuat otomatis dari impor data Excel'
                    ]);
                    $categories[$catKey] = $newCat;
                }

                $category = $categories[$catKey];
                $unit = trim($row['unit'] ?? 'pcs');
                if (!$unit) $unit = 'pcs';

                $currentStock = isset($row['current_stock']) ? floatval(str_replace(',', '.', $row['current_stock'])) : 0;
                $minimumStock = isset($row['minimum_stock']) ? floatval(str_replace(',', '.', $row['minimum_stock'])) : 5;
                $costPrice = isset($row['cost_price']) ? floatval(preg_replace('/[^0-9.]/', '', str_replace(',', '.', $row['cost_price']))) : 0;
                $description = trim($row['description'] ?? '');

                // Cek apakah item sudah ada berdasarkan nama (case-insensitive)
                $existing = Item::whereRaw('LOWER(name) = ?', [strtolower($name)])->first();

                if ($existing) {
                    $existing->update([
                        'category_id' => $category->id,
                        'unit' => $unit,
                        'minimum_stock' => $minimumStock,
                        'cost_price' => $costPrice > 0 ? $costPrice : $existing->cost_price,
                        'description' => $description ?: $existing->description,
                    ]);
                    $updated++;
                } else {
                    Item::create([
                        'category_id' => $category->id,
                        'name' => $name,
                        'unit' => $unit,
                        'current_stock' => max(0, $currentStock),
                        'minimum_stock' => max(0, $minimumStock),
                        'cost_price' => max(0, $costPrice),
                        'description' => $description,
                    ]);
                    $imported++;
                }
            }
        });

        $message = "Berhasil mengonversi dan mengimpor file Excel: {$imported} bahan baru ditambahkan, {$updated} bahan diperbarui.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'imported' => $imported,
                'updated' => $updated,
            ]);
        }

        return redirect()->route('items.index')->with('success', $message);
    }
}

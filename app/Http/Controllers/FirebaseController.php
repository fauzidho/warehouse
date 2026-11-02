<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\StockTransaction;
use App\Models\StockTransactionItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FirebaseController extends Controller
{
    public function index()
    {
        $firebaseConfig = config('firebase');
        $localStats = [
            'items_count' => Item::count(),
            'categories_count' => Category::count(),
            'suppliers_count' => Supplier::count(),
            'transactions_count' => StockTransaction::count(),
        ];

        return view('firebase.index', compact('firebaseConfig', 'localStats'));
    }

    /**
     * Return all local database data as JSON for frontend Firebase SDK to sync to Cloud Firestore.
     */
    public function exportData()
    {
        $categories = Category::all();
        $suppliers = Supplier::all();
        $items = Item::with('category')->get();
        $transactions = StockTransaction::with(['items', 'supplier', 'user'])->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'categories' => $categories,
                'suppliers' => $suppliers,
                'items' => $items,
                'transactions' => $transactions,
            ]
        ]);
    }

    /**
     * Import documents received from Cloud Firestore into the local database.
     */
    public function importData(Request $request)
    {
        $payload = $request->validate([
            'categories' => ['nullable', 'array'],
            'suppliers' => ['nullable', 'array'],
            'items' => ['nullable', 'array'],
        ]);

        return DB::transaction(function () use ($payload) {
            $importedCategories = 0;
            $importedSuppliers = 0;
            $importedItems = 0;

            // 1. Categories
            if (!empty($payload['categories'])) {
                foreach ($payload['categories'] as $cat) {
                    Category::updateOrCreate(
                        ['slug' => $cat['slug'] ?? \Illuminate\Support\Str::slug($cat['name'])],
                        [
                            'name' => $cat['name'],
                            'description' => $cat['description'] ?? null,
                            'icon' => $cat['icon'] ?? 'coffee',
                        ]
                    );
                    $importedCategories++;
                }
            }

            // 2. Suppliers
            if (!empty($payload['suppliers'])) {
                foreach ($payload['suppliers'] as $sup) {
                    Supplier::updateOrCreate(
                        ['name' => $sup['name']],
                        [
                            'contact_person' => $sup['contact_person'] ?? null,
                            'phone' => $sup['phone'] ?? null,
                            'email' => $sup['email'] ?? null,
                            'address' => $sup['address'] ?? null,
                        ]
                    );
                    $importedSuppliers++;
                }
            }

            // 3. Items
            if (!empty($payload['items'])) {
                foreach ($payload['items'] as $it) {
                    $category = null;
                    if (!empty($it['category_name'])) {
                        $category = Category::firstOrCreate(
                            ['name' => $it['category_name']],
                            ['slug' => \Illuminate\Support\Str::slug($it['category_name'])]
                        );
                    } elseif (!empty($it['category_id'])) {
                        $category = Category::find($it['category_id']);
                    }

                    if (!$category) {
                        $category = Category::firstOrCreate(
                            ['name' => 'Bahan Baku Umum'],
                            ['slug' => 'bahan-baku-umum']
                        );
                    }

                    $match = !empty($it['id']) ? ['id' => $it['id']] : ['name' => $it['name']];
                    Item::updateOrCreate(
                        $match,
                        [
                            'category_id' => $category->id,
                            'name' => $it['name'],
                            'unit' => $it['unit'] ?? 'pcs',
                            'minimum_stock' => $it['minimum_stock'] ?? 5,
                            'current_stock' => $it['current_stock'] ?? 0,
                            'cost_price' => $it['cost_price'] ?? 0,
                            'description' => $it['description'] ?? null,
                        ]
                    );
                    $importedItems++;
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => "Berhasil sinkronisasi dari Firestore: {$importedItems} bahan, {$importedCategories} kategori, {$importedSuppliers} supplier.",
            ]);
        });
    }
}

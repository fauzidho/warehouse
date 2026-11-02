<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Item;
use App\Models\StockTransaction;
use App\Models\Supplier;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirestoreService
{
    protected string $projectId;
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->projectId = config('firebase.project_id', 'toko-kopi-8abbd');
        $this->apiKey = config('firebase.api_key', 'AIzaSyDSfrl6WclAYdSoPtxsFt1Pvh5N2NReqMw');
        $this->baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents";
    }

    protected function formatValue(mixed $val): array
    {
        if (is_null($val)) {
            return ['nullValue' => null];
        }
        if (is_bool($val)) {
            return ['booleanValue' => $val];
        }
        if (is_int($val)) {
            return ['integerValue' => (string)$val];
        }
        if (is_float($val)) {
            return ['doubleValue' => $val];
        }
        if (is_numeric($val)) {
            if (str_contains((string)$val, '.')) {
                return ['doubleValue' => (float)$val];
            }
            return ['integerValue' => (string)$val];
        }
        return ['stringValue' => (string)$val];
    }

    public function syncItem(Item $item): bool
    {
        try {
            $item->loadMissing('category');
            $fields = [
                'id' => $this->formatValue($item->id),
                'name' => $this->formatValue($item->name),
                'category_id' => $this->formatValue($item->category_id),
                'category_name' => $this->formatValue($item->category?->name ?? ''),
                'unit' => $this->formatValue($item->unit),
                'current_stock' => $this->formatValue((float)$item->current_stock),
                'minimum_stock' => $this->formatValue((float)$item->minimum_stock),
                'cost_price' => $this->formatValue((float)$item->cost_price),
                'description' => $this->formatValue($item->description ?? ''),
                'updatedAt' => ['timestampValue' => now()->toISOString()],
            ];

            $url = "{$this->baseUrl}/items/{$item->id}?key={$this->apiKey}";
            $response = Http::timeout(5)->patch($url, ['fields' => $fields]);
            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning('Firestore syncItem error: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteItem(int $itemId): bool
    {
        try {
            $url = "{$this->baseUrl}/items/{$itemId}?key={$this->apiKey}";
            $response = Http::timeout(5)->delete($url);
            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning('Firestore deleteItem error: ' . $e->getMessage());
            return false;
        }
    }

    public function syncCategory(Category $category): bool
    {
        try {
            $fields = [
                'id' => $this->formatValue($category->id),
                'name' => $this->formatValue($category->name),
                'slug' => $this->formatValue($category->slug),
                'description' => $this->formatValue($category->description ?? ''),
                'updatedAt' => ['timestampValue' => now()->toISOString()],
            ];

            $url = "{$this->baseUrl}/categories/{$category->id}?key={$this->apiKey}";
            $response = Http::timeout(5)->patch($url, ['fields' => $fields]);
            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning('Firestore syncCategory error: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteCategory(int $catId): bool
    {
        try {
            $url = "{$this->baseUrl}/categories/{$catId}?key={$this->apiKey}";
            $response = Http::timeout(5)->delete($url);
            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning('Firestore deleteCategory error: ' . $e->getMessage());
            return false;
        }
    }

    public function syncSupplier(Supplier $supplier): bool
    {
        try {
            $fields = [
                'id' => $this->formatValue($supplier->id),
                'name' => $this->formatValue($supplier->name),
                'contact_person' => $this->formatValue($supplier->contact_person ?? ''),
                'phone' => $this->formatValue($supplier->phone ?? ''),
                'email' => $this->formatValue($supplier->email ?? ''),
                'address' => $this->formatValue($supplier->address ?? ''),
                'updatedAt' => ['timestampValue' => now()->toISOString()],
            ];

            $url = "{$this->baseUrl}/suppliers/{$supplier->id}?key={$this->apiKey}";
            $response = Http::timeout(5)->patch($url, ['fields' => $fields]);
            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning('Firestore syncSupplier error: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteSupplier(int $supId): bool
    {
        try {
            $url = "{$this->baseUrl}/suppliers/{$supId}?key={$this->apiKey}";
            $response = Http::timeout(5)->delete($url);
            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning('Firestore deleteSupplier error: ' . $e->getMessage());
            return false;
        }
    }

    public function syncTransaction(StockTransaction $transaction): bool
    {
        try {
            $fields = [
                'id' => $this->formatValue($transaction->id),
                'transaction_code' => $this->formatValue($transaction->transaction_code),
                'type' => $this->formatValue($transaction->type),
                'date' => $this->formatValue($transaction->date?->format('Y-m-d') ?? (string)$transaction->date),
                'destination_or_reason' => $this->formatValue($transaction->destination_or_reason ?? ''),
                'total_cost' => $this->formatValue((float)$transaction->total_cost),
                'updatedAt' => ['timestampValue' => now()->toISOString()],
            ];

            $url = "{$this->baseUrl}/stock_transactions/{$transaction->transaction_code}?key={$this->apiKey}";
            $response = Http::timeout(5)->patch($url, ['fields' => $fields]);
            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning('Firestore syncTransaction error: ' . $e->getMessage());
            return false;
        }
    }

    public function parseDocument(array $doc): array
    {
        $fields = $doc['fields'] ?? [];
        $data = [];
        foreach ($fields as $key => $valObj) {
            $type = array_key_first($valObj);
            $val = $valObj[$type];
            if ($type === 'integerValue') {
                $data[$key] = (int)$val;
            } elseif ($type === 'doubleValue') {
                $data[$key] = (float)$val;
            } elseif ($type === 'booleanValue') {
                $data[$key] = (bool)$val;
            } else {
                $data[$key] = $val;
            }
        }
        return $data;
    }

    public function getCollectionDocuments(string $collection): array
    {
        try {
            $url = "{$this->baseUrl}/{$collection}?key={$this->apiKey}";
            $response = Http::timeout(10)->get($url);
            if (!$response->successful()) {
                Log::warning("Firestore getCollectionDocuments {$collection} returned status: " . $response->status());
                return [];
            }
            $docs = $response->json('documents') ?? [];
            return array_map([$this, 'parseDocument'], $docs);
        } catch (\Throwable $e) {
            Log::warning("Firestore getCollectionDocuments {$collection} error: " . $e->getMessage());
            return [];
        }
    }

    public function pullFromFirestore(): array
    {
        $categories = $this->getCollectionDocuments('categories');
        $suppliers = $this->getCollectionDocuments('suppliers');
        $items = $this->getCollectionDocuments('items');
        $transactions = $this->getCollectionDocuments('stock_transactions');

        $importedCategories = 0;
        $importedSuppliers = 0;
        $importedItems = 0;
        $importedTransactions = 0;

        // 1. Categories
        Category::withoutEvents(function () use ($categories, &$importedCategories) {
            foreach ($categories as $cat) {
                if (empty($cat['name'])) continue;
                $slug = $cat['slug'] ?? \Illuminate\Support\Str::slug($cat['name']);
                $model = !empty($cat['id']) ? Category::find($cat['id']) : Category::where('slug', $slug)->first();
                if (!$model) {
                    $model = new Category();
                    if (!empty($cat['id'])) {
                        $model->id = $cat['id'];
                    }
                }
                $model->name = $cat['name'];
                $model->slug = $slug;
                $model->description = $cat['description'] ?? null;
                $model->icon = $cat['icon'] ?? 'coffee';
                $model->save();
                $importedCategories++;
            }
        });

        // 2. Suppliers
        Supplier::withoutEvents(function () use ($suppliers, &$importedSuppliers) {
            foreach ($suppliers as $sup) {
                if (empty($sup['name'])) continue;
                $model = !empty($sup['id']) ? Supplier::find($sup['id']) : Supplier::where('name', $sup['name'])->first();
                if (!$model) {
                    $model = new Supplier();
                    if (!empty($sup['id'])) {
                        $model->id = $sup['id'];
                    }
                }
                $model->name = $sup['name'];
                $model->contact_person = $sup['contact_person'] ?? null;
                $model->phone = $sup['phone'] ?? null;
                $model->email = $sup['email'] ?? null;
                $model->address = $sup['address'] ?? null;
                $model->save();
                $importedSuppliers++;
            }
        });

        // 3. Items
        Item::withoutEvents(function () use ($items, &$importedItems) {
            foreach ($items as $it) {
                if (empty($it['name'])) continue;
                $categoryId = $it['category_id'] ?? null;
                if (!$categoryId && !empty($it['category_name'])) {
                    $cat = Category::firstOrCreate(
                        ['name' => $it['category_name']],
                        ['slug' => \Illuminate\Support\Str::slug($it['category_name'])]
                    );
                    $categoryId = $cat->id;
                }
                if (!$categoryId) {
                    $cat = Category::first();
                    $categoryId = $cat?->id;
                }

                $model = !empty($it['id']) ? Item::find($it['id']) : Item::where('name', $it['name'])->first();
                if (!$model) {
                    $model = new Item();
                    if (!empty($it['id'])) {
                        $model->id = $it['id'];
                    }
                }
                $model->category_id = $categoryId;
                $model->name = $it['name'];
                $model->unit = $it['unit'] ?? 'pcs';
                $model->minimum_stock = $it['minimum_stock'] ?? 5;
                $model->current_stock = $it['current_stock'] ?? 0;
                $model->cost_price = $it['cost_price'] ?? 0;
                $model->description = $it['description'] ?? null;
                $model->save();
                $importedItems++;
            }
        });

        // 4. Transactions
        $defaultUserId = \App\Models\User::first()?->id ?? 1;
        StockTransaction::withoutEvents(function () use ($transactions, $defaultUserId, &$importedTransactions) {
            foreach ($transactions as $tx) {
                if (empty($tx['transaction_code'])) continue;
                $model = StockTransaction::where('transaction_code', $tx['transaction_code'])->first();
                if (!$model) {
                    $model = new StockTransaction();
                    $model->transaction_code = $tx['transaction_code'];
                }
                $model->type = $tx['type'] ?? 'in';
                $model->date = $tx['date'] ?? now()->toDateString();
                $model->user_id = $defaultUserId;
                $model->destination_or_reason = $tx['destination_or_reason'] ?? '';
                $model->total_cost = $tx['total_cost'] ?? 0;
                $model->save();
                $importedTransactions++;
            }
        });

        return [
            'categories' => $importedCategories,
            'suppliers' => $importedSuppliers,
            'items' => $importedItems,
            'transactions' => $importedTransactions,
        ];
    }

    public function syncAll(): array
    {
        $items = Item::with('category')->get();
        $categories = Category::all();
        $suppliers = Supplier::all();
        $transactions = StockTransaction::all();

        $cCount = 0;
        foreach ($categories as $c) {
            if ($this->syncCategory($c)) $cCount++;
        }

        $sCount = 0;
        foreach ($suppliers as $s) {
            if ($this->syncSupplier($s)) $sCount++;
        }

        $iCount = 0;
        foreach ($items as $i) {
            if ($this->syncItem($i)) $iCount++;
        }

        $tCount = 0;
        foreach ($transactions as $t) {
            if ($this->syncTransaction($t)) $tCount++;
        }

        return [
            'categories' => $cCount,
            'suppliers' => $sCount,
            'items' => $iCount,
            'transactions' => $tCount,
        ];
    }
}

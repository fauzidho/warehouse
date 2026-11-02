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

<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::withCount('stockTransactions');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('suppliers.index', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
        ]);

        Supplier::create($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Data supplier berhasil ditambahkan.');
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Data supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->stockTransactions()->count() > 0) {
            return back()->with('error', 'Supplier ini tidak dapat dihapus karena sudah memiliki riwayat transaksi penerimaan barang.');
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier berhasil dihapus.');
    }
}

<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\StockOutController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Stock In (Penerimaan Bahan) - Admin & Pegawai
    Route::resource('stock-in', StockInController::class)->only(['index', 'create', 'store', 'show']);

    // Stock Out (Pemakaian Bar / Dapur / Waste) - Admin & Pegawai
    Route::resource('stock-out', StockOutController::class)->only(['index', 'create', 'store', 'show']);

    // Stock Adjustment (Opname Fisik) - Admin & Pegawai
    Route::resource('adjustments', StockAdjustmentController::class)->only(['index', 'create', 'store', 'show']);

    // Reports - Admin & Pegawai
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/print', [ReportController::class, 'print'])->name('reports.print');

    // Items Management (Create, Edit, Delete) - Admin only
    Route::middleware('role:admin')->group(function () {
        Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
        Route::post('/items', [ItemController::class, 'store'])->name('items.store');
        Route::get('/items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit');
        Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update');
        Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');
    });

    // Items: Viewable by everyone authenticated
    Route::get('/items', [ItemController::class, 'index'])->name('items.index');
    Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show');

    // Admin-Only Routes for Masters
    Route::middleware('role:admin')->group(function () {
        // Master Categories
        Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);

        // Master Suppliers
        Route::resource('suppliers', SupplierController::class)->only(['index', 'store', 'update', 'destroy']);

        // User Management
        Route::resource('users', UserController::class)->only(['index', 'store', 'update', 'destroy']);

        // Firebase Cloud Center & Sync
        Route::get('/firebase', [\App\Http\Controllers\FirebaseController::class, 'index'])->name('firebase.index');
        Route::get('/firebase/export', [\App\Http\Controllers\FirebaseController::class, 'exportData'])->name('firebase.export');
        Route::post('/firebase/import', [\App\Http\Controllers\FirebaseController::class, 'importData'])->name('firebase.import');
    });
});

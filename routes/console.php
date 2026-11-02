<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('firebase:pull', function (\App\Services\FirestoreService $firestore) {
    $this->info('Mengambil data dari Google Cloud Firestore...');
    $res = $firestore->pullFromFirestore();
    $this->info("Sinkronisasi Selesai!");
    $this->line("- Kategori: {$res['categories']}");
    $this->line("- Supplier: {$res['suppliers']}");
    $this->line("- Bahan Baku: {$res['items']}");
    $this->line("- Transaksi: {$res['transactions']}");
})->purpose('Tarik data Cloud Firestore ke database lokal SQLite');

Artisan::command('firebase:push', function (\App\Services\FirestoreService $firestore) {
    $this->info('Mengunggah data lokal SQLite ke Cloud Firestore...');
    $res = $firestore->syncAll();
    $this->info("Unggah Selesai!");
    $this->line("- Kategori: {$res['categories']}");
    $this->line("- Supplier: {$res['suppliers']}");
    $this->line("- Bahan Baku: {$res['items']}");
    $this->line("- Transaksi: {$res['transactions']}");
})->purpose('Unggah data lokal SQLite ke Cloud Firestore');

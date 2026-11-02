<?php

// 1. Siapkan direktori writable di /tmp untuk sistem file serverless Vercel (read-only)
$directories = [
    '/tmp/storage/app/public',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/cache',
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// 2. Siapkan SQLite Database di /tmp
$sourceDb = __DIR__ . '/../database/database.sqlite';
$targetDb = '/tmp/database.sqlite';

if (!file_exists($targetDb)) {
    if (file_exists($sourceDb) && filesize($sourceDb) > 0) {
        copy($sourceDb, $targetDb);
    } else {
        touch($targetDb);
    }
    chmod($targetDb, 0666);
}

// 3. Konfigurasi Environment khusus Serverless Vercel
$envOverrides = [
    'LARAVEL_STORAGE_PATH' => '/tmp/storage',
    'VIEW_COMPILED_PATH'   => '/tmp/storage/framework/views',
    'DB_CONNECTION'        => 'sqlite',
    'DB_DATABASE'          => $targetDb,
    'SESSION_DRIVER'       => 'file',
    'CACHE_STORE'          => 'file',
    'LOG_CHANNEL'          => 'stderr',
];

foreach ($envOverrides as $key => $value) {
    putenv("{$key}={$value}");
    $_ENV[$key]    = $value;
    $_SERVER[$key] = $value;
}

// 4. Teruskan request ke Laravel public entrypoint
require __DIR__ . '/../public/index.php';

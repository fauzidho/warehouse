<?php

// Prepare writable directories in /tmp for Vercel's serverless read-only filesystem
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

// Runtime temporary SQLite cache in /tmp
$targetDb = '/tmp/database.sqlite';
if (!file_exists($targetDb)) {
    touch($targetDb);
    chmod($targetDb, 0666);
}

// Override critical env vars for Vercel serverless environment
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

// Forward Vercel serverless requests to Laravel's public entrypoint
require __DIR__ . '/../public/index.php';

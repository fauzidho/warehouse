<?php

// Prepare writable directories in /tmp for Vercel's serverless read-only filesystem
$directories = [
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

// Runtime temporary SQLite cache in /tmp (All persistent data is stored in Firebase Cloud Firestore)
$targetDb = '/tmp/database.sqlite';
if (!file_exists($targetDb)) {
    touch($targetDb);
    chmod($targetDb, 0666);
}

// Override env vars for Vercel serverless environment
$envOverrides = [
    'DB_DATABASE'    => $targetDb,
    'SESSION_DRIVER' => 'file',       // file-based session in /tmp
    'CACHE_STORE'    => 'file',       // file-based cache in /tmp
    'LOG_CHANNEL'    => 'stderr',     // log to stderr (visible in Vercel logs)
    'APP_ENV'        => 'production',
];

foreach ($envOverrides as $key => $value) {
    putenv("{$key}={$value}");
    $_ENV[$key]    = $value;
    $_SERVER[$key] = $value;
}

// Forward Vercel serverless requests to Laravel's public entrypoint
require __DIR__ . '/../public/index.php';

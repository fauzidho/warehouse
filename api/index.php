<?php

// Prepare writable directories in /tmp for Vercel's serverless read-only filesystem
$directories = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
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

putenv("DB_DATABASE={$targetDb}");
$_ENV['DB_DATABASE'] = $targetDb;

// Forward Vercel serverless requests to Laravel's public entrypoint
require __DIR__ . '/../public/index.php';

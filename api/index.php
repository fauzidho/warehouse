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

// Ensure SQLite database file is writable in /tmp
$sourceDb = __DIR__ . '/../database/database.sqlite';
$targetDb = '/tmp/database.sqlite';

if (file_exists($sourceDb) && !file_exists($targetDb)) {
    copy($sourceDb, $targetDb);
    chmod($targetDb, 0666);
} elseif (!file_exists($targetDb)) {
    touch($targetDb);
    chmod($targetDb, 0666);
}

putenv("DB_DATABASE={$targetDb}");
$_ENV['DB_DATABASE'] = $targetDb;

// Forward Vercel serverless requests to Laravel's public entrypoint
require __DIR__ . '/../public/index.php';

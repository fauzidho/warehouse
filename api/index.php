<?php

// Tampilkan semua error agar tidak muncul generic HTTP 500
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// 1. Siapkan direktori writable di /tmp untuk sistem file serverless Vercel (read-only)
$directories = [
    '/tmp/storage/app/public',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// 2. Siapkan SQLite Database di /tmp (harus writable agar SQLite tidak throw readonly error)
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

// 3. Fallback Environment Variables jika belum diatur di Vercel Dashboard
$defaultEnv = [
    'APP_KEY'              => 'base64:rHl2ufnNTStvITWLAuDfEjhEUElWt7KlZiOSFNRKSAs=',
    'APP_DEBUG'            => 'true',
    'APP_ENV'              => 'production',
    'LARAVEL_STORAGE_PATH' => '/tmp/storage',
    'VIEW_COMPILED_PATH'   => '/tmp/storage/framework/views',
    'DB_CONNECTION'        => 'sqlite',
    'DB_DATABASE'          => $targetDb,
    'SESSION_DRIVER'       => 'file',
    'CACHE_STORE'          => 'file',
    'LOG_CHANNEL'          => 'stderr',
];

foreach ($defaultEnv as $key => $value) {
    // Jika belum diset di Vercel env, gunakan fallback
    if (!getenv($key)) {
        putenv("{$key}={$value}");
        $_ENV[$key]    = $value;
        $_SERVER[$key] = $value;
    } else {
        // Khusus storage & db wajib tetap di /tmp
        if (in_array($key, ['LARAVEL_STORAGE_PATH', 'VIEW_COMPILED_PATH', 'DB_DATABASE'])) {
            putenv("{$key}={$value}");
            $_ENV[$key]    = $value;
            $_SERVER[$key] = $value;
        }
    }
}

// 4. Eksekusi Laravel dengan Try-Catch untuk menampilkan error detail jika gagal
try {
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    http_response_code(500);
    header('Content-Type: text/html; charset=utf-8');
    echo '<div style="font-family:ui-sans-serif,system-ui,sans-serif;padding:2.5rem;background:#09090b;color:#f4f4f5;min-height:100vh;box-sizing:border-box;">';
    echo '<h1 style="color:#f43f5e;font-size:1.5rem;margin:0 0 1rem;font-weight:700;">Laravel Serverless Startup Exception</h1>';
    echo '<div style="background:#18181b;border:1px solid #27272a;border-radius:0.75rem;padding:1.25rem;margin-bottom:1.5rem;">';
    echo '<p style="margin:0 0 0.5rem;font-size:1.1rem;font-weight:600;color:#fb7185;">' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p style="margin:0;color:#a1a1aa;font-size:0.875rem;"><strong>File:</strong> ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</p>';
    echo '</div>';
    echo '<h2 style="font-size:1rem;color:#d4d4d8;margin-bottom:0.5rem;">Stack Trace:</h2>';
    echo '<pre style="background:#18181b;border:1px solid #27272a;padding:1.25rem;border-radius:0.75rem;overflow:auto;font-size:0.8rem;line-height:1.5;color:#a1a1aa;">' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    echo '</div>';
    exit;
}


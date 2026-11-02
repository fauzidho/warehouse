<?php

// Ensure /tmp storage directory exists for view compilation on Vercel serverless
if (!is_dir('/tmp/views')) {
    mkdir('/tmp/views', 0755, true);
}

// Forward Vercel serverless requests to Laravel's public/index.php
require __DIR__ . '/../public/index.php';

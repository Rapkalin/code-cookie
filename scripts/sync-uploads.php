<?php

require __DIR__ . '/../website/vendor/autoload.php';

use Dotenv\Dotenv;

// Only allowed for cli
if (PHP_SAPI !== 'cli') {
    die('Not allowed');
}

$start = microtime(true);

// Load .env data
// The .env lives in the docroot on the server (a symlink to shared/.env) and at
// the repository root in local development.
$envDir = file_exists(__DIR__ . '/../website/.env') ? __DIR__ . '/../website' : __DIR__ . '/..';
$dotenv = Dotenv::createUnsafeImmutable($envDir, '.env');
$dotenv->safeLoad();

// Copy uploads folder on current machine
exec('scp -r ' . getenv('PROD_USER') . '@' . getenv('PROD_HOST') . ':code/website/app/uploads website/app/');

echo 'execution time ' . round(microtime(true) - $start, 2) . ' seconds.';

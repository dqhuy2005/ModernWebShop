<?php

// Display all errors for debugging
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Create writable directories in /tmp for Vercel
$tmpDirs = [
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/views',
    '/tmp/views',
    '/tmp/cache',
];

foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Set Laravel storage path to /tmp for Vercel
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/views';

// Load the Composer autoloader
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    die('Composer autoload file not found. Run: composer install');
}
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel application
if (!file_exists(__DIR__ . '/../bootstrap/app.php')) {
    die('Laravel bootstrap file not found.');
}
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Handle the request
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);

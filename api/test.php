<?php
echo "PHP is working!<br>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Current directory: " . __DIR__ . "<br>";
echo "Vendor exists: " . (file_exists(__DIR__ . '/../vendor/autoload.php') ? 'YES' : 'NO') . "<br>";
echo "Bootstrap exists: " . (file_exists(__DIR__ . '/../bootstrap/app.php') ? 'YES' : 'NO') . "<br>";
echo "Public index.php exists: " . (file_exists(__DIR__ . '/../public/index.php') ? 'YES' : 'NO') . "<br>";

// Try to load autoloader
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
    echo "Autoloader loaded successfully!<br>";

    // Check if Laravel bootstrap works
    if (file_exists(__DIR__ . '/../bootstrap/app.php')) {
        try {
            $app = require_once __DIR__ . '/../bootstrap/app.php';
            echo "Laravel bootstrap successful!<br>";
            echo "App Name: " . config('app.name') . "<br>";
            echo "App URL: " . config('app.url') . "<br>";
            echo "App Debug: " . (config('app.debug') ? 'true' : 'false') . "<br>";
        } catch (Exception $e) {
            echo "Laravel bootstrap error: " . $e->getMessage() . "<br>";
            echo "Stack trace: <pre>" . $e->getTraceAsString() . "</pre>";
        }
    }
}

// List environment variables
echo "<br><strong>Environment Variables:</strong><br>";
echo "APP_KEY: " . (getenv('APP_KEY') ? 'Set' : 'NOT SET') . "<br>";
echo "APP_ENV: " . (getenv('APP_ENV') ?: 'not set') . "<br>";
echo "APP_DEBUG: " . (getenv('APP_DEBUG') ?: 'not set') . "<br>";
echo "VERCEL: " . (getenv('VERCEL') ?: 'not set') . "<br>";

<?php

echo "<h1>Vercel PHP Diagnostic</h1>";

try {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    require __DIR__.'/../vendor/autoload.php';
    echo "✅ Autoloader loaded!<br>";

    /** @var \Illuminate\Foundation\Application $app */
    $app = require_once __DIR__.'/../bootstrap/app.php';
    echo "✅ Laravel App bootstrapped!<br>";

    $app->handleRequest(\Illuminate\Http\Request::capture());
    echo "✅ Request handled!";
} catch (\Throwable $e) {
    echo "<h2>❌ Laravel Boot Error</h2>";
    echo "<b>Message:</b> " . $e->getMessage() . "<br>";
    echo "<b>File:</b> " . $e->getFile() . ":" . $e->getLine() . "<br>";
    echo "<h3>Stack Trace:</h3><pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr><h3>Environment Check</h3>";
echo "<b>DB_CONNECTION:</b> " . (env('DB_CONNECTION') ?: 'NOT SET') . "<br>";
echo "<b>APP_KEY:</b> " . (env('APP_KEY') ?: 'NOT SET') . "<br>";
echo "<b>PHP Version:</b> " . PHP_VERSION . "<br>";

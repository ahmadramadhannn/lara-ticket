<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "🐘 DEBUG: Script started<br>";

// Register shutdown function to catch hidden fatal errors
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== NULL && ($error['type'] === E_ERROR || $error['type'] === E_PARSE || $error['type'] === E_CORE_ERROR || $error['type'] === E_COMPILE_ERROR)) {
        echo "<h1>❌ Fatal Shutdown Error Detected</h1>";
        echo "<b>Type:</b> " . $error['type'] . "<br>";
        echo "<b>Message:</b> " . htmlspecialchars($error['message']) . "<br>";
        echo "<b>File:</b> " . $error['file'] . ":" . $error['line'] . "<br>";
    }
});

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

try {
    echo "🐘 DEBUG: Loading autoloader...<br>";
    require __DIR__.'/../vendor/autoload.php';

    echo "🐘 DEBUG: Bootstrapping application...<br>";
    $app = require_once __DIR__.'/../bootstrap/app.php';

    echo "🐘 DEBUG: Handling request...<br>";
    $app->handleRequest(Request::capture());
    
    echo "🐘 DEBUG: Script finished successfully.<br>";
} catch (\Throwable $e) {
    echo "<h1>❌ Caught Throwable</h1>";
    echo "<b>Type:</b> " . get_class($e) . "<br>";
    echo "<b>Message:</b> " . htmlspecialchars($e->getMessage()) . "<br>";
    echo "<b>File:</b> " . $e->getFile() . ":" . $e->getLine() . "<br>";
    echo "<h3>Trace:</h3><pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

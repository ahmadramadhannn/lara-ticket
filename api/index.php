<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Core Diagnostic: Catch the original error before Laravel's handler tries (and fails) to render a view
set_exception_handler(function ($e) {
    http_response_code(500);
    echo "<h1>❌ Original Laravel Boot Error</h1>";
    echo "<b>Type:</b> " . get_class($e) . "<br>";
    echo "<b>Message:</b> " . htmlspecialchars($e->getMessage()) . "<br>";
    echo "<b>File:</b> " . $e->getFile() . ":" . $e->getLine() . "<br>";
    echo "<h3>Full Trace:</h3><pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    exit;
});

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->handleRequest(Request::capture());

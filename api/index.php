<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');
error_log("🐘 api/index.php: Bootstrap starting...");

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Register the Composer autoloader...
error_log("🐘 api/index.php: Loading autoloader...");
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
error_log("🐘 api/index.php: Bootstrapping Laravel...");
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

error_log("🐘 api/index.php: Handling request...");
$app->handleRequest(Request::capture());
error_log("🐘 api/index.php: Request handled.");

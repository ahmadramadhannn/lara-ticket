<?php
echo "<h1>Vercel PHP Diagnostic</h1>";
echo "<b>Current Directory:</b> " . __DIR__ . "<br>";
echo "<b>Parent Directory Content:</b> " . implode(', ', scandir(__DIR__ . '/..')) . "<br>";

$autoload = __DIR__ . '/../vendor/autoload.php';
echo "<b>Checking for:</b> $autoload<br>";

if (file_exists($autoload)) {
    echo "✅ Autoloader found!<br>";
    require $autoload;
    echo "✅ Autoloader loaded!<br>";
} else {
    echo "❌ Autoloader NOT found!<br>";
}

echo "<b>PHP Version:</b> " . PHP_VERSION . "<br>";
echo "<b>Environment:</b><pre>";
print_r($_ENV);
echo "</pre>";

<?php
// Test loading index.php step by step
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Testing index.php components</h1>";

// Step 1: Basic PHP
echo "<p>✓ PHP is working</p>";

// Step 2: Define ROOT_PATH
if (! defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR);
}
echo "<p>✓ ROOT_PATH defined: " . ROOT_PATH . "</p>";

// Step 3: Define PHPMYADMIN
define('PHPMYADMIN', true);
echo "<p>✓ PHPMYADMIN defined</p>";

// Step 4: Load constants.php
echo "<p>Loading constants.php...</p>";
try {
    require_once ROOT_PATH . 'libraries/constants.php';
    echo "<p>✓ constants.php loaded</p>";
} catch (Throwable $e) {
    echo "<p style='color:red'>✗ Error loading constants.php: " . $e->getMessage() . "</p>";
    exit;
}

// Step 5: Check AUTOLOAD_FILE
if (!defined('AUTOLOAD_FILE')) {
    echo "<p style='color:red'>✗ AUTOLOAD_FILE not defined</p>";
    exit;
}
echo "<p>✓ AUTOLOAD_FILE: " . AUTOLOAD_FILE . "</p>";

// Step 6: Load autoload
echo "<p>Loading autoload.php...</p>";
try {
    require AUTOLOAD_FILE;
    echo "<p>✓ autoload.php loaded</p>";
} catch (Throwable $e) {
    echo "<p style='color:red'>✗ Error loading autoload.php: " . $e->getMessage() . "</p>";
    exit;
}

// Step 7: Check classes
echo "<p>Checking classes...</p>";
if (class_exists('PhpMyAdmin\Common')) {
    echo "<p>✓ Common class exists</p>";
} else {
    echo "<p style='color:red'>✗ Common class not found</p>";
    exit;
}

if (class_exists('PhpMyAdmin\Routing')) {
    echo "<p>✓ Routing class exists</p>";
} else {
    echo "<p style='color:red'>✗ Routing class not found</p>";
    exit;
}

// Step 8: Try Common::run()
echo "<p>Calling Common::run()...</p>";
try {
    global $route, $containerBuilder, $request;
    PhpMyAdmin\Common::run();
    echo "<p>✓ Common::run() completed</p>";
} catch (Throwable $e) {
    echo "<p style='color:red'>✗ Error in Common::run(): " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    exit;
}

echo "<h2 style='color:green'>All tests passed!</h2>";
echo "<p><a href='index.php'>Try index.php now</a></p>";

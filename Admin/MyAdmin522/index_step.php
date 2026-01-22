<?php
/**
 * Step-by-step execution of index.php to find where it fails
 */
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Disable output buffering
if (ob_get_level()) {
    ob_end_clean();
}

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>phpMyAdmin Debug</title></head><body>";
echo "<h1>phpMyAdmin Step-by-Step Debug</h1>";
echo "<pre style='background:#f0f0f0;padding:10px;font-family:monospace;'>";

function step($msg) {
    echo "\n[STEP] $msg\n";
    flush();
    if (ob_get_level() > 0) {
        ob_flush();
    }
}

try {
    step("1. Starting...");
    step("   ✓ PHP is working");
    
    // Step 2: use statements
    step("2. Processing use statements...");
    use PhpMyAdmin\Common;
    use PhpMyAdmin\Routing;
    step("   ✓ use statements OK");
    
    // Step 3: ROOT_PATH
    step("3. Defining ROOT_PATH...");
    if (! defined('ROOT_PATH')) {
        define('ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR);
    }
    step("   ✓ ROOT_PATH = " . ROOT_PATH);
    
    // Step 4: PHP version check
    step("4. Checking PHP version...");
    if (PHP_VERSION_ID < 70205) {
        die('<p>PHP 7.2.5+ is required.</p><p>Currently installed version is: ' . PHP_VERSION . '</p>');
    }
    step("   ✓ PHP version OK: " . PHP_VERSION);
    
    // Step 5: PHPMYADMIN constant
    step("5. Defining PHPMYADMIN constant...");
    define('PHPMYADMIN', true);
    step("   ✓ PHPMYADMIN defined");
    
    // Step 6: Load constants.php
    step("6. Loading libraries/constants.php...");
    $constantsFile = ROOT_PATH . 'libraries/constants.php';
    step("   File: $constantsFile");
    step("   Exists: " . (file_exists($constantsFile) ? 'Yes' : 'No'));
    step("   Readable: " . (is_readable($constantsFile) ? 'Yes' : 'No'));
    
    require_once $constantsFile;
    step("   ✓ constants.php loaded");
    
    // Step 7: Check AUTOLOAD_FILE
    step("7. Checking AUTOLOAD_FILE...");
    if (!defined('AUTOLOAD_FILE')) {
        throw new Exception("AUTOLOAD_FILE not defined");
    }
    step("   AUTOLOAD_FILE = " . AUTOLOAD_FILE);
    step("   Exists: " . (file_exists(AUTOLOAD_FILE) ? 'Yes' : 'No'));
    step("   Readable: " . (is_readable(AUTOLOAD_FILE) ? 'Yes' : 'No'));
    
    if (! @is_readable(AUTOLOAD_FILE)) {
        die(
            '<p>File <samp>' . AUTOLOAD_FILE . '</samp> missing or not readable.</p>'
            . '<p>Most likely you did not run Composer to '
            . '<a href="https://docs.phpmyadmin.net/en/latest/setup.html#installing-from-git">'
            . 'install library files</a>.</p>'
        );
    }
    
    // Step 8: Load autoload
    step("8. Loading autoload.php...");
    require AUTOLOAD_FILE;
    step("   ✓ autoload.php loaded");
    
    // Step 9: Global variables
    step("9. Setting up global variables...");
    global $route, $containerBuilder, $request;
    step("   ✓ Globals declared");
    
    // Step 10: Common::run()
    step("10. Calling Common::run()...");
    Common::run();
    step("   ✓ Common::run() completed");
    
    // Step 11: Get dispatcher
    step("11. Getting dispatcher...");
    $dispatcher = Routing::getDispatcher();
    step("   ✓ Dispatcher created");
    
    // Step 12: Call controller
    step("12. Calling controller for route...");
    Routing::callControllerForRoute($request, $route, $dispatcher, $containerBuilder);
    step("   ✓ Controller called");
    
    step("\n[SUCCESS] All steps completed!");
    
} catch (Throwable $e) {
    step("\n[ERROR] Exception caught!");
    step("Type: " . get_class($e));
    step("Message: " . $e->getMessage());
    step("File: " . $e->getFile());
    step("Line: " . $e->getLine());
    step("\nStack Trace:");
    step($e->getTraceAsString());
    
    $lastError = error_get_last();
    if ($lastError) {
        step("\nLast PHP Error:");
        step(print_r($lastError, true));
    }
}

echo "</pre>";
echo "</body></html>";

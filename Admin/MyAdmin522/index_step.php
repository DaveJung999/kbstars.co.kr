<?php
/**
 * Step-by-step execution of index.php to find where it fails
 */
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
    ob_flush();
}

try {
    step("1. Starting...");
    
    // Step 2: declare
    step("2. Processing declare(strict_types=1)...");
    declare(strict_types=1);
    step("   ✓ declare completed");
    
    // Step 3: use statements (these are compile-time, so if we get here, they're OK)
    step("3. Processing use statements...");
    use PhpMyAdmin\Common;
    use PhpMyAdmin\Routing;
    step("   ✓ use statements OK");
    
    // Step 4: ROOT_PATH
    step("4. Defining ROOT_PATH...");
    if (! defined('ROOT_PATH')) {
        define('ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR);
    }
    step("   ✓ ROOT_PATH = " . ROOT_PATH);
    
    // Step 5: PHP version check
    step("5. Checking PHP version...");
    if (PHP_VERSION_ID < 70205) {
        die('<p>PHP 7.2.5+ is required.</p><p>Currently installed version is: ' . PHP_VERSION . '</p>');
    }
    step("   ✓ PHP version OK: " . PHP_VERSION);
    
    // Step 6: PHPMYADMIN constant
    step("6. Defining PHPMYADMIN constant...");
    define('PHPMYADMIN', true);
    step("   ✓ PHPMYADMIN defined");
    
    // Step 7: Load constants.php
    step("7. Loading libraries/constants.php...");
    $constantsFile = ROOT_PATH . 'libraries/constants.php';
    step("   File: $constantsFile");
    step("   Exists: " . (file_exists($constantsFile) ? 'Yes' : 'No'));
    step("   Readable: " . (is_readable($constantsFile) ? 'Yes' : 'No'));
    
    require_once $constantsFile;
    step("   ✓ constants.php loaded");
    
    // Step 8: Check AUTOLOAD_FILE
    step("8. Checking AUTOLOAD_FILE...");
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
    
    // Step 9: Load autoload
    step("9. Loading autoload.php...");
    require AUTOLOAD_FILE;
    step("   ✓ autoload.php loaded");
    
    // Step 10: Global variables
    step("10. Setting up global variables...");
    global $route, $containerBuilder, $request;
    step("   ✓ Globals declared");
    
    // Step 11: Common::run()
    step("11. Calling Common::run()...");
    Common::run();
    step("   ✓ Common::run() completed");
    
    // Step 12: Get dispatcher
    step("12. Getting dispatcher...");
    $dispatcher = Routing::getDispatcher();
    step("   ✓ Dispatcher created");
    
    // Step 13: Call controller
    step("13. Calling controller for route...");
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

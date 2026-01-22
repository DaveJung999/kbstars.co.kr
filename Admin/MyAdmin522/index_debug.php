<?php
/**
 * Debug version of index.php to catch errors
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Set error handler
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    if (error_reporting() & $errno) {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
    return false;
});

// Set exception handler
set_exception_handler(function($exception) {
    echo "<h2 style='color:red'>Uncaught Exception!</h2>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($exception->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($exception->getFile()) . "</p>";
    echo "<p><strong>Line:</strong> " . $exception->getLine() . "</p>";
    echo "<pre>" . htmlspecialchars($exception->getTraceAsString()) . "</pre>";
    exit;
});

// Capture fatal errors
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== NULL && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
        echo "<h2 style='color:red'>Fatal Error!</h2>";
        echo "<p><strong>Type:</strong> " . $error['type'] . "</p>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($error['message']) . "</p>";
        echo "<p><strong>File:</strong> " . htmlspecialchars($error['file']) . "</p>";
        echo "<p><strong>Line:</strong> " . $error['line'] . "</p>";
    }
});

echo "<h1>phpMyAdmin Index Debug</h1>";
echo "<p>Starting index.php execution...</p>";

try {
    declare(strict_types=1);

    use PhpMyAdmin\Common;
    use PhpMyAdmin\Routing;

    if (! defined('ROOT_PATH')) {
        // phpcs:disable PSR1.Files.SideEffects
        define('ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR);
        // phpcs:enable
    }

    echo "<p>✓ ROOT_PATH defined: " . ROOT_PATH . "</p>";

    if (PHP_VERSION_ID < 70205) {
        die('<p>PHP 7.2.5+ is required.</p><p>Currently installed version is: ' . PHP_VERSION . '</p>');
    }

    echo "<p>✓ PHP version check passed</p>";

    // phpcs:disable PSR1.Files.SideEffects
    define('PHPMYADMIN', true);
    // phpcs:enable

    echo "<p>✓ PHPMYADMIN defined</p>";

    echo "<p>Loading constants.php...</p>";
    require_once ROOT_PATH . 'libraries/constants.php';
    echo "<p>✓ constants.php loaded</p>";

    /**
     * Activate autoloader
     */
    if (! @is_readable(AUTOLOAD_FILE)) {
        die(
            '<p>File <samp>' . AUTOLOAD_FILE . '</samp> missing or not readable.</p>'
            . '<p>Most likely you did not run Composer to '
            . '<a href="https://docs.phpmyadmin.net/en/latest/setup.html">'
            . 'install library files</a>.</p>'
        );
    }

    echo "<p>Loading autoload...</p>";
    require AUTOLOAD_FILE;
    echo "<p>✓ autoload.php loaded</p>";

    global $route, $containerBuilder, $request;

    echo "<p>Calling Common::run()...</p>";
    Common::run();
    echo "<p>✓ Common::run() completed</p>";

    echo "<p>Getting dispatcher...</p>";
    $dispatcher = Routing::getDispatcher();
    echo "<p>✓ Dispatcher created</p>";

    echo "<p>Calling controller for route...</p>";
    Routing::callControllerForRoute($request, $route, $dispatcher, $containerBuilder);
    echo "<p>✓ Controller called</p>";

    echo "<h2 style='color:green'>All steps completed successfully!</h2>";
    echo "<p>If you see this message but still get 500 error, the issue might be:</p>";
    echo "<ul>";
    echo "<li>Output buffering issues</li>";
    echo "<li>Header already sent errors</li>";
    echo "<li>Session issues</li>";
    echo "<li>Apache/PHP configuration issues</li>";
    echo "</ul>";

} catch (Throwable $e) {
    echo "<h2 style='color:red'>Error occurred!</h2>";
    echo "<p><strong>Error Type:</strong> " . get_class($e) . "</p>";
    echo "<p><strong>Error Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    echo "<p><strong>Error Code:</strong> " . $e->getCode() . "</p>";
    
    if ($e->getPrevious()) {
        echo "<h3>Previous Exception:</h3>";
        echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getPrevious()->getMessage()) . "</p>";
    }
    
    echo "<h3>Stack Trace:</h3>";
    echo "<pre style='background:#f0f0f0;padding:10px;overflow:auto;max-height:500px;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    
    // Show last error if any
    $lastError = error_get_last();
    if ($lastError) {
        echo "<h3>Last PHP Error:</h3>";
        echo "<pre style='background:#fff0f0;padding:10px;'>";
        print_r($lastError);
        echo "</pre>";
    }
}

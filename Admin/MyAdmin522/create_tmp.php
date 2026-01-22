<?php
/**
 * Create tmp directory for phpMyAdmin
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Create tmp Directory</h1>";

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR);
}

$tmpDir = ROOT_PATH . 'tmp';

echo "<p>Target directory: <code>$tmpDir</code></p>";

if (file_exists($tmpDir)) {
    echo "<p style='color:green'>✓ Directory already exists</p>";
    echo "<p>Is writable: " . (is_writable($tmpDir) ? "<span style='color:green'>Yes</span>" : "<span style='color:red'>No</span>") . "</p>";
    echo "<p>Permissions: " . substr(sprintf('%o', fileperms($tmpDir)), -4) . "</p>";
} else {
    echo "<p>Directory does not exist. Attempting to create...</p>";
    
    if (@mkdir($tmpDir, 0755, true)) {
        echo "<p style='color:green'>✓ Directory created successfully</p>";
        echo "<p>Permissions: " . substr(sprintf('%o', fileperms($tmpDir)), -4) . "</p>";
    } else {
        $error = error_get_last();
        echo "<p style='color:red'>✗ Failed to create directory</p>";
        echo "<p>Error: " . ($error ? $error['message'] : 'Unknown error') . "</p>";
        echo "<p><strong>Please create the directory manually:</strong></p>";
        echo "<pre>mkdir -p $tmpDir\nchmod 755 $tmpDir</pre>";
    }
}

// Test write permission
if (file_exists($tmpDir) && is_writable($tmpDir)) {
    $testFile = $tmpDir . '/test_write.txt';
    if (@file_put_contents($testFile, 'test')) {
        @unlink($testFile);
        echo "<p style='color:green'>✓ Write test successful</p>";
    } else {
        echo "<p style='color:orange'>⚠ Directory exists but not writable</p>";
        echo "<p>Please run: <code>chmod 755 $tmpDir</code></p>";
    }
}

echo "<hr>";
echo "<p><a href='test.php'>Back to test.php</a> | <a href='error_test.php'>Run error_test.php</a></p>";

<?php
/**
 * Cache Clearing Script
 * Run this file directly via browser: https://workflow.synergygroup.com.pk/clear_cache.php
 * Or via command line: php clear_cache.php
 */

// Security check - remove this file after use or add password protection
// Uncomment the line below and set a password
// if (!isset($_GET['key']) || $_GET['key'] !== 'your-secret-key-here') { die('Access denied'); }

define('BASEPATH', true);

// Clear application cache
$cache_path = __DIR__ . '/application/cache/';
if (is_dir($cache_path)) {
    $files = glob($cache_path . '*');
    foreach ($files as $file) {
        if (is_file($file) && basename($file) !== 'index.html') {
            @unlink($file);
        }
    }
    echo "Application cache cleared.<br>";
}

// Clear system cache if exists
$system_cache = __DIR__ . '/system/cache/';
if (is_dir($system_cache)) {
    $files = glob($system_cache . '*');
    foreach ($files as $file) {
        if (is_file($file) && basename($file) !== 'index.html') {
            @unlink($file);
        }
    }
    echo "System cache cleared.<br>";
}

// Clear opcache if enabled
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OPcache cleared.<br>";
}

// Clear any .htaccess cache
if (file_exists(__DIR__ . '/.htaccess')) {
    touch(__DIR__ . '/.htaccess');
    echo ".htaccess touched (cache cleared).<br>";
}

echo "<br><strong>Cache cleared successfully!</strong><br>";
echo "<br>Please delete this file (clear_cache.php) after use for security reasons.";
echo "<br><br><a href='" . (isset($_SERVER['HTTP_HOST']) ? 'https://' . $_SERVER['HTTP_HOST'] : '') . "'>Go to Homepage</a>";


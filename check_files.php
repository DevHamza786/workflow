<?php
/**
 * File Check Script
 * Run this to verify your files are uploaded correctly
 * Access via: https://workflow.synergygroup.com.pk/check_files.php
 */

// Security - remove after checking or add password
// if (!isset($_GET['key']) || $_GET['key'] !== 'your-secret-key-here') { die('Access denied'); }

echo "<h2>File Verification Check</h2>";
echo "<pre>";

$files_to_check = [
    'application/controllers/Home.php',
    'application/controllers/Authentication.php',
    'application/config/routes.php',
    'index.php'
];

foreach ($files_to_check as $file) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        $size = filesize($path);
        $modified = date('Y-m-d H:i:s', filemtime($path));
        echo "✓ $file exists (Size: $size bytes, Modified: $modified)\n";
        
        // Check for key content
        $content = file_get_contents($path);
        if ($file === 'application/controllers/Home.php') {
            if (strpos($content, 'redirect(admin_url(\'authentication\'))') !== false) {
                echo "  → Contains redirect to admin/authentication ✓\n";
            } else {
                echo "  → WARNING: Does not contain expected redirect code!\n";
            }
        }
        if ($file === 'application/config/routes.php') {
            if (strpos($content, '$route[\'default_controller\']') !== false && 
                strpos($content, 'home') !== false) {
                echo "  → Default controller set to 'home' ✓\n";
            } else {
                echo "  → WARNING: Default controller may not be set correctly!\n";
            }
        }
    } else {
        echo "✗ $file NOT FOUND!\n";
    }
    echo "\n";
}

echo "</pre>";
echo "<br><strong>Check complete!</strong><br>";
echo "<br>Please delete this file (check_files.php) after use for security reasons.";


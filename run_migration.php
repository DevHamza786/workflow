<?php
// Run migration to add staff columns
// Access this file via browser: http://workflows.test/run_migration.php

define('ENVIRONMENT', 'development');
require_once('index.php');

$CI =& get_instance();

// Temporarily enable migrations
$CI->config->set_item('migration_enabled', true);
$CI->config->set_item('migration_version', 315);

// Load migration library with config
$CI->load->library('migration', [
    'migration_enabled' => true,
    'migration_type' => 'sequential',
    'migration_table' => 'migrations',
    'migration_auto_latest' => false,
    'migration_version' => 315,
    'migration_path' => APPPATH . 'migrations/'
]);

echo "<h2>Running Migration 315 - Adding Staff Columns</h2>";
echo "<pre>";

// Run migration to version 315
$result = $CI->migration->version(315);

if ($result === FALSE) {
    echo "✗ Migration Error: " . $CI->migration->error_string() . "\n";
} else {
    echo "✓ Migration completed successfully!\n";
    echo "\nAdded columns to tblstaff table:\n";
    echo "- company (VARCHAR 255, NULL)\n";
    echo "- designation (VARCHAR 255, NULL)\n";
    echo "- department (VARCHAR 255, NULL)\n";
    echo "\nYou can now use these fields in the staff form!\n";
}

echo "\nDone! You can now close this page.\n";
echo "</pre>";


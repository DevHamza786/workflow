<?php
// Script to add company, designation, and department columns to staff table
// Run this file once: http://workflows.test/add_staff_columns.php

// Load CodeIgniter
define('ENVIRONMENT', 'development');
require_once('index.php');

// Get database connection
$CI =& get_instance();
$CI->load->database();

$table_name = db_prefix() . 'staff';

// Check if columns already exist and add them
$columns_to_add = [
    'company' => "ALTER TABLE `{$table_name}` ADD COLUMN `company` VARCHAR(255) NULL AFTER `phonenumber`",
    'designation' => "ALTER TABLE `{$table_name}` ADD COLUMN `designation` VARCHAR(255) NULL AFTER `company`",
    'department' => "ALTER TABLE `{$table_name}` ADD COLUMN `department` VARCHAR(255) NULL AFTER `designation`"
];

echo "<h2>Adding Staff Table Columns</h2>";
echo "<pre>";

$success_count = 0;
$skip_count = 0;
$error_count = 0;

foreach ($columns_to_add as $column_name => $sql) {
    // Check if column exists
    $check_sql = "SHOW COLUMNS FROM `{$table_name}` LIKE '{$column_name}'";
    $result = $CI->db->query($check_sql);
    
    if ($result->num_rows() > 0) {
        echo "Column '{$column_name}' already exists. Skipping...\n";
        $skip_count++;
    } else {
        try {
            $CI->db->query($sql);
            echo "✓ Successfully added column '{$column_name}'\n";
            $success_count++;
        } catch (Exception $e) {
            echo "✗ Error adding column '{$column_name}': " . $e->getMessage() . "\n";
            $error_count++;
        }
    }
}

echo "\n";
echo "Summary:\n";
echo "- Successfully added: {$success_count} columns\n";
echo "- Already existed: {$skip_count} columns\n";
echo "- Errors: {$error_count} columns\n";
echo "\nDone! You can now close this page.\n";
echo "</pre>";


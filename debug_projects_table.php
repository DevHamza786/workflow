<?php
/**
 * Debug script for Projects Table DataTables error
 * Access via: https://workflow.synergygroup.com.pk/debug_projects_table.php
 * 
 * SECURITY: Delete this file after debugging!
 */

// if (!isset($_GET['key']) || $_GET['key'] !== 'your-secret-key-here') { die('Access denied'); }

define('BASEPATH', true);
require_once(__DIR__ . '/index.php');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start output buffering
ob_start();

try {
    // Simulate the table request
    $_POST['draw'] = 1;
    $_POST['start'] = 0;
    $_POST['length'] = 10;
    
    $CI = &get_instance();
    $CI->load->library('App_table');
    
    // Try to get the table data
    $table = App_table::find('projects');
    
    echo "<h2>Projects Table Debug</h2>";
    echo "<pre>";
    echo "Table found: " . ($table ? "Yes" : "No") . "\n";
    
    if ($table) {
        echo "Table ID: " . $table->id() . "\n";
        echo "Output closure exists: " . (is_callable($table->outputUsing) ? "Yes" : "No") . "\n";
        
        // Try to execute the output
        $params = ['customFieldsColumns' => []];
        $closure = $table->outputUsing;
        $closure = $closure->bindTo($table);
        
        echo "\nAttempting to execute closure...\n";
        $result = $closure($params, $table->rules());
        
        echo "Result type: " . gettype($result) . "\n";
        echo "Result keys: " . (is_array($result) ? implode(', ', array_keys($result)) : 'N/A') . "\n";
        
        $json = json_encode($result);
        if ($json === false) {
            echo "JSON Error: " . json_last_error_msg() . "\n";
        } else {
            echo "JSON length: " . strlen($json) . " bytes\n";
            echo "First 500 chars: " . substr($json, 0, 500) . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
} catch (Error $e) {
    echo "FATAL ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

$output = ob_get_clean();
echo $output;
echo "</pre>";

echo "<br><br><strong>Please delete this file after debugging!</strong>";


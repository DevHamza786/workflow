<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_316 extends CI_Migration
{
    public function up()
    {
        // Add task_type and task_nature columns to tasks table
        $table_name = db_prefix() . 'tasks';

        // Check if task_type column already exists before adding
        if (!$this->db->field_exists('task_type', $table_name)) {
            $this->db->query('ALTER TABLE `' . $table_name . '` ADD COLUMN `task_type` VARCHAR(50) NULL DEFAULT NULL AFTER `name`;');
        }

        // Check if task_nature column already exists before adding
        if (!$this->db->field_exists('task_nature', $table_name)) {
            $this->db->query('ALTER TABLE `' . $table_name . '` ADD COLUMN `task_nature` TEXT NULL DEFAULT NULL AFTER `task_type`;');
        }
    }

    public function down()
    {
        // Remove task_type and task_nature columns if migration is rolled back
        $table_name = db_prefix() . 'tasks';
        
        if ($this->db->field_exists('task_nature', $table_name)) {
            $this->db->query('ALTER TABLE `' . $table_name . '` DROP COLUMN `task_nature`;');
        }
        
        if ($this->db->field_exists('task_type', $table_name)) {
            $this->db->query('ALTER TABLE `' . $table_name . '` DROP COLUMN `task_type`;');
        }
    }
}


<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_315 extends CI_Migration
{
    public function up()
    {
        // Use raw SQL queries to avoid double prefix issue
        $table_name = db_prefix() . 'staff';

        // Check and add company column
        if (!$this->db->field_exists('company', $table_name)) {
            $this->db->query('ALTER TABLE `' . $table_name . '` ADD COLUMN `company` VARCHAR(255) NULL AFTER `phonenumber`;');
        }

        // Check and add designation column
        if (!$this->db->field_exists('designation', $table_name)) {
            $this->db->query('ALTER TABLE `' . $table_name . '` ADD COLUMN `designation` VARCHAR(255) NULL AFTER `company`;');
        }

        // Check and add department column
        if (!$this->db->field_exists('department', $table_name)) {
            $this->db->query('ALTER TABLE `' . $table_name . '` ADD COLUMN `department` VARCHAR(255) NULL AFTER `designation`;');
        }
    }

    public function down()
    {
        $table_name = db_prefix() . 'staff';

        // Drop columns if they exist
        if ($this->db->field_exists('company', $table_name)) {
            $this->db->query('ALTER TABLE `' . $table_name . '` DROP COLUMN `company`;');
        }

        if ($this->db->field_exists('designation', $table_name)) {
            $this->db->query('ALTER TABLE `' . $table_name . '` DROP COLUMN `designation`;');
        }

        if ($this->db->field_exists('department', $table_name)) {
            $this->db->query('ALTER TABLE `' . $table_name . '` DROP COLUMN `department`;');
        }
    }
}


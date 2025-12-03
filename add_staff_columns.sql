-- Add company and designation columns to staff table
ALTER TABLE `tblstaff` ADD COLUMN `company` VARCHAR(255) NULL AFTER `phonenumber`;
ALTER TABLE `tblstaff` ADD COLUMN `designation` VARCHAR(255) NULL AFTER `company`;
ALTER TABLE `tblstaff` ADD COLUMN `department` VARCHAR(255) NULL AFTER `designation`;


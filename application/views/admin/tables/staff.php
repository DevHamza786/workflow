<?php

defined('BASEPATH') or exit('No direct script access allowed');

$has_permission_delete = staff_can('delete',  'staff');

$custom_fields = get_custom_fields('staff', [
    'show_on_table' => 1,
    ]);
$aColumns = [
    'firstname',
    'email',
    db_prefix() . 'roles.name as role_name',
    'company',
    'designation',
    '(SELECT GROUP_CONCAT(' . db_prefix() . 'departments.name SEPARATOR ", ") FROM ' . db_prefix() . 'staff_departments LEFT JOIN ' . db_prefix() . 'departments ON ' . db_prefix() . 'staff_departments.departmentid = ' . db_prefix() . 'departments.departmentid WHERE ' . db_prefix() . 'staff_departments.staffid = ' . db_prefix() . 'staff.staffid) as departments',
    'last_login',
    'active',
    ];
$sIndexColumn = 'staffid';
$sTable       = db_prefix() . 'staff';
$join         = ['LEFT JOIN ' . db_prefix() . 'roles ON ' . db_prefix() . 'roles.roleid = ' . db_prefix() . 'staff.role'];
$i            = 0;
foreach ($custom_fields as $field) {
    $select_as = 'cvalue_' . $i;
    if ($field['type'] == 'date_picker' || $field['type'] == 'date_picker_time') {
        $select_as = 'date_picker_cvalue_' . $i;
    }
    array_push($aColumns, 'ctable_' . $i . '.value as ' . $select_as);
    array_push($join, 'LEFT JOIN ' . db_prefix() . 'customfieldsvalues as ctable_' . $i . ' ON ' . db_prefix() . 'staff.staffid = ctable_' . $i . '.relid AND ctable_' . $i . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $i . '.fieldid=' . $field['id']);
    $i++;
}
            // Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
}

$where = hooks()->apply_filters('staff_table_sql_where', []);

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    'profile_image',
    'lastname',
    'staffid',
    db_prefix() . 'staff.role',
    db_prefix() . 'staff.admin',
    ]);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $alias = trim(strafter($aColumns[$i], 'as '));
            $_data = isset($aRow[$alias]) ? $aRow[$alias] : '';
        } else {
            $_data = isset($aRow[$aColumns[$i]]) ? $aRow[$aColumns[$i]] : '';
        }
        if ($aColumns[$i] == 'last_login') {
            if ($_data != null) {
                $_data = '<span class="text-has-action is-date" data-toggle="tooltip" data-title="' . e(_dt($_data)) . '">' . time_ago($_data) . '</span>';
            } else {
                $_data = 'Never';
            }
        } elseif ($aColumns[$i] == 'active') {
            $checked = '';
            if ($aRow['active'] == 1) {
                $checked = 'checked';
            }

            $_data = '<div class="onoffswitch">
                <input type="checkbox" ' . (($aRow['staffid'] == get_staff_user_id() || (is_admin($aRow['staffid']) || staff_cant('edit', 'staff')) && !is_admin()) ? 'disabled' : '') . ' data-switch-url="' . admin_url() . 'staff/change_staff_status" name="onoffswitch" class="onoffswitch-checkbox" id="c_' . $aRow['staffid'] . '" data-id="' . $aRow['staffid'] . '" ' . $checked . '>
                <label class="onoffswitch-label" for="c_' . $aRow['staffid'] . '"></label>
            </div>';

            // For exporting
            $_data .= '<span class="hide">' . ($checked == 'checked' ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';
        } elseif ($aColumns[$i] == 'firstname') {
            $_data = '<a href="' . admin_url('staff/profile/' . $aRow['staffid']) . '">' . staff_profile_image($aRow['staffid'], [
                'staff-profile-image-small',
                ]) . '</a>';
            $_data .= ' <a href="' . admin_url('staff/member/' . $aRow['staffid']) . '">' . e($aRow['firstname'] . ' ' . $aRow['lastname']) . '</a>';

            $_data .= '<div class="row-options">';
            $_data .= '<a href="' . admin_url('staff/member/' . $aRow['staffid']) . '">' . _l('view') . '</a>';

            if (($has_permission_delete && ($has_permission_delete && !is_admin($aRow['staffid']))) || is_admin()) {
                if ($has_permission_delete && $output['iTotalRecords'] > 1 && $aRow['staffid'] != get_staff_user_id()) {
                    $_data .= ' | <a href="#" onclick="delete_staff_member(' . $aRow['staffid'] . '); return false;" class="text-danger">' . _l('delete') . '</a>';
                }
            }

            $_data .= '</div>';
        } elseif ($aColumns[$i] == 'email') {
            $_data = '<a href="mailto:' . e($_data) . '">' . e($_data) . '</a>';
        } elseif (strpos($aColumns[$i], 'role_name') !== false) {
            // Handle role column - override previous $_data value and check directly
            // First check the role_name alias directly from the query result
            $_data = '';
            if (isset($aRow['role_name']) && $aRow['role_name'] !== null && trim($aRow['role_name']) !== '') {
                $_data = trim($aRow['role_name']);
            } else {
                // If no role, check if staff is admin (admin users might not have a role assigned)
                $adminField = db_prefix() . 'staff.admin';
                if (isset($aRow[$adminField]) && $aRow[$adminField] == 1) {
                    $_data = _l('admin');
                } elseif (isset($aRow['admin']) && $aRow['admin'] == 1) {
                    $_data = _l('admin');
                } elseif (isset($aRow['staffid']) && is_admin($aRow['staffid'])) {
                    $_data = _l('admin');
                }
            }
            // Display role or dash if empty
            $_data = e($_data ?: '-');
        } elseif ($aColumns[$i] == 'company') {
            $_data = e($aRow['company'] ?: '-');
        } elseif ($aColumns[$i] == 'designation') {
            $_data = e($aRow['designation'] ?: '-');
        } elseif (strpos($aColumns[$i], 'departments') !== false || $aColumns[$i] == 'department') {
            // Handle department column - check if it's the alias
            if (isset($aRow['departments'])) {
                $_data = e($aRow['departments'] ?: '');
            } else {
                $_data = e($_data ?: '');
            }
        } else {
            if (strpos($aColumns[$i], 'date_picker_') !== false) {
                $_data = (strpos($_data, ' ') !== false ? _dt($_data) : _d($_data));
            }
        }
        $row[] = $_data;
    }

    $row['DT_RowClass'] = 'has-row-options';

    $row = hooks()->apply_filters('staff_table_row', $row, $aRow);

    $output['aaData'][] = $row;
}

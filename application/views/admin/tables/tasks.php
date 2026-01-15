<?php

defined('BASEPATH') or exit('No direct script access allowed');

return App_table::find('tasks')
    ->outputUsing(function ($params) {
        extract($params);
        $hasPermissionEdit   = staff_can('edit',  'tasks');
        $hasPermissionDelete = staff_can('delete',  'tasks');
        $tasksPriorities     = get_tasks_priorities();
        $task_statuses = $this->ci->tasks_model->get_statuses();

        $aColumns = [
            '1', // bulk actions
            db_prefix() . 'tasks.id as id',
            db_prefix() . 'tasks.name as task_name',
            'status',
            'startdate',
            'duedate',
            get_sql_select_task_asignees_full_names() . ' as assignees',
        ];

        $sIndexColumn = 'id';
        $sTable       = db_prefix() . 'tasks';

        $where = [];
        $join  = [];

        if ($filtersWhere = $this->getWhereFromRules()) {
            $where[] = $filtersWhere;
        }
                
        if (staff_cant('view', 'tasks')) {
            $where[] = get_tasks_where_string();
        }

        // Dashboard my tasks table
        if($this->ci->input->post('my_tasks')) {
            $where[] = 'AND (' . db_prefix() . 'tasks.id IN (SELECT taskid FROM ' . db_prefix() . 'task_assigned WHERE staffid = ' . get_staff_user_id() . ') AND status != '.Tasks_model::STATUS_COMPLETE.')';
        }

        array_push($where, 'AND CASE WHEN rel_type="project" AND rel_id IN (SELECT project_id FROM ' . db_prefix() . 'project_settings WHERE project_id=rel_id AND name="hide_tasks_on_main_tasks_table" AND value=1) THEN rel_type != "project" ELSE 1=1 END');

        $aColumns = hooks()->apply_filters('tasks_table_sql_columns', $aColumns);

        $result = data_tables_init(
            $aColumns,
            $sIndexColumn,
            $sTable,
            $join,
            $where,
            [
                'rel_type',
                'rel_id',
                'recurring',
                tasks_rel_name_select_query() . ' as rel_name',
                'billed',
                '(SELECT staffid FROM ' . db_prefix() . 'task_assigned WHERE taskid=' . db_prefix() . 'tasks.id AND staffid=' . get_staff_user_id() . ') as is_assigned',
                get_sql_select_task_assignees_ids() . ' as assignees_ids',
                '(SELECT staffid FROM ' . db_prefix() . 'task_assigned WHERE taskid=' . db_prefix() . 'tasks.id AND staffid=' . get_staff_user_id() . ') as current_user_is_assigned',
                '(SELECT CASE WHEN addedfrom=' . get_staff_user_id() . ' AND is_added_from_contact=0 THEN 1 ELSE 0 END) as current_user_is_creator',
            ]
        );

        $output  = $result['output'];
        $rResult = $result['rResult'];

        foreach ($rResult as $aRow) {
            $row = [];

            $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';

            $row[] = '<a href="' . admin_url('tasks/view/' . $aRow['id']) . '" onclick="init_task_modal(' . $aRow['id'] . '); return false;">' . $aRow['id'] . '</a>';

            $outputName = '';

            $outputName .= '<a href="' . admin_url('tasks/view/' . $aRow['id']) . '" class="display-block main-tasks-table-href-name' . (!empty($aRow['rel_id']) ? ' mbot5' : '') . '" onclick="init_task_modal(' . $aRow['id'] . '); return false;">' . e($aRow['task_name']) . '</a>';

            if ($aRow['rel_name']) {
                $relName = task_rel_name($aRow['rel_name'], $aRow['rel_id'], $aRow['rel_type']);

                $link = task_rel_link($aRow['rel_id'], $aRow['rel_type']);

                $outputName .= '<span class="hide"> - </span><a class="tw-text-neutral-700 task-table-related tw-text-sm" data-toggle="tooltip" title="' . _l('task_related_to') . '" href="' . $link . '">' . e($relName) . '</a>';
            }

            if ($aRow['recurring'] == 1) {
                $outputName .= '<br /><span class="label label-primary inline-block mtop4"> ' . _l('recurring_task') . '</span>';
            }

            $outputName .= '<div class="row-options">';

            if ($hasPermissionEdit) {
                $outputName .= '<span class="tw-text-neutral-300"> | </span><a href="#" onclick="edit_task(' . $aRow['id'] . '); return false">' . _l('edit') . '</a>';
            }

            if ($hasPermissionDelete) {
                $outputName .= '<span class="tw-text-neutral-300"> | </span><a href="' . admin_url('tasks/delete_task/' . $aRow['id']) . '" class="text-danger _delete task-delete">' . _l('delete') . '</a>';
            }
            $outputName .= '</div>';

            $row[] = $outputName;

            $canChangeStatus = ($aRow['current_user_is_creator'] != '0' || $aRow['current_user_is_assigned'] || staff_can('edit',  'tasks'));
            $status          = get_task_status_by_id($aRow['status']);
            $outputStatus    = '';

            $outputStatus .= '<span class="label" style="color:' . $status['color'] . ';border:1px solid ' . adjust_hex_brightness($status['color'], 0.4) . ';background: ' . adjust_hex_brightness($status['color'], 0.04) . ';" task-status-table="' . e($aRow['status']) . '">';

            $outputStatus .= e($status['name']);

            if ($canChangeStatus) {
                $outputStatus .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
                $outputStatus .= '<a href="#" style="font-size:14px;vertical-align:middle;" class="dropdown-toggle text-dark" id="tableTaskStatus-' . $aRow['id'] . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                $outputStatus .= '<span data-toggle="tooltip" title="' . _l('ticket_single_change_status') . '"><i class="fa-solid fa-chevron-down tw-opacity-70"></i></span>';
                $outputStatus .= '</a>';

                $outputStatus .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tableTaskStatus-' . $aRow['id'] . '">';
                foreach ($task_statuses as $taskChangeStatus) {
                    if ($aRow['status'] != $taskChangeStatus['id']) {
                        $outputStatus .= '<li>
                  <a href="#" onclick="task_mark_as(' . $taskChangeStatus['id'] . ',' . $aRow['id'] . '); return false;">
                     ' . e(_l('task_mark_as', $taskChangeStatus['name'])) . '
                  </a>
               </li>';
                    }
                }
                $outputStatus .= '</ul>';
                $outputStatus .= '</div>';
            }

            $outputStatus .= '</span>';

            $row[] = $outputStatus;

            $row[] = e(_d($aRow['startdate']));

            $row[] = e(_d($aRow['duedate']));

            $row[] = format_members_by_ids_and_names($aRow['assignees_ids'], $aRow['assignees']);

            $row['DT_RowClass'] = 'has-row-options';

            if ((!empty($aRow['duedate']) && $aRow['duedate'] < date('Y-m-d')) && $aRow['status'] != Tasks_model::STATUS_COMPLETE) {
                $row['DT_RowClass'] .= ' danger';
            }

            $row = hooks()->apply_filters('tasks_table_row_data', $row, $aRow);

            $output['aaData'][] = $row;
        }

        return $output;
    })->setRules([
        App_table_filter::new('name', 'TextRule')->label(_l('tasks_dt_name')),

        App_table_filter::new('startdate', 'DateRule')->label(_l('tasks_dt_datestart')),

        App_table_filter::new('duedate', 'DateRule')
            ->label(_l('task_duedate'))
            ->withEmptyOperators(),

        App_table_filter::new('status', 'MultiSelectRule')->label(_l('task_status'))->options(function ($ci) {
            return collect($ci->tasks_model->get_statuses())->map(fn ($status) => [
                'value' => $status['id'],
                'label' => $status['name']
            ])->all();
        }),

        App_table_filter::new('todays_tasks', 'BooleanRule')
            ->label(_l('todays_tasks'))
            ->raw(function ($value) {
                return '(startdate ' . ($value == '1' ? '=' : '!=') . ' "' . date('Y-m-d') . '") AND status != ' . Tasks_model::STATUS_COMPLETE;
            }),

        App_table_filter::new('duedate_passed', 'BooleanRule')
            ->label(_l('task_list_duedate_passed'))
            ->raw(function ($value) {
                return '(startdate ' . ($value == '1' ? '=' : '!=') . ' "' . date('Y-m-d') . '") AND status != ' . Tasks_model::STATUS_COMPLETE;
            }),

        App_table_filter::new('not_assigned', 'BooleanRule')
            ->label(_l('task_list_not_assigned'))
            ->raw(function ($value) {
                return db_prefix() . 'tasks.id ' . ($value == '1' ? 'NOT IN' : 'IN') . ' (SELECT taskid FROM ' . db_prefix() . 'task_assigned)';
            }),

        App_table_filter::new('my_tasks', 'BooleanRule')
            ->label(_l('tasks_view_assigned_to_user'))
            ->raw(function ($value) {
                return '(' . db_prefix() . 'tasks.id ' . ($value == '1' ? 'IN' : 'NOT IN') . ' (SELECT taskid FROM ' . db_prefix() . 'task_assigned WHERE staffid = ' . get_staff_user_id() . '))';
            }),

        App_table_filter::new('my_following_tasks', 'BooleanRule')
            ->label(_l('tasks_view_follower_by_user'))
            ->raw(function ($value) {
                return '(' . db_prefix() . 'tasks.id ' . ($value == '1' ? 'IN' : 'NOT IN') . ' (SELECT taskid FROM ' . db_prefix() . 'task_followers WHERE staffid = ' . get_staff_user_id() . '))';
            }),

        App_table_filter::new('upcoming_tasks', 'BooleanRule')
            ->label(_l('upcoming_tasks'))
            ->raw(function ($value) {
                return '(duedate ' . ($value == '1' ? '<' : '>') . ' "' . date('Y-m-d') . '" AND duedate IS NOT NULL) AND status != ' . Tasks_model::STATUS_COMPLETE;
            }),

        App_table_filter::new('recurring', 'BooleanRule')
            ->label(_l('recurring_tasks'))
            ->isVisible(fn () => staff_can('create', 'tasks') || staff_can('edit', 'tasks')),

        App_table_filter::new('billable', 'BooleanRule')
            ->label(_l('task_billable'))
            ->isVisible(fn () => staff_can('create', 'invoices')),

        App_table_filter::new('billed', 'BooleanRule')->label(_l('task_billed'))
            ->isVisible(fn () => staff_can('create', 'invoices')),

        App_table_filter::new('assigned', 'MultiSelectRule')->label(_l('task_assigned'))
            ->isVisible(fn () => staff_can('view', 'tasks'))
            ->options(function ($ci) {
                return collect($ci->misc_model->get_tasks_distinct_assignees())->map(function ($staff) {
                    return [
                        'value' => $staff['assigneeid'],
                        'label' => get_staff_full_name($staff['assigneeid'])
                    ];
                })->all();
            })->raw(function ($value, $operator, $sqlOperator) {
                $dbPrefix = db_prefix();
                $sqlOperator = $sqlOperator['operator'];

                return "({$dbPrefix}tasks.id IN (SELECT taskid FROM {$dbPrefix}task_assigned WHERE staffid $sqlOperator ('" . implode("','", $value) . "')))";
            })
    ]);

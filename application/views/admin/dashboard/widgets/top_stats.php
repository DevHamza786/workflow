<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="widget relative" id="widget-<?php echo create_widget_id(); ?>" data-name="<?php echo _l('quick_stats'); ?>">
    <div class="widget-dragger"></div>
    <div class="row">
        <div class="quick-stats-staff col-xs-12 col-md-3 col-sm-6 tw-mb-2 sm:tw-mb-0">
            <div class="top_stats_wrapper">
                <?php
                  $total_staff = total_rows(db_prefix() . 'staff', 'active=1');
                  $total_inactive_staff = total_rows(db_prefix() . 'staff', 'active=0');
                  $percent_active_staff = ($total_staff > 0 ? number_format(($total_staff * 100) / ($total_staff + $total_inactive_staff), 2) : 0);
                  ?>
                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                        <i class="fa fa-users tw-w-30 tw-h-30 tw-mr-3 rtl:tw-ml-3 tw-text-neutral-600 fa-lg"></i>
                        <span class="tw-truncate">
                            <?php echo _l('Total Staff'); ?>
                        </span>
                    </div>
                    <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0">
                        <?php echo e($total_staff); ?>
                    </span>
                </div>
                <div class="progress tw-mb-0 tw-mt-2 progress-bar-mini">
                    <div class="progress-bar progress-bar-success no-percent-text not-dynamic" role="progressbar"
                        aria-valuenow="<?php echo e($percent_active_staff); ?>" aria-valuemin="0" aria-valuemax="100"
                        style="width: 0%" data-percent="<?php echo e($percent_active_staff); ?>">
                    </div>
                </div>
            </div>
        </div>
        <div class="quick-stats-admins col-xs-12 col-md-3 col-sm-6 tw-mb-2 sm:tw-mb-0">
            <div class="top_stats_wrapper">
                <?php
                  $total_admins = total_rows(db_prefix() . 'staff', 'admin=1');
                  $total_users = total_rows(db_prefix() . 'staff', 'admin=0');
                  $percent_admins = (($total_admins + $total_users) > 0 ? number_format(($total_admins * 100) / ($total_admins + $total_users), 2) : 0);
                  ?>
                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                        <i class="fa fa-user tw-w-30 tw-h-30 tw-mr-3 rtl:tw-ml-3 tw-text-neutral-600 fa-la"></i>
                        <span class="tw-truncate">
                            <?php echo _l('Total Admins'); ?>
                        </span>
                    </div>
                    <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0">
                        <?php echo e($total_admins); ?>
                    </span>
                </div>
                <div class="progress tw-mb-0 tw-mt-2 progress-bar-mini">
                    <div class="progress-bar progress-bar-warning no-percent-text not-dynamic" role="progressbar"
                        aria-valuenow="<?php echo e($percent_admins); ?>" aria-valuemin="0" aria-valuemax="100"
                        style="width: 0%" data-percent="<?php echo e($percent_admins); ?>">
                    </div>
                </div>
            </div>
        </div>
        <div class="quick-stats-projects col-xs-12 col-md-3 col-sm-6 tw-mb-2 sm:tw-mb-0">
            <div class="top_stats_wrapper">
                <?php
                  $_where         = '';
                  $project_status = get_project_status_by_id(2);
                  if (staff_cant('view', 'projects')) {
                      $_where = 'id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . ')';
                  }
                  $total_projects               = total_rows(db_prefix() . 'projects', $_where);
                  $where                        = ($_where == '' ? '' : $_where . ' AND ') . 'status = 2';
                  $total_projects_in_progress   = total_rows(db_prefix() . 'projects', $where);
                  $percent_in_progress_projects = ($total_projects > 0 ? number_format(($total_projects_in_progress * 100) / $total_projects, 2) : 0);
                  ?>
                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                    <div class="tw-font-medium tw-inline-flex text-neutral-500 tw-items-center tw-truncate">
                        <i class="fa fa-folder-open tw-w-30 tw-h-30 tw-mr-3 rtl:tw-ml-3 tw-text-neutral-600 fa-lg"></i>
                        <span class="tw-truncate">
                            <?php echo e(_l('projects') . ' ' . $project_status['name']); ?>
                        </span>
                    </div>
                    <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0">
                        <?php echo e($total_projects_in_progress); ?> /
                        <?php echo e($total_projects); ?>
                    </span>
                </div>

                <div class="progress tw-mb-0 tw-mt-2 progress-bar-mini">
                    <div class="progress-bar no-percent-text not-dynamic"
                        style="background:<?php echo e($project_status['color']); ?>" role="progressbar"
                        aria-valuenow="<?php echo e($percent_in_progress_projects); ?>" aria-valuemin="0"
                        aria-valuemax="100" style="width: 0%"
                        data-percent="<?php echo e($percent_in_progress_projects); ?>">
                    </div>
                </div>
            </div>
        </div>
        <div class="quick-stats-tasks col-xs-12 col-md-3 col-sm-6 tw-mb-2 sm:tw-mb-0">
            <div class="top_stats_wrapper">
                <?php
                  $_where = '';
                  if (staff_cant('view', 'tasks')) {
                      $_where = db_prefix() . 'tasks.id IN (SELECT taskid FROM ' . db_prefix() . 'task_assigned WHERE staffid = ' . get_staff_user_id() . ')';
                  }
                  $total_tasks                = total_rows(db_prefix() . 'tasks', $_where);
                  $where                      = ($_where == '' ? '' : $_where . ' AND ') . 'status != ' . Tasks_model::STATUS_COMPLETE;
                  $total_not_finished_tasks   = total_rows(db_prefix() . 'tasks', $where);
                  $percent_not_finished_tasks = ($total_tasks > 0 ? number_format(($total_not_finished_tasks * 100) / $total_tasks, 2) : 0);
                  ?>
                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
                        <i class="fa fa-tasks tw-w-30 tw-h-30 tw-mr-3 rtl:tw-ml-3 tw-text-neutral-600 fa-lg"></i>
                        <span class="tw-truncate">
                            <?php echo _l('tasks_not_finished'); ?>
                        </span>
                    </div>
                    <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0">
                        <?php echo e($total_not_finished_tasks); ?> / <?php echo e($total_tasks); ?>
                    </span>
                </div>
                <div class="progress tw-mb-0 tw-mt-2 progress-bar-mini">
                    <div class="progress-bar progress-bar-default no-percent-text not-dynamic" role="progressbar"
                        aria-valuenow="<?php echo e($percent_not_finished_tasks); ?>" aria-valuemin="0" aria-valuemax="100"
                        style="width: 0%" data-percent="<?php echo e($percent_not_finished_tasks); ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
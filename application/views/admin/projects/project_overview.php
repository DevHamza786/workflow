<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-12 project-overview-left">
        <div class="panel_s tw-shadow-sm tw-rounded-lg tw-border tw-border-neutral-200">
            <div class="panel-body tw-p-5">
                <div class="row">

                    <?php if (count($project->shared_vault_entries) > 0) { ?>
                    <?php $this->load->view('admin/clients/vault_confirm_password'); ?>
                    <div class="col-md-12">
                        <p class="tw-font-medium">
                            <a href="#" onclick="slideToggle('#project_vault_entries'); return false;"
                                class="tw-inline-flex tw-items-center tw-space-x-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="tw-w-5 tw-h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                                </svg>
                                <span>
                                    <?php echo _l('project_shared_vault_entry_login_details'); ?>
                                </span>
                            </a>
                        </p>
                        <div id="project_vault_entries"
                            class="hide tw-mb-4 tw-bg-neutral-50 tw-px-4 tw-py-2 tw-rounded-md">
                            <?php foreach ($project->shared_vault_entries as $vault_entry) { ?>
                            <div class="tw-my-3">
                                <div class="row" id="<?php echo 'vaultEntry-' . $vault_entry['id']; ?>">
                                    <div class="col-md-6">
                                        <p class="mtop5">
                                            <b><?php echo _l('server_address'); ?>:
                                            </b><?php echo e($vault_entry['server_address']); ?>
                                        </p>
                                        <p class="tw-mb-0">
                                            <b><?php echo _l('port'); ?>:
                                            </b><?php echo e(!empty($vault_entry['port']) ? $vault_entry['port'] : _l('no_port_provided')); ?>
                                        </p>
                                        <p class="tw-mb-0">
                                            <b><?php echo _l('vault_username'); ?>:
                                            </b><?php echo e($vault_entry['username']); ?>
                                        </p>
                                        <p class="no-margin">
                                            <b><?php echo _l('vault_password'); ?>: </b><span
                                                class="vault-password-fake">
                                                <?php echo str_repeat('&bull;', 10); ?> </span><span
                                                class="vault-password-encrypted"></span> <a href="#"
                                                class="vault-view-password mleft10" data-toggle="tooltip"
                                                data-title="<?php echo _l('view_password'); ?>"
                                                onclick="vault_re_enter_password(<?php echo e($vault_entry['id']); ?>,this); return false;"><i
                                                    class="fa fa-lock" aria-hidden="true"></i></a>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <?php if (!empty($vault_entry['description'])) { ?>
                                        <p class="tw-mb-0">
                                            <b><?php echo _l('vault_description'); ?>:
                                            </b><br /><?php echo process_text_content_for_display($vault_entry['description']); ?>
                                        </p>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php } ?>

                    <div class="col-md-12 tw-mb-5">
                        <div class="project-overview-open-tasks">
                            <div class="panel_s tw-shadow-sm tw-rounded-lg tw-border tw-border-neutral-200">
                                <div class="panel-body !tw-px-5 !tw-py-4">
                                    <div class="tw-flex tw-items-center tw-justify-between tw-mb-3">
                                        <div>
                                            <p class="tw-text-sm tw-font-semibold tw-text-neutral-600 tw-mb-1">
                                                <?php echo _l('project_open_tasks'); ?>
                                            </p>
                                            <p class="tw-text-xl tw-font-bold tw-text-neutral-900 tw-mb-0">
                                                <span dir="ltr"><?php echo e($tasks_not_completed); ?> / <?php echo e($total_tasks); ?></span>
                                            </p>
                                        </div>
                                        <div class="tw-text-right">
                                            <p class="tw-text-lg tw-font-bold tw-text-neutral-700 tw-mb-0">
                                                <?php echo e($tasks_not_completed_progress); ?>%
                                            </p>
                                        </div>
                                    </div>
                                    <div class="tw-mt-3">
                                        <div class="progress tw-h-2 tw-rounded-full tw-bg-neutral-200 tw-overflow-hidden">
                                            <div class="progress-bar progress-bar-success no-percent-text not-dynamic tw-h-full tw-rounded-full tw-transition-all tw-duration-500"
                                                role="progressbar"
                                                aria-valuenow="<?php echo e($tasks_not_completed_progress); ?>"
                                                aria-valuemin="0" aria-valuemax="100" style="width: 0%"
                                                data-percent="<?php echo e($tasks_not_completed_progress); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <h4 class="tw-font-semibold tw-text-base tw-mb-4 tw-text-neutral-800">
                            <?php echo _l('project_overview'); ?>
                        </h4>
                        <dl class="tw-grid tw-grid-cols-1 tw-gap-x-4 tw-gap-y-4 sm:tw-grid-cols-2">
                            <div class="sm:tw-col-span-1 project-overview-id">
                                <dt class="tw-text-xs tw-font-semibold tw-text-neutral-500 tw-uppercase tw-tracking-wide tw-mb-2">
                                    <?php echo _l('project'); ?> <?php echo _l('the_number_sign'); ?>
                                </dt>
                                <dd class="tw-text-base tw-font-medium tw-text-neutral-900"><?php echo e($project->id); ?></dd>
                            </div>

                            <div class="sm:tw-col-span-1 project-overview-customer">
                                <dt class="tw-text-xs tw-font-semibold tw-text-neutral-500 tw-uppercase tw-tracking-wide tw-mb-2">
                                    <?php echo _l('project_customer'); ?>
                                </dt>
                                <dd class="tw-text-base tw-font-medium tw-text-neutral-900">
                                    <a href="<?php echo admin_url(); ?>clients/client/<?php echo e($project->clientid); ?>" class="tw-text-blue-600 hover:tw-text-blue-800 tw-transition-colors">
                                        <?php echo e($project->client_data->company); ?>
                                    </a>
                                </dd>
                            </div>


                            <div class="sm:tw-col-span-1 project-overview-status">
                                <dt class="tw-text-xs tw-font-semibold tw-text-neutral-500 tw-uppercase tw-tracking-wide tw-mb-2">
                                    <?php echo _l('project_status'); ?>
                                </dt>
                                <dd>
                                    <span class="tw-inline-flex tw-items-center tw-px-3 tw-py-1 tw-rounded-md tw-text-sm tw-font-medium" style="color: <?php echo e($project_status['color']); ?>; background-color: <?php echo adjust_hex_brightness($project_status['color'], 0.1); ?>; border: 1px solid <?php echo adjust_hex_brightness($project_status['color'], 0.2); ?>;">
                                        <?php echo e($project_status['name']); ?>
                                    </span>
                                </dd>
                            </div>

                            <div class="sm:tw-col-span-1 project-overview-date-created">
                                <dt class="tw-text-xs tw-font-semibold tw-text-neutral-500 tw-uppercase tw-tracking-wide tw-mb-2">
                                    <?php echo _l('project_datecreated'); ?>
                                </dt>
                                <dd class="tw-text-base tw-font-medium tw-text-neutral-900">
                                    <?php echo e(_d($project->project_created)); ?>
                                </dd>
                            </div>
                            <div class="sm:tw-col-span-1 project-overview-start-date">
                                <dt class="tw-text-xs tw-font-semibold tw-text-neutral-500 tw-uppercase tw-tracking-wide tw-mb-2">
                                    <?php echo _l('project_start_date'); ?>
                                </dt>
                                <dd class="tw-text-base tw-font-medium tw-text-neutral-900">
                                    <?php echo e(_d($project->start_date)); ?>
                                </dd>
                            </div>
                            <?php if ($project->deadline) { ?>
                            <div class="sm:tw-col-span-1 project-overview-deadline">
                                <dt class="tw-text-xs tw-font-semibold tw-text-neutral-500 tw-uppercase tw-tracking-wide tw-mb-2">
                                    <?php echo _l('project_deadline'); ?>
                                </dt>
                                <dd class="tw-text-base tw-font-medium tw-text-neutral-900">
                                    <?php echo e(_d($project->deadline)); ?>
                                </dd>
                            </div>
                            <?php } ?>

                            <?php if ($project->date_finished) { ?>
                            <div class="sm:tw-col-span-1 project-overview-date-finished">
                                <dt class="tw-text-xs tw-font-semibold tw-text-neutral-500 tw-uppercase tw-tracking-wide tw-mb-2">
                                    <?php echo _l('project_completed_date'); ?>
                                </dt>
                                <dd class="tw-text-base tw-font-medium text-success">
                                    <?php echo e(_dt($project->date_finished)); ?>
                                </dd>
                            </div>
                            <?php } ?>

                            <?php if ($project->estimated_hours && $project->estimated_hours != '0') { ?>
                            <div class="sm:tw-col-span-1 project-overview-estimated-hours">
                                <dt class="tw-text-xs tw-font-semibold tw-text-neutral-500 tw-uppercase tw-tracking-wide tw-mb-2">
                                    <?php echo _l('estimated_hours'); ?>
                                </dt>
                                <dd class="tw-text-base tw-font-medium <?php echo hours_to_seconds_format($project->estimated_hours) < (int)$project_total_logged_time ? 'text-warning' : 'text-neutral-900'; ?>">
                                    <?php echo e(str_replace('.', ':', $project->estimated_hours)); ?>
                                </dd>
                            </div>
                            <?php } ?>



                            <?php $custom_fields = get_custom_fields('projects');
                            if (count($custom_fields) > 0) { ?>
                            <?php foreach ($custom_fields as $field) { ?>
                            <?php $value = get_custom_field_value($project->id, $field['id'], 'projects');
                            if ($value == '') {
                                continue;
                            } ?>
                            <div class="sm:tw-col-span-1">
                                <dt class="tw-text-xs tw-font-semibold tw-text-neutral-500 tw-uppercase tw-tracking-wide tw-mb-2">
                                    <?php echo e(ucfirst($field['name'])); ?>
                                </dt>
                                <dd class="tw-text-base tw-font-medium tw-text-neutral-900">
                                    <?php echo $value; ?>
                                </dd>
                            </div>
                            <?php } ?>
                            <?php } ?>

                            <?php $tags = get_tags_in($project->id, 'project'); ?>
                            <?php if (count($tags) > 0) { ?>
                            <div class="sm:tw-col-span-1 project-overview-tags">
                                <dt class="tw-text-xs tw-font-semibold tw-text-neutral-500 tw-uppercase tw-tracking-wide tw-mb-2">
                                    Sub-Brands
                                </dt>
                                <dd class="tags-read-only-custom">
                                    <input type="text" class="tagsinput read-only" id="tags" name="tags"
                                        value="<?php echo prep_tags_input($tags); ?>" data-role="tagsinput">
                                </dd>
                            </div>
                            <?php } ?>
                            <div class="clearfix"></div>
                            <div class="sm:tw-col-span-2 project-overview-description tc-content tw-mt-2">
                                <dt class="tw-text-xs tw-font-semibold tw-text-neutral-500 tw-uppercase tw-tracking-wide tw-mb-2">
                                    <?php echo _l('project_description'); ?>
                                </dt>
                                <dd class="tw-p-3 tw-bg-neutral-50 tw-rounded-md tw-border tw-border-neutral-200 tw-text-sm tw-text-neutral-700">
                                    <?php if (empty($project->description)) { ?>
                                    <p class="text-muted tw-mb-0 tw-italic">
                                        <?php echo _l('no_description_project'); ?>
                                    </p>
                                    <?php } else { ?>
                                    <?php echo check_for_links($project->description); ?>
                                    <?php } ?>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <?php hooks()->do_action('admin_project_overview_end_of_project_overview_left', $project) ?>
    </div>
</div>

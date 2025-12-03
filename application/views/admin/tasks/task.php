<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php echo form_open_multipart(admin_url('tasks/task' . ($id ? '/' . $id : '')), ['id' => 'task-form']); ?>
<div class="modal fade<?php if (isset($task)) {
    echo ' edit';
} ?>" id="_task_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" <?php if ($this->input->get('opened_from_lead_id')) {
    echo 'data-lead-id=' . $this->input->get('opened_from_lead_id');
} ?>>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo e($title); ?>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php
                  $rel_type = '';
                  $rel_id   = '';
                  if (isset($task) || ($this->input->get('rel_id') && $this->input->get('rel_type'))) {
                      $rel_id   = isset($task) ? $task->rel_id : $this->input->get('rel_id');
                      $rel_type = isset($task) ? $task->rel_type : $this->input->get('rel_type');
                  }
                   if (isset($task) && $task->billed == 1) {
                       echo '<div class="alert alert-success text-center no-margin">' . _l('task_is_billed', '<a href="' . admin_url('invoices/list_invoices/' . $task->invoice_id) . '" target="_blank">' . e(format_invoice_number($task->invoice_id))) . '</a></div><br />';
                   }
                  ?>
                        <?php if (isset($task)) { ?>
                        <div class="pull-right mbot10 task-single-menu task-menu-options">
                            <div class="content-menu hide">
                                <ul>
                                    <?php if (staff_can('create',  'tasks')) { ?>
                                    <?php
                           $copy_template = '';
                           if (total_rows(db_prefix() . 'task_assigned', ['taskid' => $task->id]) > 0) {
                               $copy_template .= "<div class='checkbox checkbox-primary'><input type='checkbox' name='copy_task_assignees' id='copy_task_assignees' checked><label for='copy_task_assignees'>" . _l('task_single_assignees') . '</label></div>';
                           }
                           if (total_rows(db_prefix() . 'task_followers', ['taskid' => $task->id]) > 0) {
                               $copy_template .= "<div class='checkbox checkbox-primary'><input type='checkbox' name='copy_task_followers' id='copy_task_followers' checked><label for='copy_task_followers'>" . _l('task_single_followers') . '</label></div>';
                           }
                           if (total_rows(db_prefix() . 'task_checklist_items', ['taskid' => $task->id]) > 0) {
                               $copy_template .= "<div class='checkbox checkbox-primary'><input type='checkbox' name='copy_task_checklist_items' id='copy_task_checklist_items' checked><label for='copy_task_checklist_items'>" . _l('task_checklist_items') . '</label></div>';
                           }
                           if (total_rows(db_prefix() . 'files', ['rel_id' => $task->id, 'rel_type' => 'task']) > 0) {
                               $copy_template .= "<div class='checkbox checkbox-primary'><input type='checkbox' name='copy_task_attachments' id='copy_task_attachments'><label for='copy_task_attachments'>" . _l('task_view_attachments') . '</label></div>';
                           }

                           $copy_template .= '<p>' . _l('task_status') . '</p>';
                           $task_copy_statuses = hooks()->apply_filters('task_copy_statuses', $task_statuses);
                           foreach ($task_copy_statuses as $copy_status) {
                               $copy_template .= "<div class='radio radio-primary'><input type='radio' value='" . $copy_status['id'] . "' name='copy_task_status' id='copy_task_status_" . $copy_status['id'] . "'" . ($copy_status['id'] == hooks()->apply_filters('copy_task_default_status', 1) ? ' checked' : '') . "><label for='copy_task_status_" . $copy_status['id'] . "'>" . $copy_status['name'] . '</label></div>';
                           }

                           $copy_template .= "<div class='text-center'>";
                           $copy_template .= "<button type='button' data-task-copy-from='" . $task->id . "' class='btn btn-success copy_task_action'>" . _l('copy_task_confirm') . '</button>';
                           $copy_template .= '</div>';
                           ?>
                                    <li> <a href="#" onclick="return false;" data-placement="bottom"
                                            data-toggle="popover"
                                            data-content="<?php echo htmlspecialchars($copy_template); ?>"
                                            data-html="true"><?php echo _l('task_copy'); ?></span></a>
                                    </li>
                                    <?php } ?>
                                    <?php if (staff_can('delete',  'tasks')) { ?>
                                    <li>
                                        <a href="<?php echo admin_url('tasks/delete_task/' . $task->id); ?>"
                                            class="_delete task-delete">
                                            <?php echo _l('task_single_delete'); ?>
                                        </a>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </div>
                            <?php if (staff_can('delete',  'tasks') || staff_can('create',  'tasks')) { ?>
                            <a href="#" onclick="return false;" class="trigger manual-popover mright5">
                                <i class="fa-regular fa-circle fa-sm"></i>
                                <i class="fa-regular fa-circle fa-sm"></i>
                                <i class="fa-regular fa-circle fa-sm"></i>
                            </a>
                            <?php } ?>
                        </div>
                        <?php } ?>
                        <?php
                        // Set default values for removed fields as hidden inputs
                        $is_public = (isset($task) && $task->is_public == 1) ? '1' : '0';
                        $billable = (isset($task) && $task->billable == 1) ? '1' : '0';
                        $visible_to_client = (isset($task) && $task->visible_to_client == 1) ? '1' : '0';
                        $milestone = (isset($task) && $task->milestone) ? $task->milestone : '';
                        $priority = (isset($task) && $task->priority) ? $task->priority : (get_option('default_task_priority') ?: '2');
                        $repeat_every = (isset($task) && $task->recurring > 0) ? $task->repeat_every . '-' . $task->recurring_type : '';
                        $tags = (isset($task)) ? prep_tags_input(get_tags_in($task->id, 'task')) : '';
                        
                        echo form_hidden('is_public', $is_public);
                        echo form_hidden('billable', $billable);
                        echo form_hidden('visible_to_client', $visible_to_client);
                        echo form_hidden('milestone', $milestone);
                        echo form_hidden('priority', $priority);
                        echo form_hidden('repeat_every', $repeat_every);
                        echo form_hidden('tags', $tags);
                        ?>
                        <?php /* Remove Public, Billable, Visible to customer checkboxes and Attach Files option
                        <div class="checkbox checkbox-primary checkbox-inline task-add-edit-public tw-pt-2">
                            <input type="checkbox" id="task_is_public" name="is_public" <?php if (isset($task)) {
                               if ($task->is_public == 1) {
                                   echo 'checked';
                               }
                           }; ?>>
                            <label for="task_is_public" data-toggle="tooltip" data-placement="bottom"
                                title="<?php echo _l('task_public_help'); ?>"><?php echo _l('task_public'); ?></label>
                        </div>
                        <div class="checkbox checkbox-primary checkbox-inline task-add-edit-billable tw-pt-2">
                            <input type="checkbox" id="task_is_billable" name="billable" <?php if ((isset($task) && $task->billable == 1) || (!isset($task) && get_option('task_biillable_checked_on_creation') == 1)) {
                               echo ' checked';
                           }?>>
                            <label for="task_is_billable"><?php echo _l('task_billable'); ?></label>
                        </div>
                        <div class="task-visible-to-customer tw-pt-2 checkbox checkbox-inline checkbox-primary<?php if ((isset($task) && $task->rel_type != 'project') || !isset($task) || (isset($task) && $task->rel_type == 'project' && total_rows(db_prefix() . 'project_settings', ['project_id' => $task->rel_id, 'name' => 'view_tasks', 'value' => 0]) > 0)) {
echo ' hide';
                           } ?>">
                            <input type="checkbox" id="task_visible_to_client" name="visible_to_client" <?php if (isset($task)) {
                               if ($task->visible_to_client == 1) {
                                   echo 'checked';
                               }
                           } ?>>
                            <label for="task_visible_to_client"><?php echo _l('task_visible_to_client'); ?></label>
                        </div>
                        <?php if (!isset($task)) { ?>
                        <a href="#" class="pull-right tw-pt-2"
                            onclick="slideToggle('#new-task-attachments'); return false;">
                            <?php echo _l('attach_files'); ?>
                        </a>
                        <div id="new-task-attachments" class="hide">
                            <hr class="-tw-mx-3.5" />
                            <div class="row attachments">
                                <div class="attachment">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="attachment"
                                                class="control-label"><?php echo _l('add_task_attachments'); ?></label>
                                            <div class="input-group">
                                                <input type="file"
                                                    extension="<?php echo str_replace('.', '', get_option('allowed_files')); ?>"
                                                    filesize="<?php echo file_upload_max_size(); ?>"
                                                    class="form-control" name="attachments[0]">
                                                <span class="input-group-btn">
                                                    <button class="btn btn-default add_more_attachments"
                                                        type="button"><i class="fa fa-plus"></i></button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                            if ($this->input->get('ticket_to_task')) {
                                echo form_hidden('ticket_to_task', $rel_id);
                            }
                        } ?>
                        */ ?>
                        <?php
                            if ($this->input->get('ticket_to_task')) {
                                echo form_hidden('ticket_to_task', $rel_id);
                            }
                        ?>
                        <?php
                        // Parse existing task name to extract task type and nature (if editing)
                        $task_type = '';
                        $task_natures = [];
                        if (isset($task) && !empty($task->name)) {
                            // Try to parse format: "Task Type - Task Nature1, Task Nature2" or "Task Type - Task Nature" or just "Task Type"
                            if (strpos($task->name, ' - ') !== false) {
                                $parts = explode(' - ', $task->name, 2);
                                $task_type = $parts[0];
                                // Remove numbers from task type if present (for backward compatibility)
                                $task_type = preg_replace('/^\d+\.\s*/', '', $task_type);
                                $natures = $parts[1];
                                // Handle multiple natures separated by comma
                                if (strpos($natures, ',') !== false) {
                                    $task_natures = array_map('trim', explode(',', $natures));
                                } else {
                                    $task_natures = [trim($natures)];
                                }
                            } else {
                                $task_type = preg_replace('/^\d+\.\s*/', '', $task->name);
                            }
                        }
                        ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group select-placeholder">
                                    <label for="task_type" class="control-label"><?php echo _l('task_type'); ?></label>
                                    <select name="task_type" id="task_type" class="selectpicker" data-width="100%"
                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                        <option value=""><?php echo _l('dropdown_non_selected_tex'); ?></option>
                                        <option value="Creative" <?php echo ($task_type == 'Creative' || $task_type == '1. Creative') ? 'selected' : ''; ?>>Creative</option>
                                        <option value="Design" <?php echo ($task_type == 'Design' || $task_type == '2. Design') ? 'selected' : ''; ?>>Design</option>
                                        <option value="Strategy" <?php echo ($task_type == 'Strategy' || $task_type == '3. Strategy') ? 'selected' : ''; ?>>Strategy</option>
                                        <option value="Production" <?php echo ($task_type == 'Production' || $task_type == '4. Production') ? 'selected' : ''; ?>>Production</option>
                                        <option value="CS" <?php echo ($task_type == 'CS' || $task_type == '5. CS') ? 'selected' : ''; ?>>CS</option>
                                        <option value="Tech" <?php echo ($task_type == 'Tech' || $task_type == '6. Tech') ? 'selected' : ''; ?>>Tech</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group select-placeholder">
                                    <label for="task_nature" class="control-label"><?php echo _l('task_nature'); ?></label>
                                    <select name="task_nature[]" id="task_nature" class="selectpicker" data-width="100%"
                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                                        multiple data-live-search="true" data-actions-box="true">
                                    </select>
                                </div>
                            </div>
                        </div>
                        <?php echo form_hidden('name', ''); ?>
                        <div class="task-hours<?php if (isset($task) && $task->rel_type == 'project' && total_rows(db_prefix() . 'projects', ['id' => $task->rel_id, 'billing_type' => 3]) == 0) {
                            echo ' hide';
                          } ?>">
                            <?php $value = (isset($task) ? $task->hourly_rate : 0); ?>
                            <?php echo render_input('hourly_rate', 'task_hourly_rate', $value); ?>
                        </div>
                        <?php /* Remove Milestone option
                        <div class="project-details<?php if ($rel_type != 'project') {
                            echo ' hide';
                          } ?>">
                            <div class="form-group">
                                <label for="milestone"><?php echo _l('task_milestone'); ?></label>
                                <select name="milestone" id="milestone" class="selectpicker" data-width="100%"
                                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <option value=""></option>
                                    <?php foreach ($milestones as $milestone) { ?>
                                    <option value="<?php echo e($milestone['id']); ?>" <?php if (isset($task) && $task->milestone == $milestone['id']) {
                      echo 'selected';
                  } ?>><?php echo e($milestone['name']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        */ ?>
                        <div class="row">
                            <div class="col-md-6">
                                <?php if (isset($task)) {
                      $value = _d($task->startdate);
                  } elseif (isset($start_date)) {
                      $value = $start_date;
                  } else {
                      $value = _d(date('Y-m-d'));
                  }
                        $date_attrs = [];
                        if (isset($task) && $task->recurring > 0 && $task->last_recurring_date != null) {
                            $date_attrs['disabled'] = true;
                        }
                        ?>
                                <?php echo render_date_input('startdate', 'task_add_edit_start_date', $value, $date_attrs); ?>
                            </div>
                            <div class="col-md-6">
                                <?php $value = (isset($task) ? _d($task->duedate) : ''); ?>
                                <?php echo render_date_input('duedate', 'task_add_edit_due_date', $value, $project_end_date_attrs); ?>
                            </div>
                            <?php /* Remove Priority option
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="priority"
                                        class="control-label"><?php echo _l('task_add_edit_priority'); ?></label>
                                    <select name="priority" class="selectpicker" id="priority" data-width="100%"
                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                        <?php foreach (get_tasks_priorities() as $priority) { ?>
                                        <option value="<?php echo e($priority['id']); ?>" <?php if (isset($task) && $task->priority == $priority['id'] || !isset($task) && get_option('default_task_priority') == $priority['id']) {
                            echo ' selected';
                        } ?>><?php echo e($priority['name']); ?></option>
                                        <?php } ?>
                                        <?php hooks()->do_action('task_priorities_select', (isset($task) ? $task : 0)); ?>
                                    </select>
                                </div>
                            </div>
                            */ ?>
                            <?php /* Remove Repeat every dropdown
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="repeat_every"
                                        class="control-label"><?php echo _l('task_repeat_every'); ?></label>
                                    <select name="repeat_every" id="repeat_every" class="selectpicker" data-width="100%"
                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                        <option value=""></option>
                                        <option value="1-week" <?php if (isset($task) && $task->repeat_every == 1 && $task->recurring_type == 'week') {
                            echo 'selected';
                        } ?>><?php echo _l('week'); ?></option>
                                        <option value="2-week" <?php if (isset($task) && $task->repeat_every == 2 && $task->recurring_type == 'week') {
                            echo 'selected';
                        } ?>>2 <?php echo _l('weeks'); ?></option>
                                        <option value="1-month" <?php if (isset($task) && $task->repeat_every == 1 && $task->recurring_type == 'month') {
                            echo 'selected';
                        } ?>>1 <?php echo _l('month'); ?></option>
                                        <option value="2-month" <?php if (isset($task) && $task->repeat_every == 2 && $task->recurring_type == 'month') {
                            echo 'selected';
                        } ?>>2 <?php echo _l('months'); ?></option>
                                        <option value="3-month" <?php if (isset($task) && $task->repeat_every == 3 && $task->recurring_type == 'month') {
                            echo 'selected';
                        } ?>>3 <?php echo _l('months'); ?></option>
                                        <option value="6-month" <?php if (isset($task) && $task->repeat_every == 6 && $task->recurring_type == 'month') {
                            echo 'selected';
                        } ?>>6 <?php echo _l('months'); ?></option>
                                        <option value="1-year" <?php if (isset($task) && $task->repeat_every == 1 && $task->recurring_type == 'year') {
                            echo 'selected';
                        } ?>>1 <?php echo _l('year'); ?></option>
                                        <option value="custom" <?php if (isset($task) && $task->custom_recurring == 1) {
                            echo 'selected';
                        } ?>><?php echo _l('recurring_custom'); ?></option>
                                    </select>
                                </div>
                            </div>
                            */ ?>
                        </div>
                        <?php /* Remove custom recurring and cycles wrapper
                        <div class="recurring_custom <?php if ((isset($task) && $task->custom_recurring != 1) || (!isset($task))) {
                            echo 'hide';
                        } ?>">
                            <div class="row">
                                <div class="col-md-6">
                                    <?php $value = (isset($task) && $task->custom_recurring == 1 ? $task->repeat_every : 1); ?>
                                    <?php echo render_input('repeat_every_custom', '', $value, 'number', ['min' => 1]); ?>
                                </div>
                                <div class="col-md-6">
                                    <select name="repeat_type_custom" id="repeat_type_custom" class="selectpicker"
                                        data-width="100%"
                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                        <option value="day" <?php if (isset($task) && $task->custom_recurring == 1 && $task->recurring_type == 'day') {
                            echo 'selected';
                        } ?>><?php echo _l('task_recurring_days'); ?></option>
                                        <option value="week" <?php if (isset($task) && $task->custom_recurring == 1 && $task->recurring_type == 'week') {
                            echo 'selected';
                        } ?>><?php echo _l('task_recurring_weeks'); ?></option>
                                        <option value="month" <?php if (isset($task) && $task->custom_recurring == 1 && $task->recurring_type == 'month') {
                            echo 'selected';
                        } ?>><?php echo _l('task_recurring_months'); ?></option>
                                        <option value="year" <?php if (isset($task) && $task->custom_recurring == 1 && $task->recurring_type == 'year') {
                            echo 'selected';
                        } ?>><?php echo _l('task_recurring_years'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div id="cycles_wrapper" class="<?php if (!isset($task) || (isset($task) && $task->recurring == 0)) {
                            echo ' hide';
                        }?>">
                            <?php $value = (isset($task) ? $task->cycles : 0); ?>
                            <div class="form-group recurring-cycles">
                                <label for="cycles"><?php echo _l('recurring_total_cycles'); ?>
                                    <?php if (isset($task) && $task->total_cycles > 0) {
                            echo '<small>' . e(_l('cycles_passed', $task->total_cycles)) . '</small>';
                        }
                        ?>
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control" <?php if ($value == 0) {
                            echo ' disabled';
                        } ?> name="cycles" id="cycles" value="<?php echo e($value); ?>" <?php if (isset($task) && $task->total_cycles > 0) {
                            echo 'min="' . e($task->total_cycles) . '"';
                        } ?>>
                                    <div class="input-group-addon">
                                        <div class="checkbox">
                                            <input type="checkbox" <?php if ($value == 0) {
                            echo ' checked';
                        } ?> id="unlimited_cycles">
                                            <label for="unlimited_cycles"><?php echo _l('cycles_infinity'); ?></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        */ ?>
                        <?php
                        // Always set rel_type to 'project' (Job) - permanent
                        $rel_type = 'project';
                        if (!isset($task) && !$this->input->get('rel_type')) {
                            $rel_id = '';
                        } elseif (isset($task)) {
                            if ($task->rel_type != 'project') {
                                // Convert existing tasks to project type
                                $rel_type = 'project';
                                $rel_id = ($task->rel_id && $task->rel_type == 'project') ? $task->rel_id : '';
                            } else {
                                $rel_id = $task->rel_id;
                            }
                        } else {
                            $rel_id = $this->input->get('rel_id') ?: '';
                        }
                        
                        // Set rel_type as hidden input (always project)
                        echo form_hidden('rel_type', 'project');
                        ?>
                        <div class="row">
                            <?php /* Hide Related To dropdown - always set to Job (project)
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="rel_type"
                                        class="control-label"><?php echo _l('task_related_to'); ?></label>
                                    <select name="rel_type" class="selectpicker" id="rel_type" data-width="100%"
                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                        <option value="project" selected><?php echo _l('project'); ?></option>
                                    </select>
                                </div>
                            </div>
                            */ ?>
                            <div class="col-md-12">
                                <div class="form-group" id="rel_id_wrapper">
                                    <label for="rel_id" class="control-label"><?php echo _l('task_related_to'); ?></label>
                                    <div id="rel_id_select">
                                        <select name="rel_id" id="rel_id" class="ajax-sesarch" data-width="100%"
                                            data-live-search="true"
                                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                            <?php if ($rel_id != '' && $rel_type == 'project') {
                                $rel_data = get_relation_data('project', $rel_id);
                                $rel_val  = get_relation_values($rel_data, 'project');
                                echo '<option value="' . $rel_val['id'] . '" selected>' . $rel_val['name'] . '</option>';
                            } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if (!isset($task)) { ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group select-placeholder>">
                                    <label for="assignees"><?php echo _l('task_single_assignees'); ?></label>
                                    <select name="assignees[]" id="assignees" class="selectpicker" data-width="100%"
                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                                        multiple data-live-search="true">
                                        <?php foreach ($members as $member) { ?>
                                        <option value="<?php echo e($member['staffid']); ?>" <?php if ((get_option('new_task_auto_assign_current_member') == '1') && get_staff_user_id() == $member['staffid']) {
                                echo 'selected';
                            } ?> data-subtext="<?php echo e($member['department_name']); ?>">
                                            <?php echo e($member['fullname']); ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <?php
                     $follower = (get_option('new_task_auto_follower_current_member') == '1') ? [get_staff_user_id()] : '';
                     echo render_select('followers[]', $members, ['staffid', 'fullname', 'department_name'], 'task_single_followers', $follower, ['multiple' => true], [], '', '', false);
                     ?>
                            </div>
                        </div>
                        <?php } ?>

                        <?php
                  if (isset($task)
                     && $task->status == Tasks_model::STATUS_COMPLETE
                     && (staff_can('create', 'tasks') || staff_can('edit', 'tasks'))) {
                      echo render_datetime_input('datefinished', 'task_finished', _dt($task->datefinished));
                  }
               ?>
                        <div class="form-group checklist-templates-wrapper<?php if (count($checklistTemplates) == 0 || isset($task)) {
                   echo ' hide';
               }  ?>">
                            <label for="checklist_items"><?php echo _l('insert_checklist_templates'); ?></label>
                            <select id="checklist_items" name="checklist_items[]"
                                class="selectpicker checklist-items-template-select" multiple="1"
                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex') ?>"
                                data-width="100%" data-live-search="true" data-actions-box="true">
                                <option value="" class="hide"></option>
                                <?php foreach ($checklistTemplates as $chkTemplate) { ?>
                                <option value="<?php echo e($chkTemplate['id']); ?>">
                                    <?php echo e($chkTemplate['description']); ?>
                                </option>
                                <?php } ?>
                            </select>
                        </div>
                        <?php /* Remove Sub-brands (tags) option
                        <div class="form-group">
                            <div id="inputTagsWrapper">
                                <label for="tags" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i>
                                    <?php echo _l('tags'); ?></label>
                                <input type="text" class="tagsinput" id="tags" name="tags"
                                    value="<?php echo(isset($task) ? prep_tags_input(get_tags_in($task->id, 'task')) : ''); ?>"
                                    data-role="tagsinput">
                            </div>
                        </div>
                        */ ?>
                        <?php $rel_id_custom_field = (isset($task) ? $task->id : false); ?>
                        <?php echo render_custom_fields('tasks', $rel_id_custom_field); ?>
                        <hr />
                        <p class="bold"><?php echo _l('task_add_edit_description'); ?></p>
                        <?php
               // onclick and onfocus used for convert ticket to task too
               echo render_textarea('description', '', (isset($task) ? $task->description : ''), ['rows' => 6, 'placeholder' => _l('task_add_description'), 'data-task-ae-editor' => true, !is_mobile() ? 'onclick' : 'onfocus' => (!isset($task) || isset($task) && $task->description == '' ? 'init_editor(\'.tinymce-task\', {height:200, auto_focus: true});' : '')], [], 'no-mbot', 'tinymce-task'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
            </div>
        </div>
    </div>
    <?php echo form_close(); ?>
    <script>
    var _rel_id = $('#rel_id'),
        _rel_type = null, // Always 'project', so no need for select element
        _rel_id_wrapper = $('#rel_id_wrapper'),
        _current_member = undefined,
        data = {type: 'project'}; // Always set to project

    var _milestone_selected_data;
    _milestone_selected_data = undefined;

    <?php if (get_option('new_task_auto_assign_current_member') == '1') { ?>
    _current_member = "<?php echo get_staff_user_id(); ?>";
    <?php } ?>
    $(function() {

        $("body").off("change", "#rel_id");

        var inner_popover_template =
            '<div class="popover"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"></div></div></div>';

        $('#_task_modal .task-menu-options .trigger').popover({
            html: true,
            placement: "bottom",
            trigger: 'click',
            title: "<?php echo _l('actions'); ?>",
            content: function() {
                return $('body').find('#_task_modal .task-menu-options .content-menu').html();
            },
            template: inner_popover_template
        });

        custom_fields_hyperlink();

        appValidateForm($('#task-form'), {
            task_type: 'required',
            startdate: 'required',
            repeat_every_custom: {
                min: 1
            },
        }, task_form_handler);

        // Always set to project (Job) - no dropdown needed
        // $('.rel_id_label').html(_rel_type.find('option:selected').text());
        
        // Initialize rel_id wrapper to be visible (always show Job selector)
        _rel_id_wrapper.removeClass('hide');
        init_project_details('project');

        init_datepicker();
        init_color_pickers();
        init_selectpicker();
        task_rel_select();

        // Task Type and Task Nature cascading dropdowns
        var taskNatureOptions = {
            'Creative': [
                'Ideation',
                'TVC/DVC Concept',
                'ATL Content',
                'Digital Content',
                'CC Content',
                'Presentation Development',
                'Moving Board',
                'Static Pictorial Board',
                'Digital Ideation'
            ],
            'Design': [
                'KV',
                'Animation',
                'Static Post',
                'ATL Adaptations',
                'Digital Adaptations',
                'Website',
                'Video Editing',
                'AI Video Creation',
                'Deck Template',
                'Reels',
                'Tender Ads',
                'Marketing Collaterals'
            ],
            'Strategy': ['N/a'],
            'Production': ['N/a'],
            'CS': ['N/a'],
            'Tech': ['N/a']
        };

        // Function to update task nature dropdown based on task type
        function updateTaskNature(taskType, selectedNatures) {
            var $taskNature = $('#task_nature');
            $taskNature.empty();
            
            // Handle backward compatibility - remove numbers from task type
            if (taskType && taskType.match(/^\d+\.\s*/)) {
                taskType = taskType.replace(/^\d+\.\s*/, '');
            }

            if (taskType && taskNatureOptions[taskType]) {
                taskNatureOptions[taskType].forEach(function(nature) {
                    var selected = '';
                    if (selectedNatures && selectedNatures.length > 0) {
                        if (Array.isArray(selectedNatures)) {
                            selected = (selectedNatures.indexOf(nature) !== -1) ? 'selected' : '';
                        } else {
                            selected = (selectedNatures === nature) ? 'selected' : '';
                        }
                    }
                    $taskNature.append('<option value="' + nature + '" ' + selected + '>' + nature + '</option>');
                });
            }

            $taskNature.selectpicker('refresh');
            updateTaskName();
        }

        // Function to update the hidden name field
        function updateTaskName() {
            var taskType = $('#task_type').val();
            var taskNatures = $('#task_nature').val(); // This will be an array for multi-select
            var combinedName = '';

            // Handle backward compatibility - remove numbers from task type
            if (taskType && taskType.match(/^\d+\.\s*/)) {
                taskType = taskType.replace(/^\d+\.\s*/, '');
                $('#task_type').val(taskType).selectpicker('refresh');
            }

            if (taskType) {
                // Filter out empty values and "N/a" from task natures
                var validNatures = [];
                if (taskNatures && Array.isArray(taskNatures)) {
                    validNatures = taskNatures.filter(function(nature) {
                        return nature && nature !== '' && nature !== 'N/a';
                    });
                } else if (taskNatures && taskNatures !== '' && taskNatures !== 'N/a') {
                    validNatures = [taskNatures];
                }

                if (validNatures.length > 0) {
                    combinedName = taskType + ' - ' + validNatures.join(', ');
                } else {
                    combinedName = taskType;
                }
            }

            $('input[name="name"]').val(combinedName);
        }

        // Update name field before form submission
        $('#task-form').on('submit', function(e) {
            updateTaskName();
        });

        // Handle task type change
        $('#task_type').on('changed.bs.select', function() {
            var selectedType = $(this).val();
            // Handle backward compatibility - remove numbers from task type
            if (selectedType && selectedType.match(/^\d+\.\s*/)) {
                selectedType = selectedType.replace(/^\d+\.\s*/, '');
                $(this).val(selectedType).selectpicker('refresh');
            }
            updateTaskNature(selectedType, []);
            
            // Auto-select "N/a" if it's the only option
            if (selectedType && taskNatureOptions[selectedType] && taskNatureOptions[selectedType].length === 1 && taskNatureOptions[selectedType][0] === 'N/a') {
                setTimeout(function() {
                    $('#task_nature').val(['N/a']).selectpicker('refresh');
                    updateTaskName();
                }, 50);
            } else {
                $('#task_nature').val([]).selectpicker('refresh');
            }
        });

        // Handle task nature change
        $('#task_nature').on('changed.bs.select', function() {
            updateTaskName();
        });

        // Initialize task nature dropdown if task type is already selected (when editing)
        var initialTaskType = $('#task_type').val();
        var initialTaskNatures = <?php echo json_encode(isset($task_natures) ? $task_natures : []); ?>;
        
        // Handle backward compatibility - remove numbers from task type
        if (initialTaskType && initialTaskType.match(/^\d+\.\s*/)) {
            initialTaskType = initialTaskType.replace(/^\d+\.\s*/, '');
            $('#task_type').val(initialTaskType).selectpicker('refresh');
        }
        
        if (initialTaskType) {
            // Small delay to ensure selectpicker is initialized
            setTimeout(function() {
                updateTaskNature(initialTaskType, initialTaskNatures);
            }, 100);
        }

        // Also update name field on page load if values exist
        if (initialTaskType) {
            setTimeout(function() {
                updateTaskName();
            }, 200);
        }

        var _allAssigneeSelect = $("#assignees").html();

        $('body').on('change', '#rel_id', function() {
            if ($(this).val() != '') {
                // Always 'project' type
                if (true) {
                    $.get(admin_url + 'projects/get_rel_project_data/' + $(this).val() + '/' + taskid,
                        function(project) {
                            $("select[name='milestone']").html(project.milestones);
                            if (typeof(_milestone_selected_data) != 'undefined') {
                                $("select[name='milestone']").val(_milestone_selected_data.id);
                                $('input[name="duedate"]').val(_milestone_selected_data.due_date)
                            }
                            $("select[name='milestone']").selectpicker('refresh');

                            $("#assignees").html(project.assignees);
                            if (typeof(_current_member) != 'undefined') {
                                $("#assignees").val(_current_member);
                            }
                            $("#assignees").selectpicker('refresh')
                            if (project.billing_type == 3) {
                                $('.task-hours').addClass('project-task-hours');
                            } else {
                                $('.task-hours').removeClass('project-task-hours');
                            }

                            if (project.deadline) {
                                var $duedate = $('#_task_modal #duedate');
                                var currentSelectedTaskDate = $duedate.val();
                                $duedate.attr('data-date-end-date', project.deadline);
                                $duedate.datetimepicker('destroy');
                                init_datepicker($duedate);

                                if (currentSelectedTaskDate) {
                                    var dateTask = new Date(unformat_date(currentSelectedTaskDate));
                                    var projectDeadline = new Date(project.deadline);
                                    if (dateTask > projectDeadline) {
                                        $duedate.val(project.deadline_formatted);
                                    }
                                }
                            } else {
                                reset_task_duedate_input();
                            }
                            init_project_details('project', project.allow_to_view_tasks);
                        }, 'json');



                } else {
                    reset_task_duedate_input();
                }
            }
        });

        <?php if (!isset($task) && $rel_id != '') { ?>
        _rel_id.change();
        <?php } ?>

        // Removed rel_type change handler - always 'project'

    });

    <?php if (isset($_milestone_selected_data)) { ?>
    _milestone_selected_data = '<?php echo json_encode($_milestone_selected_data); ?>';
    _milestone_selected_data = JSON.parse(_milestone_selected_data);
    <?php } ?>

    function task_rel_select() {
        var serverData = {};
        serverData.rel_id = _rel_id.val();
        data.type = 'project'; // Always project
        init_ajax_search('project', _rel_id, serverData);
    }

    function init_project_details(type, tasks_visible_to_customer) {
        var wrap = $('.non-project-details');
        var wrap_task_hours = $('.task-hours');
        if (type == 'project') {
            if (wrap_task_hours.hasClass('project-task-hours') == true) {
                wrap_task_hours.removeClass('hide');
            } else {
                wrap_task_hours.addClass('hide');
            }
            wrap.addClass('hide');
            $('.project-details').removeClass('hide');
        } else {
            wrap_task_hours.removeClass('hide');
            wrap.removeClass('hide');
            $('.project-details').addClass('hide');
            $('.task-visible-to-customer').addClass('hide').prop('checked', false);
        }
        if (typeof(tasks_visible_to_customer) != 'undefined') {
            if (tasks_visible_to_customer == 1) {
                $('.task-visible-to-customer').removeClass('hide');
                $('.task-visible-to-customer input').prop('checked', true);
            } else {
                $('.task-visible-to-customer').addClass('hide')
                $('.task-visible-to-customer input').prop('checked', false);
            }
        }
    }

    function reset_task_duedate_input() {
        var $duedate = $('#_task_modal #duedate');
        $duedate.removeAttr('data-date-end-date');
        $duedate.datetimepicker('destroy');
        init_datepicker($duedate);
    }
    </script>
<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php echo form_open($this->uri->uri_string(), ['id' => 'project_form']); ?>

            <div class="col-md-8 col-md-offset-2">
                <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700">
                    <?php echo e($title); ?>
                </h4>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="tw-mt-3">
                            <div id="tab_project">


                                <?php
                        $disable_type_edit = '';
                        if (isset($project)) {
                            if ($project->billing_type != 1) {
                                if (total_rows(db_prefix() . 'tasks', ['rel_id' => $project->id, 'rel_type' => 'project', 'billable' => 1, 'billed' => 1]) > 0) {
                                    $disable_type_edit = 'disabled';
                                }
                            }
                        }
                        ?>
                                <?php $value = (isset($project) ? $project->name : ''); ?>
                                <?php echo render_input('name', 'Job Title', $value); ?>
                                <div class="form-group select-placeholder">
                                    <label for="clientid"
                                        class="control-label"><?php echo _l('project_customer'); ?></label>
                                    <select id="clientid" name="clientid" data-live-search="true" data-width="100%"
                                        class="ajax-search"
                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                        <?php $selected = (isset($project) ? $project->clientid : '');
                             if ($selected == '') {
                                 $selected = (isset($customer_id) ? $customer_id: '');
                             }
                             if ($selected != '') {
                                 $rel_data = get_relation_data('customer', $selected);
                                 $rel_val  = get_relation_values($rel_data, 'customer');
                                 echo '<option value="' . $rel_val['id'] . '" selected>' . $rel_val['name'] . '</option>';
                             } ?>
                                    </select>
                                </div>
                                <?php
                    if (isset($project) && $project->progress_from_tasks == 1) {
                        $value = $this->projects_model->calc_progress_by_tasks($project->id);
                    } elseif (isset($project) && $project->progress_from_tasks == 0) {
                        $value = $project->progress;
                    } else {
                        $value = 0;
                    }
                    ?>
                                <?php echo form_hidden('progress', $value); ?>
                                <?php echo form_hidden('progress_from_tasks', 0); ?>
                                <?php echo form_hidden('billing_type', 1); ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group select-placeholder">
                                            <label for="status"><?php echo _l('project_status'); ?></label>
                                            <div class="clearfix"></div>
                                            <select name="status" id="status" class="selectpicker" data-width="100%"
                                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                <?php foreach ($statuses as $status) { ?>
                                                <option value="<?php echo e($status['id']); ?>" <?php if (!isset($project) && $status['id'] == 2 || (isset($project) && $project->status == $status['id'])) {
                                    echo 'selected';
                                } ?>><?php echo e($status['name']); ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <?php
                         $selected = [];
                         if (isset($project_members)) {
                             foreach ($project_members as $member) {
                                 array_push($selected, $member['staff_id']);
                             }
                         } else {
                             array_push($selected, get_staff_user_id());
                         }
                        echo render_select('project_members[]', $staff, ['staffid', 'fullname', 'department_name'], 'project_members', $selected, ['multiple' => true, 'data-actions-box' => true], [], '', '', false);
                        ?>
                                    </div>
                                </div>
                                <?php echo form_hidden('project_cost', isset($project) ? $project->project_cost : ''); ?>
                                <?php if (isset($project) && project_has_recurring_tasks($project->id)) { ?>
                                <div class="alert alert-warning recurring-tasks-notice hide"></div>
                                <?php } ?>
                                <?php if (is_email_template_active('project-finished-to-customer')) { ?>
                                <div class="form-group project_marked_as_finished hide">
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" name="project_marked_as_finished_email_to_contacts"
                                            id="project_marked_as_finished_email_to_contacts">
                                        <label
                                            for="project_marked_as_finished_email_to_contacts"><?php echo _l('project_marked_as_finished_to_contacts'); ?></label>
                                    </div>
                                </div>
                                <?php } ?>
                                <?php if (isset($project)) { ?>
                                <div class="form-group mark_all_tasks_as_completed hide">
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" name="mark_all_tasks_as_completed"
                                            id="mark_all_tasks_as_completed">
                                        <label
                                            for="mark_all_tasks_as_completed"><?php echo _l('project_mark_all_tasks_as_completed'); ?></label>
                                    </div>
                                </div>
                                <div class="notify_project_members_status_change hide">
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" name="notify_project_members_status_change"
                                            id="notify_project_members_status_change">
                                        <label
                                            for="notify_project_members_status_change"><?php echo _l('notify_project_members_status_change'); ?></label>
                                    </div>
                                    <hr />
                                </div>
                                <?php } ?>
                                <?php echo form_hidden('estimated_hours', isset($project) ? $project->estimated_hours : ''); ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php $value = (isset($project) ? _d($project->start_date) : _d(date('Y-m-d'))); ?>
                                        <?php echo render_date_input('start_date', 'project_start_date', $value); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php $value = (isset($project) ? _d($project->deadline) : ''); ?>
                                        <?php echo render_date_input('deadline', 'project_deadline', $value); ?>
                                    </div>
                                </div>
                                <?php if (isset($project) && $project->date_finished != null && $project->status == 4) { ?>
                                <?php echo render_datetime_input('date_finished', 'project_completed_date', _dt($project->date_finished)); ?>
                                <?php } ?>
                                <div class="form-group">
                                    <label for="tags" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i>
                                        Sub-Brands</label>
                                    <input type="text" class="tagsinput" id="tags" name="tags"
                                        value="<?php echo(isset($project) ? prep_tags_input(get_tags_in($project->id, 'project')) : ''); ?>"
                                        data-role="tagsinput">
                                </div>
                                <?php $rel_id_custom_field = (isset($project) ? $project->id : false); ?>
                                <?php echo render_custom_fields('projects', $rel_id_custom_field); ?>
                                <p class="bold"><?php echo _l('project_description'); ?></p>
                                <?php $contents = ''; if (isset($project)) {
                            $contents           = $project->description;
                        } ?>
                                <?php echo render_textarea('description', '', $contents, [], [], '', 'tinymce'); ?>

                                <?php if (isset($estimate)) {?>
                                <hr class="hr-panel-separator" />
                                <h5 class="font-medium"><?php echo _l('estimate_items_convert_to_tasks') ?></h5>
                                <input type="hidden" name="estimate_id" value="<?php echo $estimate->id ?>">
                                <div class="row">
                                    <?php foreach ($estimate->items as $item) { ?>
                                    <div class="col-md-8 border-right">
                                        <div class="checkbox mbot15">
                                            <input type="checkbox" name="items[]" value="<?php echo $item['id'] ?>"
                                                checked id="item-<?php echo $item['id'] ?>">
                                            <label for="item-<?php echo $item['id'] ?>">
                                                <h5 class="no-mbot no-mtop text-uppercase">
                                                    <?php echo $item['description'] ?>
                                                </h5>
                                                <span class="text-muted"><?php echo $item['long_description'] ?></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div data-toggle="tooltip"
                                            title="<?php echo _l('task_single_assignees_select_title'); ?>">
                                            <?php echo render_select('items_assignee[]', $staff, ['staffid', 'fullname', 'department_name'], '', get_staff_user_id(), ['data-actions-box' => true], [], '', '', false); ?>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>
                                <?php } ?>
                                <hr class="hr-panel-separator" />

                                <?php if (is_email_template_active('assigned-to-project')) { ?>
                                <div class="checkbox checkbox-primary tw-mb-0">
                                    <input type="checkbox" name="send_created_email" id="send_created_email">
                                    <label
                                        for="send_created_email"><?php echo _l('project_send_created_email'); ?></label>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <button type="submit" data-form="#project_form" class="btn btn-primary" autocomplete="off"
                            data-loading-text="<?php echo _l('wait_text'); ?>">
                            <?php echo _l('submit'); ?>
                        </button>
                    </div>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
<?php if (isset($project)) { ?>
var original_project_status = '<?php echo e($project->status); ?>';
<?php } ?>

$(function() {

    $contacts_select = $('#notify_contacts'),
        $contacts_wrapper = $('#notify_contacts_wrapper'),
        $clientSelect = $('#clientid'),
        $contact_notification_select = $('#contact_notification');

    init_ajax_search('contacts', $contacts_select, {
        rel_id: $contacts_select.val(),
        type: 'contacts',
        extra: {
            client_id: function() {
                return $clientSelect.val();
            }
        }
    });

    if ($clientSelect.val() == '') {
        $contacts_select.prop('disabled', true);
        $contacts_select.selectpicker('refresh');
    } else {
        $contacts_select.siblings().find('input[type="search"]').val(' ').trigger('keyup');
    }

    $clientSelect.on('changed.bs.select', function() {
        if ($clientSelect.selectpicker('val') == '') {
            $contacts_select.prop('disabled', true);
        } else {
            $contacts_select.siblings().find('input[type="search"]').val(' ').trigger('keyup');
            $contacts_select.prop('disabled', false);
        }
        deselect_ajax_search($contacts_select[0]);
        $contacts_select.find('option').remove();
        $contacts_select.selectpicker('refresh');
    });

    $contact_notification_select.on('changed.bs.select', function() {
        if ($contact_notification_select.selectpicker('val') == 2) {
            $contacts_select.siblings().find('input[type="search"]').val(' ').trigger('keyup');
            $contacts_wrapper.removeClass('hide');
        } else {
            $contacts_wrapper.addClass('hide');
            deselect_ajax_search($contacts_select[0]);
        }
    });

    // Billing type is fixed to 1 (fixed rate), no change handler needed

    appValidateForm($('form'), {
        name: 'required',
        clientid: 'required',
        start_date: 'required',
        'notify_contacts[]': {
            required: {
                depends: function() {
                    return !$contacts_wrapper.hasClass('hide');
                }
            }
        },
    });

    $('select[name="status"]').on('change', function() {
        var status = $(this).val();
        var mark_all_tasks_completed = $('.mark_all_tasks_as_completed');
        var notify_project_members_status_change = $('.notify_project_members_status_change');
        mark_all_tasks_completed.removeClass('hide');
        if (typeof(original_project_status) != 'undefined') {
            if (original_project_status != status) {

                mark_all_tasks_completed.removeClass('hide');
                notify_project_members_status_change.removeClass('hide');

                if (status == 4 || status == 5 || status == 3) {
                    $('.recurring-tasks-notice').removeClass('hide');
                    var notice = "<?php echo _l('project_changing_status_recurring_tasks_notice'); ?>";
                    notice = notice.replace('{0}', $(this).find('option[value="' + status + '"]').text()
                        .trim());
                    $('.recurring-tasks-notice').html(notice);
                    $('.recurring-tasks-notice').append(
                        '<input type="hidden" name="cancel_recurring_tasks" value="true">');
                    mark_all_tasks_completed.find('input').prop('checked', true);
                } else {
                    $('.recurring-tasks-notice').html('').addClass('hide');
                    mark_all_tasks_completed.find('input').prop('checked', false);
                }
            } else {
                mark_all_tasks_completed.addClass('hide');
                mark_all_tasks_completed.find('input').prop('checked', false);
                notify_project_members_status_change.addClass('hide');
                $('.recurring-tasks-notice').html('').addClass('hide');
            }
        }

        if (status == 4) {
            $('.project_marked_as_finished').removeClass('hide');
        } else {
            $('.project_marked_as_finished').addClass('hide');
            $('.project_marked_as_finished').prop('checked', false);
        }
    });

    $('form').on('submit', function() {
        $('#available_features,#available_features option').prop('disabled', false);
    });

    // Progress slider removed - progress is now hidden and set to 0

    $('#project-settings-area input').on('change', function() {
        if ($(this).attr('id') == 'view_tasks' && $(this).prop('checked') == false) {
            $('#create_tasks').prop('checked', false).prop('disabled', true);
            $('#edit_tasks').prop('checked', false).prop('disabled', true);
            $('#view_task_comments').prop('checked', false).prop('disabled', true);
            $('#comment_on_tasks').prop('checked', false).prop('disabled', true);
            $('#view_task_attachments').prop('checked', false).prop('disabled', true);
            $('#view_task_checklist_items').prop('checked', false).prop('disabled', true);
            $('#upload_on_tasks').prop('checked', false).prop('disabled', true);
            $('#view_task_total_logged_time').prop('checked', false).prop('disabled', true);
        } else if ($(this).attr('id') == 'view_tasks' && $(this).prop('checked') == true) {
            $('#create_tasks').prop('disabled', false);
            $('#edit_tasks').prop('disabled', false);
            $('#view_task_comments').prop('disabled', false);
            $('#comment_on_tasks').prop('disabled', false);
            $('#view_task_attachments').prop('disabled', false);
            $('#view_task_checklist_items').prop('disabled', false);
            $('#upload_on_tasks').prop('disabled', false);
            $('#view_task_total_logged_time').prop('disabled', false);
        }
    });

    // Auto adjust customer permissions based on selected project visible tabs
    // Eq Project creator disable TASKS tab, then this function will auto turn off customer project option Allow customer to view tasks

    $('#available_features').on('change', function() {
        $("#available_features option").each(function() {
            if ($(this).data('linked-customer-option') && !$(this).is(':selected')) {
                var opts = $(this).data('linked-customer-option').split(',');
                for (var i = 0; i < opts.length; i++) {
                    var project_option = $('#' + opts[i]);
                    project_option.prop('checked', false);
                    if (opts[i] == 'view_tasks') {
                        project_option.trigger('change');
                    }
                }
            }
        });
    });
    $("#view_tasks").trigger('change');
    <?php if (!isset($project)) { ?>
    $('#available_features').trigger('change');
    <?php } ?>
});
</script>
</body>

</html>

<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<h4 class="customer-profile-group-heading"><?php echo _l('tasks'); ?></h4>
<?php if (isset($client)) {
    init_relation_tasks_table([ 'data-new-rel-id' => $client->userid, 'data-new-rel-type' => 'customer']);
} ?>
<style>
/* Completely hide New Task buttons and filter options from client tasks view */
.new-task-relation,
.btn-primary:has(.fa-plus),
a[href*="new_task"],
#tasks_related_filter,
.checkbox-inline input[name="tasks_related_to[]"],
.filters-wrapper,
app-filters {
    display: none !important;
}

/* Hide any Vue filter components that might be loaded */
.vue-app-filters,
[data-filter-wrapper] {
    display: none !important;
}
</style>
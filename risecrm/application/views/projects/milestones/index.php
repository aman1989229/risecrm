<div class="panel">
    <div class="tab-title clearfix">
        <h4><?php echo lang('milestones'); ?></h4>
        <div class="title-button-group">
            <?php
            if ($this->login_user->is_admin) {
                echo modal_anchor(get_uri("projects/milestone_modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_milestone'), array("class" => "btn btn-default", "title" => lang('add_milestone'), "data-post-project_id" => $project_id));
            }
            ?>
        </div>
    </div>

    <div class="table-responsive">
        <table id="milestone-table" class="display" width="100%">            
        </table>
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function() {
        var optionVisibility = false;
        if ("<?php echo $this->login_user->is_admin; ?>" == 1) {
            optionVisibility = true;
        }
        $("#milestone-table").appTable({
            source: '<?php echo_uri("projects/milestones_list_data/" . $project_id) ?>',
            columns: [
                {title: '<?php echo lang("due_date") ?>', "class": "text-center option w100"},
                {title: '<?php echo lang("title") ?>'},
                {title: '<?php echo lang("progress") ?>'},
                {visible: optionVisibility, title: '<i class="fa fa-bars"></i>', "class": "text-center option w100"}
            ],
            printColumns: [0, 1, 2, 3]
        });
    });
</script>
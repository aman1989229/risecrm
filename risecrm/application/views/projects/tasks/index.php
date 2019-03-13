<div class="panel">
    <div class="tab-title clearfix">
        <h4><?php echo lang('tasks'); ?></h4>
        <div class="title-button-group">
            <?php echo modal_anchor(get_uri("projects/task_modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_task'), array("class" => "btn btn-default", "title" => lang('add_task'), "data-post-project_id" => $project_id)); ?>
        </div>
    </div>
    <div class="table-responsive">
        <table id="task-table" class="display" width="100%">            
        </table>
    </div>
</div>
<?php
load_css(array(
    "assets/js/dropzone/dropzone.min.css",
    "assets/js/magnific-popup/magnific-popup.css",
));
load_js(array(
    "assets/js/dropzone/dropzone.min.js",
    "assets/js/magnific-popup/jquery.magnific-popup.min.js",
));
?>
<script type="text/javascript">
    $(document).ready(function() {
        $("#task-table").appTable({
            source: '<?php echo_uri("projects/tasks_list_data/" . $project_id) ?>',
            order: [[2, "asc"]],
            filterDropdown: [{name: "milestone_id", class: "w200", options: <?php echo $milestone_dropdown; ?>}],
            checkBoxes: [
                {text: '<?php echo lang("to_do") ?>', name: "status", value: "to_do", isChecked: true},
                {text: '<?php echo lang("in_progress") ?>', name: "status", value: "in_progress", isChecked: true},
                {text: '<?php echo lang("done") ?>', name: "status", value: "done", isChecked: false}
            ],
            columns: [
                {title: '<?php echo lang("id") ?>'},
                {title: '<?php echo lang("title") ?>'},
                {title: '<?php echo lang("deadline") ?>'},
                {visible: false, searchable: false},
                {title: '<?php echo lang("assigned_to") ?>', "class": "min-w150"},
                {title: '<?php echo lang("status") ?>'},
                {title: '<i class="fa fa-bars"></i>', "class": "text-center option w100"}
            ],
            printColumns: [0, 1, 2, 4, 5],
            xlsColumns: [0, 1, 2, 4, 5],
            rowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                $('td:eq(0)', nRow).addClass(aData[7]);
            }
        });
    });
</script>

<?php $this->load->view("projects/tasks/update_task_script"); ?>
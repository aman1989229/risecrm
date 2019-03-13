<div id="page-content" class="p20 clearfix">
    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1> <?php echo lang('my_tasks'); ?></h1>
        </div>
        <div class="table-responsive">
            <table id="task-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
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
            source: '<?php echo_uri("projects/my_tasks_list_data") ?>',
            order: [[2, "asc"]],
            filterDropdown: [{name: "project_id", class: "w200", options: <?php echo $projects_dropdown; ?>}],
            checkBoxes: [
                {text: '<?php echo lang("to_do") ?>', name: "status", value: "to_do", isChecked: true},
                {text: '<?php echo lang("in_progress") ?>', name: "status", value: "in_progress", isChecked: true},
                {text: '<?php echo lang("done") ?>', name: "status", value: "done", isChecked: false}
            ],
            columns: [
                {title: '<?php echo lang("id") ?>'},
                {title: '<?php echo lang("title") ?>'},
                {title: '<?php echo lang("deadline") ?>'},
                {title: '<?php echo lang("project") ?>', "class": "min-w150"},
                {visible: false, searchable: false},
                {title: '<?php echo lang("status") ?>'},
                {targets: [4], visible: false, searchable: false}
            ],
            printColumns: [0, 1, 2, 3, 5],
            xlsColumns: [0, 1, 2, 3, 5],
            rowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                $('td:eq(0)', nRow).addClass(aData[7]);
            }
        });

    });
</script>

<?php $this->load->view("projects/tasks/update_task_script"); ?>
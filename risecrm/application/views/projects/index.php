<div id="page-content" class="p20 clearfix">
    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1><?php echo lang('projects'); ?></h1>
            <div class="title-button-group">
                <?php
                if ($this->login_user->is_admin) {
                    echo modal_anchor(get_uri("projects/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_project'), array("class" => "btn btn-default", "title" => lang('add_project')));
                }
                ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="project-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        var optionVisibility = false;
        if ("<?php echo $this->login_user->is_admin; ?>" == 1) {
            optionVisibility = true;
        }

        $("#project-table").appTable({
            source: '<?php echo_uri("projects/list_data") ?>',
            radioButtons: [{text: '<?php echo lang("open") ?>', name: "status", value: "open", isChecked: true}, {text: '<?php echo lang("completed") ?>', name: "status", value: "completed", isChecked: false}, {text: '<?php echo lang("canceled") ?>', name: "status", value: "canceled", isChecked: false}],
            columns: [
                {title: '<?php echo lang("id") ?>', "class": "w50"},
                {title: '<?php echo lang("title") ?>'},
                {title: '<?php echo lang("client") ?>', "class": "w15p"},
                {title: '<?php echo lang("deadline") ?>', "class": "w10p"},
                {title: '<?php echo lang("progress") ?>', "class": "w15p"},
                {title: '<?php echo lang("status") ?>', "class": "w10p"},
                {visible: optionVisibility, title: '<i class="fa fa-bars"></i>', "class": "text-center option w100"}
            ],
            order: [[1, "desc"]],
            printColumns: [0, 1, 2, 3, 5],
            xlsColumns: [0, 1, 2, 3, 5]
        });
    });
</script>
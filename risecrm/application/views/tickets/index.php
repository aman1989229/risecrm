<div id="page-content" class="p20 clearfix">
    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1><?php echo lang('tickets'); ?></h1>
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("tickets/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_ticket'), array("class" => "btn btn-default", "title" => lang('add_ticket'))); ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="ticket-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $("#ticket-table").appTable({
            source: '<?php echo_uri("tickets/list_data") ?>',
            order: [[4, "desc"]],
            radioButtons: [{text: '<?php echo lang("open") ?>', name: "status", value: "open", isChecked: true}, {text: '<?php echo lang("closed") ?>', name: "status", value: "closed", isChecked: false}],
            columns: [
                {title: '<?php echo lang("ticket_id") ?>', "class": "w10p"},
                {title: '<?php echo lang("title") ?>', "class": "w40p"},
                {title: '<?php echo lang("client") ?>', "class": "w15p"},
                {title: '<?php echo lang("ticket_type") ?>', "class": "w15p"},
                {title: '<?php echo lang("last_activity") ?>', "class": "w10p"},
                {title: '<?php echo lang("status") ?>', "class": "w10p"},
                {title: '<?php echo lang("assigned_to") ?>', "class": "w15p"},
                {title: '<i class="fa fa-bars"></i>', "class": "text-center option w50"}
            ],
            printColumns: [0, 1, 2, 3, 4, 5, 6],
            xlsColumns: [0, 1, 2, 3, 4, 5, 6]
        });
    });
</script>

<?php
load_css(array(
    "assets/js/dropzone/dropzone.min.css"
));
load_js(array(
    "assets/js/dropzone/dropzone.min.js"
));
?>
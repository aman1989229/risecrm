<?php if (isset($page_type) && $page_type === "full") { ?>
    <div id="page-content" class="m20 clearfix">
    <?php } ?>

    <div class="panel">
        <?php if (isset($page_type) && $page_type === "full") { ?>
            <div class="page-title clearfix">
                <h1><?php echo lang('tickets'); ?></h1>
                <div class="title-button-group">
                    <?php echo modal_anchor(get_uri("tickets/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_ticket'), array("class" => "btn btn-default", "data-post-client_id" => $client_id, "title" => lang('add_ticket'))); ?>
                </div>
            </div>
        <?php } else { ?>
            <div class="tab-title clearfix">
                <h4><?php echo lang('tickets'); ?></h4>
                <div class="title-button-group">
                    <?php echo modal_anchor(get_uri("tickets/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_ticket'), array("class" => "btn btn-default", "data-post-client_id" => $client_id, "title" => lang('add_ticket'))); ?>
                </div>
            </div>
        <?php } ?>

        <div class="table-responsive">
            <table id="ticket-table" class="display" width="100%">            
            </table>
        </div>
    </div>
    <?php if (isset($page_type) && $page_type === "full") { ?>
    </div>
<?php } ?>

<?php
load_css(array(
    "assets/js/dropzone/dropzone.min.css"
));
load_js(array(
    "assets/js/dropzone/dropzone.min.js"
));
?>

<script type="text/javascript">
    $(document).ready(function() {
        $("#ticket-table").appTable({
            source: '<?php echo_uri("tickets/ticket_list_data_of_client/" . $client_id) ?>',
            order: [[4, "desc"]],
            columns: [
                {title: '<?php echo lang("ticket_id") ?>', "class": "w10p"},
                {title: '<?php echo lang("title") ?>'},
                {targets: [2], visible: false, searchable: false},
                {title: '<?php echo lang("ticket_type") ?>', "class": "w20p"},
                {title: '<?php echo lang("last_activity") ?>', "class": "w15p"},
                {title: '<?php echo lang("status") ?>', "class": "w10p"}
            ],
            printColumns: [0, 1, 3, 4, 5],
            xlsColumns: [0, 1, 3, 4, 5]
        });
    });
</script>
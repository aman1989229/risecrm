<div id="page-content" class="clearfix">
<style type="text/css">
    .message-buttons .btn{
    border:1px solid #fff;
    color: #fff;
    margin-right: 10px;
}
</style>
    <div style="background: linear-gradient(90deg,#70d467 0,#087cb1);" class="m15 p10 message-buttons">

         <?php echo modal_anchor(get_uri("messages/modal_form"), "<i class='fa fa-edit'></i> " . lang('compose'), array("class" => "btn", "title" => lang('send_message'))); ?> 

        <?php echo anchor(get_uri("messages/inbox"), "<i class='fa fa-inbox'></i> " . lang('inbox'), array("class" => "btn", "title" => lang('inbox'))); ?>

        <?php echo anchor(get_uri("messages/sent_items"), "<i class='fa fa-send'></i> " . lang('sent_items'), array("class" => "btn", "title" => lang('sent_items'))); ?>

    </div>

    <div class="clearfix">
        <div class="col-sm-12 col-md-5">
            <div id="message-list-box" class="panel panel-default">
                <div class="panel-heading clearfix">
                    <div class="pull-left p5">
                        <?php
                        if ($mode === "inbox") {
                            echo "<i class='fa fa-inbox'></i> " . lang('inbox');
                        } else if ($mode === "sent_items") {
                            echo "<i class='fa fa-send'></i> " . lang('sent_items');
                        }
                        ?>
                    </div>
                    <div class="pull-right">
                        <input type="text" id="search-messages" class="datatable-search" placeholder="Search...">
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="message-table" class="display no-thead no-padding clickable" cellspacing="0" width="100%">            
                    </table>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-7">
            <div id="message-details-section" class="panel panel-default"> 
                <div id="empty-message" class="text-center mb15 box">
                    <div class="box-content" style="vertical-align: middle; height: 100%"> 
                        <div><?php echo lang("select_a_message"); ?></div>
                        <span class="fa fa-envelope-o" style="font-size: 500%; color:#f6f8f8"></span>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<style type="text/css">
    .datatable-tools:first-child {
        display:  none;
    }
</style>

<script type="text/javascript">
    $(document).ready(function() {
        var autoSelectIndex = "<?php echo $auto_select_index; ?>";
        $("#message-table").appTable({
            source: '<?php echo_uri("messages/list_data/" . $mode) ?>',
            order: [[1, "desc"]],
            columns: [
                {title: '<?php echo lang("message") ?>'},
                {targets: [1], visible: false},
                {targets: [2], visible: false}
            ],
            onInitComplete: function() {
                if (autoSelectIndex) {
                    //automatically select the message
                    var $tr = $("[data-index=" + autoSelectIndex + "]").closest("tr");
                    if ($tr.length)
                        $tr.trigger("click");
                }

                var $message_list = $("#message-list-box"),
                        $empty_message = $("#empty-message");
                if ($empty_message.length && $message_list.length) {
                    $empty_message.height($message_list.height());
                }
            }
        });

        var messagesTable = $('#message-table').DataTable();
        $('#search-messages').keyup(function() {
            messagesTable.search($(this).val()).draw();
        })


        /*load a message details*/
        $("body").on("click", "tr", function() {
            //remove unread class
            $(this).find(".unread").removeClass("unread");

            //don't load this message if already has selected.
            if (!$(this).hasClass("active")) {
                var message_id = $(this).find(".message-row").attr("data-id"),
                        reply = $(this).find(".message-row").attr("data-reply");
                if (message_id) {
                    $("tr.active").removeClass("active");
                    $(this).addClass("active");
                    $.ajax({
                        url: "<?php echo get_uri("messages/view"); ?>/" + message_id + "/<?php echo $mode ?>/" + reply,
                        dataType: "json",
                        success: function(result) {
                            if (result.success) {
                                $("#message-details-section").html(result.data);
                            } else {
                                appAlert.error(result.message);
                            }
                        }
                    });
                }
            }
        });

    });
</script>
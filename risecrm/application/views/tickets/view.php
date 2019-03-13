<div id="page-content" class="p20 clearfix">
    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1><?php echo lang('ticket') . ": #" . $ticket_info->id . " - " . $ticket_info->title ?></h1>
            <div class="title-button-group">
                <?php
                $status = ($ticket_info->status === "closed") ? "closed" : "open";
                echo "<button class='btn btn-info btn-sm' data-id='$ticket_info->id'  data-value='$status' data-act='update-ticket-status'><i class='fa fa-edit'></i> " . lang("change_status") . "</button>";
                ?>
            </div>
        </div>
        <div class="panel-body">
            <div id="ticket-title-section">
                <?php $this->load->view("tickets/ticket_sub_title"); ?>
            </div>

            <?php foreach ($comments as $comment) { ?>
                <?php $this->load->view("tickets/comment_row", array("comment" => $comment)); ?>
            <?php } ?>

            <div id="comment-form-container" class="pr15">
                <?php echo form_open(get_uri("tickets/save_comment"), array("id" => "comment-form", "class" => "general-form", "role" => "form")); ?>
                <div class="p15 box">
                    <div class="box-content avatar avatar-md pr15">
                        <img src="<?php echo get_avatar($this->login_user->image); ?>" alt="..." />
                    </div>

                    <div id="ticket-comment-dropzone" class="post-dropzone box-content form-group">
                        <input type="hidden" name="ticket_id" value="<?php echo $ticket_info->id; ?>">
                        <?php
                        echo form_textarea(array(
                            "id" => "description",
                            "name" => "description",
                            "class" => "form-control",
                            "placeholder" => lang('write_a_comment'),
                            "data-rule-required" => true,
                            "data-msg-required" => lang("field_required"),
                        ));
                        ?>
                        <?php $this->load->view("includes/dropzone_preview"); ?>
                        <footer class="panel-footer b-a clearfix ">
                            <button class="btn btn-default upload-file-button pull-left btn-sm round" type="button" style="color:#7988a2"><i class='fa fa-camera'></i> <?php echo lang("upload_file"); ?></button>
                            <button class="btn btn-primary pull-right btn-sm " type="submit"><i class='fa fa-paper-plane'></i> <?php echo lang("post_comment"); ?></button>
                        </footer>
                    </div>

                </div>
                <?php echo form_close(); ?>
            </div>
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
        var uploadUrl = "<?php echo get_uri("tickets/upload_file"); ?>";
        var validationUrl = "<?php echo get_uri("tickets/validate_ticket_file"); ?>";

        var dropzone = attachDropzoneWithForm("#ticket-comment-dropzone", uploadUrl, validationUrl);

        $("#comment-form").appForm({
            isModal: false,
            onSuccess: function(result) {
                $("#description").val("");
                $(result.data).insertBefore("#comment-form-container");
                appAlert.success(result.message, {duration: 10000});

                dropzone.removeAllFiles();
            }
        });



        $('body').on('click', '[data-act=update-ticket-status]', function() {
            $(this).editable({
                type: "select2",
                placement: "bottom",
                pk: 1,
                name: 'status',
                display: false,
                ajaxOptions: {
                    type: 'post',
                    dataType: 'json'
                },
                value: $(this).attr('data-value'),
                url: '<?php echo_uri("tickets/save_ticket_status") ?>/' + $(this).attr('data-id'),
                showbuttons: false,
                source: [
                    {value: "open", text: "<?php echo lang('open'); ?>"},
                    {value: "closed", text: "<?php echo lang('close'); ?>"}
                ],
                success: function(response, newValue) {
                    if (response.success) {
                        $("#ticket-title-section").html(response.data);
                    } else {
                        appAlert.error(response.message);
                    }
                }
            });
            $(this).editable("show");
        });

    });
</script>

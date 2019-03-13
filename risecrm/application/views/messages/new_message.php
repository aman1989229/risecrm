
<?php echo form_open(get_uri("messages/send_message/"), array("id" => "new-message-form", "class" => "general-form dashed-row", "role" => "form")); ?>
<div class="panel">
    <div class="panel-default panel-heading">
        <h4><?php echo lang("send_message") . " (" . lang('private') . ")"; ?></h4>
    </div>
    <div class="panel-body">
        <div class="form-group">
            <label for="message_to" class=" col-md-12"><?php echo lang('to'); ?></label>
            <div class=" col-md-12">
                <?php
                echo form_dropdown("message_to", $team_members_dropdown, array(), "class='select2'");
                ?>
            </div>
        </div>
        <div class="form-group">
            <label for="address" class=" col-md-12"><?php echo lang('message'); ?></label>
            <div class=" col-md-12">
                <?php
                echo form_textarea(array(
                    "id" => "message",
                    "name" => "message",
                    "class" => "form-control",
                    "placeholder" => lang('write_a_message')
                ));
                ?>
            </div>
        </div>


    </div>
    <div class="panel-footer">
        <button type="submit" class="btn btn-primary"><span class="fa fa-envelope"></span> <?php echo lang('send'); ?></button>
    </div>
</div>
<?php echo form_close(); ?>


<script type="text/javascript">
    $(document).ready(function() {
        $("#new-message-form").appForm({
            isModal: false,
            onSuccess: function(result) {
                appAlert.success(result.message, {duration: 10000});
            }
        });

        $("#new-message-form .select2").select2();
    });
</script>    
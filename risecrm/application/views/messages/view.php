<?php
if ($mode === "inbox") {
    if ($is_reply) {
        $user_image = $this->login_user->image;
    } else {
        $user_image = $message_info->user_image;
    }
} if ($mode === "sent_items") {
    if ($is_reply) {
        $user_image = $message_info->user_image;
    } else {
        $user_image = $this->login_user->image;
    }
}
?>

<div class="media b-b p15 m0 bg-white">
    <div class="media-left"> 
        <span class="avatar avatar-sm">
            <img src="<?php echo get_avatar($user_image); ?>" alt="..." />
        </span>
    </div>
    <div class="media-body w100p">
        <div class="media-heading clearfix">
            <?php if ($mode === "sent_items" && $is_reply != "1" || $mode === "inbox" && $is_reply == "1") { ?>
                <label class="label label-success large"><?php echo lang("to"); ?></label>
            <?php } ?>
            <?php echo get_team_member_profile_link($message_info->from_user_id, $message_info->user_name, array("class"=>"dark strong")); ?>
            <span class="text-off pull-right"><?php echo format_to_relative_time($message_info->created_at); ?></span>
        </div>
        <p class="p5 b-b b-turquoise">
            <?php echo lang("subject"); ?>:  <?php echo $message_info->subject; ?>  
        </p>
        <p>
            <?php echo nl2br(link_it($message_info->message)); ?>
        </p>
        <p>
            <button class="btn btn-danger  btn-sm  pull-right"  style="margin-right: 15px; width: 30px;
  text-align: center;
  font-size: 12px;
  line-height: 1.42;
  border-radius: 15px;
  "><a href="<?php echo site_url('messages/delete/'.$message_info->id);?>" style="color: #fff;"><i class='fa fa-trash'></i></a></button>
        </p>
            
    </div>
    <?php foreach ($replies as $reply_info) { ?>

   <?php $this->load->view("messages/reply_row", array("reply_info" => $reply_info));  ?>
     <?php } 
   ?>
</div>




<div id="reply-form-container" class="pr15">
    <?php echo form_open(get_uri("messages/reply"), array("id" => "message-reply-form", "class" => "general-form", "role" => "form")); ?>
    <div class="p15 box b-b">
        <div class="box-content avatar avatar-md pr15">
            <img src="<?php echo get_avatar($this->login_user->image); ?>" alt="..." />
        </div>
        <div class="box-content form-group">
            <input type="hidden" name="message_id" value="<?php echo $encrypted_message_id; ?>">
            <?php
            echo form_textarea(array(
                "id" => "reply_message",
                "name" => "reply_message",
                "class" => "form-control",
                "placeholder" => lang('write_a_reply'),
                "data-rule-required" => true,
                "data-msg-required" => lang("field_required"),
            ));
            ?>
            <footer class="panel-footer b-a clearfix ">
                <button class="btn btn-primary pull-right btn-sm " type="submit"><i class='fa fa-reply'></i> <?php echo lang("reply"); ?></button>
            </footer>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $("#message-reply-form").appForm({
            isModal: false,
            onSuccess: function(result) {
                $("#reply_message").val("");
                $(result.data).insertBefore("#reply-form-container");
                appAlert.success(result.message, {duration: 10000});
            }
        });

    });
</script>

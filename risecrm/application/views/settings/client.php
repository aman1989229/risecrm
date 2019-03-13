<div id="page-content" class="p20 row">
    <div class="col-sm-3 col-lg-2">
        <?php
        $tab_view['active_tab'] = "client";
        $this->load->view("settings/tabs", $tab_view);
        ?>
    </div>

    <div class="col-sm-9 col-lg-10">
        <?php echo form_open(get_uri("settings/save_client_settings"), array("id" => "client-settings-form", "class" => "general-form dashed-row", "role" => "form")); ?>
        <div class="panel">
            <div class="panel-default panel-heading">
                <h4><?php echo lang("client_settings"); ?></h4>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="disable_client_login_and_signup" class="col-md-2"><?php echo lang('disable_client_login_and_signup'); ?></label>
                    <div class="col-md-10">
                        <?php
                        echo form_checkbox("disable_client_login_and_signup", "1", get_setting("disable_client_login_and_signup") ? true : false, "id='disable_client_login_and_signup' class='ml15'");
                        ?>
                        <span id="disable-login-help-block" class="ml10 <?php echo get_setting("disable_client_login_and_signup") ? "" : "hide" ?>"><i class="fa fa-warning text-warning"></i> <?php echo lang("disable_client_login_help_message"); ?></span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="client_message_users" class=" col-md-2"><?php echo lang('who_can_send_or_receive_message_to_or_from_clients'); ?></label>
                    <div class=" col-md-9">
                        <?php
                        echo form_input(array(
                            "id" => "client_message_users",
                            "name" => "client_message_users",
                            "value" => get_setting("client_message_users"),
                            "class" => "form-control",
                            "placeholder" => lang('team_members')
                        ));
                        ?>
                    </div>
                </div>

            </div>
            <div class="panel-footer">
                <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $("#client-settings-form").appForm({
            isModal: false,
            onSuccess: function(result) {
                appAlert.success(result.message, {duration: 10000});
            }
        });
        $("#client_message_users").select2({
            multiple: true,
            data: <?php echo ($members_dropdown); ?>
        });


        //show/hide disable_client_login_and_signup help message
        $("#disable_client_login_and_signup").click(function() {
            if ($(this).is(":checked")) {
                $("#disable-login-help-block").removeClass("hide");
            } else {
                $("#disable-login-help-block").addClass("hide");
            }
        });
    });
</script>
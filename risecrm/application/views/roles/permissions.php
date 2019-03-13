<div class="tab-content">
    <?php echo form_open(get_uri("roles/save_permissions/"), array("id" => "permissions-form", "class" => "general-form dashed-row", "role" => "form")); ?>
    <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
    <div class="panel">
        <div class="panel-default panel-heading">
            <h4><?php echo lang('permissions') . ": " . $model_info->title; ?></h4>
        </div>
        <div class="panel-body">

            <ul class="permission-list">
                <li>
                    <h5><?php echo lang("can_manage_team_members_leave"); ?> <span class="help" data-toggle="tooltip" title="Assign, approve or reject leave applications"><i class="fa fa-question-circle"></i></span> </h5>
                    <div>
                        <?php
                        echo form_radio(array(
                            "id" => "leave_permission_no",
                            "name" => "leave_permission",
                            "value" => "",
                            "class" => "leave_permission toggle_specific",
                                ), $leave, ($leave === "") ? true : false);
                        ?>
                        <label for="leave_permission_no"><?php echo lang("no"); ?></label>
                    </div>
                    <div>
                        <?php
                        echo form_radio(array(
                            "id" => "leave_permission_all",
                            "name" => "leave_permission",
                            "value" => "all",
                            "class" => "leave_permission toggle_specific",
                                ), $leave, ($leave === "all") ? true : false);
                        ?>
                        <label for="leave_permission_all"><?php echo lang("yes_all_members"); ?></label>
                    </div>
                    <div class="form-group">
                        <?php
                        echo form_radio(array(
                            "id" => "leave_permission_specific",
                            "name" => "leave_permission",
                            "value" => "specific",
                            "class" => "leave_permission toggle_specific",
                                ), $leave, ($leave === "specific") ? true : false);
                        ?>
                        <label for="leave_permission_specific"><?php echo lang("yes_specific_members_or_teams") . " (" . lang("excluding_his_her_leaves") . ")"; ?>:</label>
                        <div class="specific_dropdown">
                            <input type="text" value="<?php echo $leave_specific; ?>" name="leave_permission_specific" id="leave_specific_dropdown" class="w100p validate-hidden"  data-rule-required="true" data-msg-required="<?php echo lang('field_required'); ?>" placeholder="<?php echo lang('choose_members_and_or_teams'); ?>"  />    
                        </div>

                    </div>
                </li>
                <li>
                    <h5><?php echo lang("can_manage_team_members_timecards"); ?> <span class="help" data-toggle="tooltip" title="Add, edit and delete time cards"><i class="fa fa-question-circle"></i></span></h5>
                    <div>
                        <?php
                        echo form_radio(array(
                            "id" => "attendance_permission_no",
                            "name" => "attendance_permission",
                            "value" => "",
                            "class" => "attendance_permission toggle_specific",
                                ), $attendance, ($attendance === "") ? true : false);
                        ?>
                        <label for="attendance_permission_no"><?php echo lang("no"); ?> </label>
                    </div>
                    <div>
                        <?php
                        echo form_radio(array(
                            "id" => "attendance_permission_all",
                            "name" => "attendance_permission",
                            "value" => "all",
                            "class" => "attendance_permission toggle_specific",
                                ), $attendance, ($attendance === "all") ? true : false);
                        ?>
                        <label for="attendance_permission_all"><?php echo lang("yes_all_members"); ?></label>
                    </div>
                    <div class="form-group">
                        <?php
                        echo form_radio(array(
                            "id" => "attendance_permission_specific",
                            "name" => "attendance_permission",
                            "value" => "specific",
                            "class" => "attendance_permission toggle_specific",
                                ), $attendance, ($attendance === "specific") ? true : false);
                        ?>
                        <label for="attendance_permission_specific"><?php echo lang("yes_specific_members_or_teams") . " (" . lang("excluding_his_her_time_cards") . ")"; ?>:</label>
                        <div class="specific_dropdown">
                            <input type="text" value="<?php echo $attendance_specific; ?>" name="attendance_permission_specific" id="attendance_specific_dropdown" class="w100p validate-hidden"  data-rule-required="true" data-msg-required="<?php echo lang('field_required'); ?>" placeholder="<?php echo lang('choose_members_and_or_teams'); ?>"  />
                        </div>
                    </div>

                </li>
                <li>
                    <h5><?php echo lang("can_access_invoices"); ?></h5>
                    <div>
                        <?php
                        echo form_radio(array(
                            "id" => "invoice_no",
                            "name" => "invoice_permission",
                            "value" => "",
                                ), $invoice, ($invoice === "") ? true : false);
                        ?>
                        <label for="invoice_no"><?php echo lang("no"); ?> </label>
                    </div>
                    <div>
                        <?php
                        echo form_radio(array(
                            "id" => "invoice_yes",
                            "name" => "invoice_permission",
                            "value" => "all",
                                ), $invoice, ($invoice === "all") ? true : false);
                        ?>
                        <label for="invoice_yes"><?php echo lang("yes"); ?></label>
                    </div>
                </li>
                <li>
                    <h5><?php echo lang("can_access_expenses"); ?></h5>
                    <div>
                        <?php
                        echo form_radio(array(
                            "id" => "expense_no",
                            "name" => "expense_permission",
                            "value" => "",
                                ), $expense, ($expense === "") ? true : false);
                        ?>
                        <label for="expense_no"><?php echo lang("no"); ?> </label>
                    </div>
                    <div>
                        <?php
                        echo form_radio(array(
                            "id" => "expense_yes",
                            "name" => "expense_permission",
                            "value" => "all",
                                ), $expense, ($expense === "all") ? true : false);
                        ?>
                        <label for="expense_yes"><?php echo lang("yes"); ?></label>
                    </div>
                </li>
                <li>
                    <h5><?php echo lang("can_access_clients_information"); ?> <span class="help" data-toggle="tooltip" title="Hides all information of clients except company name."><i class="fa fa-question-circle"></i></span></h5>
                    <div>
                        <?php
                        echo form_radio(array(
                            "id" => "client_no",
                            "name" => "client_permission",
                            "value" => "",
                                ), $client, ($client === "") ? true : false);
                        ?>
                        <label for="client_no"><?php echo lang("no"); ?> </label>
                    </div>
                    <div>
                        <?php
                        echo form_radio(array(
                            "id" => "client_yes",
                            "name" => "client_permission",
                            "value" => "all",
                                ), $client, ($client === "all") ? true : false);
                        ?>
                        <label for="client_yes"><?php echo lang("yes"); ?></label>
                    </div>
                </li>
                <li>
                    <h5><?php echo lang("can_access_tickets"); ?></h5>
                    <div>
                        <?php
                        echo form_radio(array(
                            "id" => "ticket_no",
                            "name" => "ticket_permission",
                            "value" => "",
                            "class" => "ticket_permission",
                                ), $ticket, ($ticket === "") ? true : false);
                        ?>
                        <label for="ticket_no"><?php echo lang("no"); ?> </label>
                    </div>
                    <div>
                        <?php
                        echo form_radio(array(
                            "id" => "ticket_yes",
                            "name" => "ticket_permission",
                            "value" => "all",
                            "class" => "ticket_permission",
                                ), $ticket, ($ticket === "all") ? true : false);
                        ?>
                        <label for="ticket_yes"><?php echo lang("yes"); ?> </label>
                    </div>
                </li>
                <li>
                    <h5><?php echo lang("can_manage_announcements"); ?></h5>
                    <div>
                        <?php
                        echo form_radio(array(
                            "id" => "announcement_no",
                            "name" => "announcement_permission",
                            "value" => "",
                                ), $announcement, ($announcement === "") ? true : false);
                        ?>
                        <label for="announcement_no"><?php echo lang("no"); ?> </label>
                    </div>
                    <div>
                        <?php
                        echo form_radio(array(
                            "id" => "announcement_yes",
                            "name" => "announcement_permission",
                            "value" => "all",
                                ), $announcement, ($announcement === "all") ? true : false);
                        ?>
                        <label for="announcement_yes"><?php echo lang("yes"); ?></label>
                    </div>
                </li>
            </ul>

        </div>
        <div class="panel-footer">
            <button type="submit" class="btn btn-primary mr10"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $("#permissions-form").appForm({
            isModal: false,
            onSuccess: function(result) {
                appAlert.success(result.message, {duration: 10000});
            }
        });
        function team_and_member_format(option) {
            if (option.type === "team") {
                return "<i class='fa fa-users info'></i> " + option.text;
            } else {
                return "<i class='fa fa-user'></i> " + option.text;
            }

        }
        $("#leave_specific_dropdown, #attendance_specific_dropdown").select2({
            multiple: true,
            formatResult: team_and_member_format,
            formatSelection: team_and_member_format,
            data: <?php echo ($members_and_teams_dropdown); ?>
        });
        $("#ticket_types_specific_dropdown").select2({
            multiple: true,
            data: <?php echo ($ticket_types_dropdown); ?>
        });

        $('[data-toggle="tooltip"]').tooltip();

        $(".toggle_specific").click(function() {
            toggle_specific_dropdown();
        });
        toggle_specific_dropdown();
        function toggle_specific_dropdown() {
            var selectors = [".leave_permission", ".attendance_permission"];
            $.each(selectors, function(index, element) {
                var $element = $(element + ":checked");
                if ($element.val() === "specific") {
                    $element.closest("li").find(".specific_dropdown").show().find("input").addClass("validate-hidden");
                } else {
                    $element.closest("li").find(".specific_dropdown").hide().find("input").removeClass("validate-hidden");
                }
            });

        }
    });
</script>    
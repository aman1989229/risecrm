<div id="page-content" class="p20 row">

    <div class="col-sm-3 col-lg-2">
        <?php
        $tab_view['active_tab'] = "general";
        $this->load->view("settings/tabs", $tab_view);
        ?>
    </div>

    <div class="col-sm-9 col-lg-10">
        <?php echo form_open(get_uri("settings/save_general_settings"), array("id" => "general-settings-form", "class" => "general-form dashed-row", "role" => "form")); ?>
        <div class="panel">
            <div class="panel-default panel-heading">
                <h4><?php echo lang("general_settings"); ?></h4>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="logo" class=" col-md-2"><?php echo lang('site_logo'); ?></label>
                    <div class=" col-md-10">
                        <div class="pull-left mr15">
                            <img id="site-logo-preview" src="<?php echo get_file_uri(get_setting("system_file_path") . get_setting("site_logo")); ?>" alt="..." />
                        </div>
                        <div class="pull-left file-upload btn btn-default btn-xs">
                            <span><?php echo lang("change"); ?> ...</span>
                            <input id="site_logo_file" class="upload" name="site_logo_file" type="file" data-height="40" data-width="175" data-preview-container="#site-logo-preview" data-input-field="#site_logo" />
                        </div>
                        <input type="hidden" id="site_logo" name="site_logo" value=""  />
                    </div>
                </div>
                <div class="form-group">
                    <label for="logo" class=" col-md-2"><?php echo lang('invoice_logo'); ?></label>
                    <div class=" col-md-10">
                        <div class="pull-left mr15">
                            <img id="invoice-logo-preview" src="<?php echo get_file_uri(get_setting("system_file_path") . get_setting("invoice_logo")); ?>" alt="..." />
                        </div>
                        <div class="pull-left file-upload btn btn-default btn-xs">
                            <span><?php echo lang("change"); ?> ...</span>
                            <input id="invoice_logo_file" class="upload" name="invoice_logo_file" type="file" data-height="100" data-width="300" data-preview-container="#invoice-logo-preview" data-input-field="#invoice_logo" />
                        </div>
                        <input type="hidden" id="invoice_logo" name="invoice_logo" value=""  />
                    </div>
                </div>
                <div class="form-group">
                    <label for="app_title" class=" col-md-2"><?php echo lang('app_title'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "app_title",
                            "name" => "app_title",
                            "value" => get_setting('app_title'),
                            "class" => "form-control",
                            "placeholder" => lang('app_title'),
                            "data-rule-required" => true,
                            "data-msg-required" => lang("field_required"),
                        ));
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="language" class=" col-md-2"><?php echo lang('language'); ?></label>
                    <div class="col-md-10">
                        <?php
                        echo form_dropdown(
                                "language", $language_dropdown, get_setting('language'), "class='select2 mini'"
                        );
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="timezone" class=" col-md-2"><?php echo lang('timezone'); ?></label>
                    <div class="col-md-10">
                        <?php
                        echo form_dropdown(
                                "timezone", $timezone_dropdown, get_setting('timezone'), "class='select2 mini'"
                        );
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="date_format" class=" col-md-2"><?php echo lang('date_format'); ?></label>
                    <div class="col-md-10">
                        <?php
                        echo form_dropdown(
                                "date_format", array(
                            "d-m-Y" => "d-m-Y",
                            "m-d-Y" => "m-d-Y",
                            "Y-m-d" => "Y-m-d",
                            "d/m/Y" => "d/m/Y",
                            "m/d/Y" => "m/d/Y",
                            "Y/m/d" => "Y/m/d",
                            "d.m.Y" => "d.m.Y",
                            "m.d.Y" => "m.d.Y",
                            "Y.m.d" => "Y.m.d",
                                ), get_setting('date_format'), "class='select2 mini'"
                        );
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="time_format" class=" col-md-2"><?php echo lang('time_format'); ?></label>
                    <div class="col-md-10">
                        <?php
                        echo form_dropdown(
                                "time_format", array(
                            "capital" => "12 AM",
                            "small" => "12 am"
                                ), get_setting('time_format'), "class='select2 mini'"
                        );
                        ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="first_day_of_week" class=" col-md-2"><?php echo lang('first_day_of_week'); ?></label>
                    <div class="col-md-10">
                        <?php
                        echo form_dropdown(
                                "first_day_of_week", array(
                            "0" => "Sunday",
                            "1" => "Monday",
                            "2" => "Tuesday",
                            "3" => "Wednesday",
                            "4" => "Thursday",
                            "5" => "Friday",
                            "6" => "Saturday"
                                ), get_setting('first_day_of_week'), "class='select2 mini'"
                        );
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="currency_symbol" class=" col-md-2"><?php echo lang('currency_symbol'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "currency_symbol",
                            "name" => "currency_symbol",
                            "value" => get_setting('currency_symbol'),
                            "class" => "form-control",
                            "placeholder" => lang('currency_symbol'),
                            "data-rule-required" => true,
                            "data-msg-required" => lang("field_required"),
                        ));
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="decimal_separator" class=" col-md-2"><?php echo lang('decimal_separator'); ?></label>
                    <div class="col-md-10">
                        <?php
                        echo form_dropdown(
                                "decimal_separator", array("." => "Dot (.)", "," => "Comma (,)"), get_setting('decimal_separator'), "class='select2 mini'"
                        );
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="accepted_file_formats" class=" col-md-2"><?php echo lang('accepted_file_format'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "accepted_file_formats",
                            "name" => "accepted_file_formats",
                            "value" => get_setting('accepted_file_formats'),
                            "class" => "form-control",
                            "placeholder" => lang('comma_separated'),
                            "data-rule-required" => true,
                            "data-msg-required" => lang("field_required"),
                        ));
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="item_purchase_code" class=" col-md-2"><?php echo lang('item_purchase_code'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "item_purchase_code",
                            "name" => "item_purchase_code",
                            "value" => get_setting('item_purchase_code'),
                            "class" => "form-control",
                            "placeholder" => "Envato Purchase Code",
                            "data-rule-required" => true,
                            "data-msg-required" => lang("field_required"),
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

<?php $this->load->view("includes/cropbox"); ?>

<script type="text/javascript">
    $(document).ready(function() {
        $("#general-settings-form .select2").select2();

        $("#general-settings-form").appForm({
            isModal: false,
            onSuccess: function(result) {
                appAlert.success(result.message, {duration: 10000});
                if ($("#site_logo").val() || $("#invoice_logo").val()) {
                    location.reload();
                }
            }
        });

        $(".upload").change(function() {
            showCropBox(this);
        });

    });
</script>
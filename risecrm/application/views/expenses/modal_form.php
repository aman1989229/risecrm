<?php echo form_open(get_uri("expenses/save"), array("id" => "expense-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
    <div class=" form-group">
        <label for="expense_date" class=" col-md-3"><?php echo lang('date_of_expense'); ?></label>
        <div class="col-md-9">
            <?php
            echo form_input(array(
                "id" => "expense_date",
                "name" => "expense_date",
                "value" => $model_info->expense_date,
                "class" => "form-control",
                "placeholder" => "YYYY-MM-DD",
                "data-rule-required" => true,
                "data-msg-required" => lang("field_required"),
            ));
            ?>
        </div>
    </div>
    <div class="form-group">
        <label for="category_id" class=" col-md-3"><?php echo lang('category'); ?></label>
        <div class=" col-md-9">
            <?php
            echo form_dropdown("category_id", $categories_dropdown, $model_info->category_id, "class='select2 validate-hidden' id='category_id' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
            ?>
        </div>
    </div>

    <div class="form-group">
        <label for="description" class=" col-md-3"><?php echo lang('description'); ?></label>
        <div class=" col-md-9">
            <?php
            echo form_textarea(array(
                "id" => "description",
                "name" => "description",
                "value" => $model_info->description ? $model_info->description : "",
                "class" => "form-control",
                "placeholder" => lang('description')
            ));
            ?>

        </div>
    </div>
    <div class="form-group">
        <label for="title" class=" col-md-3"><?php echo lang('amount'); ?></label>
        <div class=" col-md-9">
            <?php
            echo form_input(array(
                "id" => "amount",
                "name" => "amount",
                "value" => $model_info->amount ? to_decimal_format($model_info->amount) : "",
                "class" => "form-control",
                "placeholder" => lang('amount'),
                "autofocus" => true,
                "data-rule-required" => true,
                "data-msg-required" => lang("field_required"),
            ));
            ?>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function() {
        $("#expense-form").appForm({
            onSuccess: function(result) {
                location.reload();
            }
        });

        $("#expense_date").datepicker({
            autoclose: true,
            format: "yyyy-mm-dd"
        });
        if (!$("#expense_date").val()) {
            $("#expense_date").val(moment().format("YYYY-MM-DD"));
        }

        $("#expense-form .select2").select2();
        $("#description").focus();
    });
</script>
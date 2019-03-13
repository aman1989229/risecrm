<?php echo form_open(get_uri("tickets/save"), array("id" => "ticket-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
    <div id="new-ticket-dropzone" class="post-dropzone">

        <div class="form-group">
            <label for="title" class=" col-md-3"><?php echo lang('title'); ?></label>
            <div class=" col-md-9">
                <?php
                echo form_input(array(
                    "id" => "title",
                    "name" => "title",
                    "value" => $model_info->title,
                    "class" => "form-control",
                    "placeholder" => lang('title'),
                    "autofocus" => true,
                    "data-rule-required" => true,
                    "data-msg-required" => lang("field_required"),
                ));
                ?>
            </div>
        </div>
        <?php if ($client_id) { ?>
            <input type="hidden" name="client_id" value="<?php echo $client_id; ?>" />
        <?php } else { ?>
            <div class="form-group">
                <label for="client_id" class=" col-md-3"><?php echo lang('client'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_dropdown("client_id", $clients_dropdown, array(""), "class='select2'");
                    ?>
                </div>
            </div>
        <?php } ?>
        <div class="form-group">
            <label for="ticket_type_id" class=" col-md-3"><?php echo lang('ticket_type'); ?></label>
            <div class="col-md-9">
                <?php
                echo form_dropdown("ticket_type_id", $ticket_types_dropdown, array(""), "class='select2'");
                ?>
            </div>
        </div>
        <div class="form-group">
           <label for="applicant_id" class=" col-md-3"><?php echo lang('assigned_to'); ?></label> 
           <div class="col-md-9">
                <?php
               echo form_dropdown("applicant_id", $team_members_dropdown, array("") ,"class='select2'");
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
                    "value" => $model_info->description,
                    "class" => "form-control",
                    "placeholder" => lang('description'),
                    "data-rule-required" => true,
                    "data-msg-required" => lang("field_required"),
                ));
                ?>
            </div>
        </div>

        <?php $this->load->view("includes/dropzone_preview"); ?>   
        
        <div class="row">
            <div class="modal-footer">
                <button class="btn btn-default upload-file-button pull-left btn-sm round" type="button" style="color:#7988a2"><i class='fa fa-camera'></i> <?php echo lang("upload_file"); ?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
                <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
            </div>
        </div>

     </div>    
    </div>


<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function() {

        var uploadUrl = "<?php echo get_uri("tickets/upload_file"); ?>";
        var validationUrl = "<?php echo get_uri("tickets/validate_ticket_file"); ?>";

        var dropzone = attachDropzoneWithForm("#new-ticket-dropzone", uploadUrl, validationUrl);

        $("#ticket-form").appForm({
            onSuccess: function(result) {
                $("#ticket-table").appTable({newData: result.data, dataId: result.id});
            }
        });
        $("#title").focus();
        $("#ticket-form .select2").select2();
    });

</script>
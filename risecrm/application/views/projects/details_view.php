
<div id="page-content" class="p20 clearfix">
    <div class="row">
        <div class="col-md-12">
            <div class="page-title mb15 clearfix">
                <h1><i class="fa fa-th-large"></i> <?php echo $project_info->title; ?></h1>
                <div class="title-button-group" id="project-timer-box">
                    <?php
                    if ($show_timmer) {
                        $this->load->view("projects/project_timer");
                    }
                    ?>
                </div>
            </div>
            <ul id="project-tabs" data-toggle="ajax-tab" class="nav nav-tabs classic" role="tablist">
                <?php if ($this->login_user->user_type === "staff") { ?>
                    <li><a role="presentation" href="<?php echo_uri("projects/overview/" . $project_info->id); ?>" data-target="#project-overview-section"><?php echo lang('overview'); ?></a></li>
                    <li><a role="presentation" href="<?php echo_uri("projects/tasks/" . $project_info->id); ?>" data-target="#project-tasks-section"><?php echo lang('tasks'); ?></a></li>
                    <li><a role="presentation" href="<?php echo_uri("projects/milestones/" . $project_info->id); ?>" data-target="#project-milestones-section"> <?php echo lang('milestones'); ?></a></li>
                    <li><a role="presentation" href="<?php echo_uri("projects/notes/" . $project_info->id); ?>" data-target="#project-notes-section"> <?php echo lang('notes'); ?></a></li>
                    <li><a role="presentation" href="<?php echo_uri("projects/files/" . $project_info->id); ?>" data-target="#project-files-section"> <?php echo lang('files'); ?></a></li>
                    <li><a role="presentation" href="<?php echo_uri("projects/comments/" . $project_info->id); ?>" data-target="#project-comments-section"> <?php echo lang('comments'); ?></a></li>
                    <li><a role="presentation" href="<?php echo_uri("projects/customer_feedback/" . $project_info->id); ?>" data-target="#project-customer-feedback-section"> <?php echo lang('customer_feedback'); ?></a></li>
                    <li><a role="presentation" href="<?php echo_uri("projects/timesheets/" . $project_info->id); ?>" data-target="#project-timesheets-section"> <?php echo lang('timesheets'); ?></a></li>
                    <?php if ($show_invoice_info) { ?>
                        <li><a  role="presentation" href="<?php echo_uri("projects/invoices/" . $project_info->id); ?>" data-target="#project-invoices"> <?php echo lang('invoices'); ?></a></li>
                        <li><a  role="presentation" href="<?php echo_uri("projects/payments/" . $project_info->id); ?>" data-target="#project-payments"> <?php echo lang('payments'); ?></a></li>
                    <?php } ?>
                <?php } else { ?>
                    <li><a role="presentation" href="<?php echo_uri("projects/overview_for_client/" . $project_info->id); ?>" data-target="#project-overview-section"><?php echo lang('description'); ?></a></li>                   
                    <li><a role="presentation" href="<?php echo_uri("projects/customer_feedback/" . $project_info->id); ?>" data-target="#project-customer-feedback-section"> <?php echo lang('comments'); ?></a></li>
                    <li><a role="presentation" href="<?php echo_uri("projects/milestones/" . $project_info->id); ?>" data-target="#project-milestones-section"> <?php echo lang('milestones'); ?></a></li>
                <?php } ?>


            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade active" id="project-overview-section"></div>
                <div role="tabpanel" class="tab-pane fade" id="project-tasks-section"></div>
                <div role="tabpanel" class="tab-pane fade" id="project-milestones-section"></div>
                <div role="tabpanel" class="tab-pane fade" id="project-files-section"></div>
                <div role="tabpanel" class="tab-pane fade" id="project-comments-section"></div>
                <div role="tabpanel" class="tab-pane fade" id="project-customer-feedback-section"></div>
                <div role="tabpanel" class="tab-pane fade" id="project-notes-section"></div>
                <div role="tabpanel" class="tab-pane fade" id="project-timesheets-section"></div>
                <div role="tabpanel" class="tab-pane fade" id="project-invoices"></div>
                <div role="tabpanel" class="tab-pane fade" id="project-payments"></div>
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
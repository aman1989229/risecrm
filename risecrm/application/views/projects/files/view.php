<div class="app-modal">
    <div class="app-modal-content">
        <?php if ($is_image_file) { ?>
            <img src="<?php echo $file_url; ?>" />
            <?php
        } else {
            echo lang("file_preview_is_not_available") . "<br />";
            echo anchor($file_url, lang("download"));
        }
        ?>
    </div>
    <div class="app-modal-sidebar">
        <div class="mb15 pl15 pr15">
            <div class="media-left ">
                <span class='avatar avatar-sm'><img src='<?php echo get_avatar($file_info->uploaded_by_user_image); ?>' alt='...'></span>
            </div>
            <div class="media-left">
                <div class="mt5"><?php echo get_team_member_profile_link($file_info->uploaded_by, $file_info->uploaded_by_user_name); ?></div>
                <small><span class="text-off"><?php echo format_to_relative_time($file_info->created_at); ?></span></small>
            </div>
            <div class="pt10 pb10 b-b">
                <?php echo $file_info->description; ?>
            </div>
        </div>
        <div class="mr15">
            <?php $this->load->view("projects/comments/comment_form"); ?>
            <?php $this->load->view("projects/comments/comment_list"); ?>
        </div>

    </div>
</div>
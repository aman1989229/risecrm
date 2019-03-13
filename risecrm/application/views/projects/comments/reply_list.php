<?php foreach ($reply_list as $reply) { ?>
    <div class="media mb15 b-l">
        <div class="media-left pl15">
            <span class="avatar avatar-xs">
                <img src="<?php echo get_avatar($reply->created_by_avatar); ?>" alt="..." />
            </span>
        </div>
        <div class="media-body">
            <div class="media-heading">
                <?php
                if ($reply->user_type === "staff") {
                    echo get_team_member_profile_link($reply->created_by, $reply->created_by_user, array("class" => "dark strong"));
                } else {
                    echo get_client_contact_profile_link($reply->created_by, $reply->created_by_user, array("class" => "dark strong"));
                }
                ?>
                <small><span class="text-off"><?php echo format_to_relative_time($reply->created_at); ?></span></small>
            </div>
            <p><?php echo nl2br(link_it($reply->description)); ?></p>
        </div>
    </div>
<?php } ?>
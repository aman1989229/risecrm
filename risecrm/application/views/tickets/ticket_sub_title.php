<div class="bg-white p15 pt0 b-b">
    <span class="text-off"><?php echo lang("status") . ": "; ?></span>
    <?php
    $ticket_status_class = "label-danger";
    if ($ticket_info->status === "new") {
        $ticket_status_class = "label-warning";
    } else if ($ticket_info->status === "closed") {
        $ticket_status_class = "label-success";
    }
    $ticket_status = "<span class='label $ticket_status_class large'>" . lang($ticket_info->status) . "</span> ";
    echo $ticket_status;
    ?>
    <?php if ($this->login_user->user_type === "staff") { ?>
        <span class="text-off ml15"><?php echo lang("client") . ": "; ?></span>
        <?php echo $ticket_info->company_name ? anchor(get_uri("clients/view/" . $ticket_info->client_id), $ticket_info->company_name) : "-"; ?>
    <?php } ?>
        
    <span class="text-off ml15"><?php echo lang("created") . ": "; ?></span>
    <?php echo format_to_relative_time($ticket_info->created_at); ?> 

    <span class="text-off ml15"><?php echo lang("ticket_type") . ": "; ?></span>
    <?php echo $ticket_info->ticket_type; ?> 
   
</div>
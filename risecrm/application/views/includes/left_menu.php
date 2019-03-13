<div id="sidebar" class="box-content ani-width">
    <div id="sidebar-scroll">
        <ul class="" id="sidebar-menu">
            <?php
            if ($this->login_user->user_type == "staff") {


                $sidebar_menu = array(
                    array("name" => "dashboard", "url" => "dashboard", "class" => "fa-desktop"),
                    array("name" => "timeline", "url" => "timeline", "class" => " fa-bell font-18"),
                    array("name" => "events", "url" => "events", "class" => "fa-calendar"),
                    array("name" => "notes", "url" => "notes", "class" => "fa-sticky-note font-16"),
                    array("name" => "messages", "url" => "messages", "class" => "fa-envelope", "devider" => true, "devider" => true, "badge" => count_unread_message(), "badge_class" => "badge-secondary")
                    
                );

                $permissions = $this->login_user->permissions;
                $access_expense = get_array_value($permissions, "expense");
                $access_invoice = get_array_value($permissions, "invoice");
                $access_ticket = get_array_value($permissions, "ticket");
                $access_client = get_array_value($permissions, "client");
                $access_timecard = get_array_value($permissions, "attendance");
                $access_leave = get_array_value($permissions, "leave");


                if ($this->login_user->is_admin || $access_client) {
                    $sidebar_menu[] = array("name" => "clients", "url" => "clients", "class" => "fa-briefcase");
                }

                $sidebar_menu[] = array("name" => "projects", "url" => "projects", "class" => "fa-th-large",
                    "submenu" => array(
                        array("name" => "all_projects", "url" => "projects/all_projects"),
                        array("name" => "my_tasks", "url" => "projects/my_tasks")
                ));

                if ($this->login_user->is_admin || $access_invoice) {
                    $sidebar_menu[] = array("name" => "invoices", "url" => "invoices", "class" => "fa-file-text");
                }

                if ($this->login_user->is_admin || $access_expense || $access_invoice) {
                    $finance_submenu = array();
                    $finance_url = "";

                    if ($this->login_user->is_admin || $access_invoice) {
                        $finance_submenu[] = array("name" => "invoice_payments", "url" => "invoice_payments");
                        $finance_url = "invoice_payments";
                    }
                    if ($this->login_user->is_admin || $access_expense) {
                        $finance_submenu[] = array("name" => "expenses", "url" => "expenses");
                        $finance_url = "expenses";
                    }
                    $sidebar_menu[] = array("name" => "finance", "url" => $finance_url, "class" => "fa-money", "submenu" => $finance_submenu);
                }

              if ($this->login_user->is_admin || $access_ticket) {

                    $ticket_badge = 0;
                    if ($this->login_user->is_admin || $access_ticket === "all") {
                        $ticket_badge = count_new_tickets();
                    }

                    $sidebar_menu[] = array("name" => "tickets", "url" => "tickets", "class" => "fa-life-ring", "devider" => true, "badge" =>count_new_tickets(), "badge_class" => "badge-secondary");
                }
                
                else{
                    $sidebar_menu[] = array("name" => "tickets", "url" => "tickets/ticket_info", "class" => "fa-life-ring", "devider" => true);
                } 
       


                $sidebar_menu[] = array("name" => "team_members", "url" => "team_members", "class" => "fa-users font-16");

                if ($this->login_user->is_admin || $access_timecard) {
                    $sidebar_menu[] = array("name" => "attendance", "url" => "attendance", "class" => "fa-clock-o font-16");
                } else {
                    $sidebar_menu[] = array("name" => "attendance", "url" => "attendance/attendance_info", "class" => "fa-clock-o font-16");
                }

                if ($this->login_user->is_admin || $access_leave) {
                    $sidebar_menu[] = array("name" => "leaves", "url" => "leaves", "class" => "fa-sign-out font-16", "devider" => true);
                } else {
                    $sidebar_menu[] = array("name" => "leaves", "url" => "leaves/leave_info", "class" => "fa-sign-out font-16", "devider" => true);
                }

                $sidebar_menu[] = array("name" => "announcements", "url" => "announcements", "class" => "fa-bullhorn");

                if ($this->login_user->is_admin) {
                    $sidebar_menu[] = array("name" => "settings", "url" => "settings/general", "class" => "fa-cog");
                }
            } else {
                //client menu

                $sidebar_menu = array(
                    array("name" => "dashboard", "url" => "dashboard", "class" => "fa-desktop"),
                );

                //check message access settings for clients
                if (get_setting("client_message_users")) {
                    $sidebar_menu[] = array("name" => "messages", "url" => "messages", "class" => "fa-envelope", "badge" => count_unread_message());
                }

                $sidebar_menu[] = array("name" => "projects", "url" => "projects/all_projects", "class" => "fa fa-th-large");
                $sidebar_menu[] = array("name" => "invoices", "url" => "invoices", "class" => "fa-file-text");
                $sidebar_menu[] = array("name" => "invoice_payments", "url" => "invoice_payments", "class" => "fa-money");
                $sidebar_menu[] = array("name" => "tickets", "url" => "tickets", "class" => "fa-life-ring");
                $sidebar_menu[] = array("name" => "announcements", "url" => "announcements", "class" => "fa-bullhorn");
                $sidebar_menu[] = array("name" => "users", "url" => "clients/users", "class" => "fa-user");
                $sidebar_menu[] = array("name" => "my_profile", "url" => "clients/contact_profile/" . $this->login_user->id, "class" => "fa-cog");
            }

            foreach ($sidebar_menu as $main_menu) {
                $submenu = get_array_value($main_menu, "submenu");
                $expend_class = $submenu ? " expand " : "";
                $active_class = active_menu($main_menu['name'], $submenu);
                $submenu_open_class = "";
                if ($expend_class && $active_class) {
                    $submenu_open_class = " open ";
                }

                $submenu_is_a_controller = false;
                if ($main_menu['name'] === "settings" || $main_menu['name'] === "finance") {
                    $submenu_is_a_controller = true;
                }

                $devider_class = get_array_value($main_menu, "devider") ? "devider" : "";
                $badge = get_array_value($main_menu, "badge");
                $badge_class = get_array_value($main_menu, "badge_class");
                ?>
                <li class="<?php echo $active_class . " " . $expend_class . " " . $submenu_open_class . " $devider_class"; ?> main">
                    <a href="<?php echo_uri($main_menu['url']); ?>">
                        <i class="fa <?php echo ($main_menu['class']); ?>"></i>
                        <span><?php echo lang($main_menu['name']); ?></span>
                        <?php
                        if ($badge) {
                            echo "<span class='badge $badge_class'>$badge</span>";
                        }
                        ?>
                    </a>
                    <?php
                    if ($submenu) {
                        echo "<ul>";
                        foreach ($submenu as $s_menu) {
                            ?>
                        <li class="<?php echo active_submenu($s_menu['name'], $submenu_is_a_controller); ?>">
                            <a href="<?php echo_uri($s_menu['url']); ?>">
                                <i class="dot fa fa-circle"></i>
                                <span><?php echo lang($s_menu['name']); ?></span>
                            </a>
                        </li>
                        <?php
                    }
                    echo "</ul>";
                }
                ?>
                </li>
            <?php } ?>
        </ul>
    </div>
</div><!-- sidebar menu end -->
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view('includes/head'); ?>
    </head>
    <body>
        <div class="signin-box">
            <?php
            if (isset($form_type) && $form_type == "request_reset_password") {
                $this->load->view("signin/reset_password_form");
            } else if (isset($form_type) && $form_type == "new_password") {
                $this->load->view('signin/new_password_form');
            } else {
                $this->load->view("signin/signin_form");
            }
            ?>
        </div>
    </body>
</html>
<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once("Pre_loader.php");

class Settings extends Pre_loader {

    function __construct() {
        parent::__construct();
        $this->access_only_admin();
    }

    function index() {
        redirect('settings/general');
    }

    function general() {
        $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        $view_data['timezone_dropdown'] = array();
        foreach ($tzlist as $zone) {
            $view_data['timezone_dropdown'][$zone] = $zone;
        }

        $view_data['language_dropdown'] = array();
        $dir = "./application/language/";
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file && $file != "." && $file != ".." && $file != "index.html") {
                        $view_data['language_dropdown'][$file] = ucfirst($file);
                    }
                }
                closedir($dh);
            }
        }

        $this->template->rander("settings/general", $view_data);
    }

    function save_general_settings() {
        $settings = array("site_logo", "invoice_logo", "app_title", "language", "timezone", "date_format", "time_format", "first_day_of_week", "currency_symbol", "decimal_separator", "accepted_file_formats", "item_purchase_code");

        foreach ($settings as $setting) {
            $value = $this->input->post($setting);
            if ($value) {
                if ($setting === "site_logo") {
                    $value = move_temp_file("site-logo.png", get_setting("system_file_path"), $value);
                } else if ($setting === "invoice_logo") {
                    $value = move_temp_file("invoice-logo.png", get_setting("system_file_path"), $value);
                }

                $this->Settings_model->save_setting($setting, $value);
            }
        }

        if ($_FILES) {
            $site_logo_file = get_array_value($_FILES, "site_logo_file");
            $site_logo_file_name = get_array_value($site_logo_file, "tmp_name");
            if ($site_logo_file_name) {
                $site_logo = move_temp_file("site-logo.png", get_setting("system_file_path"), $site_logo_file_name);
                $this->Settings_model->save_setting("site_logo", $site_logo);
            }

            $invoice_logo_file = get_array_value($_FILES, "invoice_logo_file");
            $invoice_logo_file_name = get_array_value($invoice_logo_file, "tmp_name");
            if ($invoice_logo_file_name) {
                $site_logo = move_temp_file("invoice-logo.png", get_setting("system_file_path"), $invoice_logo_file_name);
                $this->Settings_model->save_setting("invoice_logo", $site_logo);
            }
        }

        echo json_encode(array("success" => true, 'message' => lang('settings_updated')));
    }

    function company() {
        $this->template->rander("settings/company");
    }

    function save_company_settings() {
        $settings = array("company_name", "company_address", "company_phone", "company_email", "company_website");

        foreach ($settings as $setting) {
            $this->Settings_model->save_setting($setting, $this->input->post($setting));
        }
        echo json_encode(array("success" => true, 'message' => lang('settings_updated')));
    }

    function email() {
        $this->template->rander("settings/email");
    }

    function save_email_settings() {
        $settings = array("email_sent_from_address", "email_sent_from_name", "email_protocol", "email_smtp_host", "email_smtp_port", "email_smtp_user", "email_smtp_pass");

        foreach ($settings as $setting) {
            $value = $this->input->post($setting);
            $this->Settings_model->save_setting($setting, $value);
        }

        $test_email_to = $this->input->post("send_test_mail_to");
        if ($test_email_to) {
            $email_config = Array(
                'charset' => 'utf-8',
                'mailtype' => 'html'
            );
            if ($this->input->post("email_protocol") === "smtp") {
                $email_config["protocol"] = "smtp";
                $email_config["smtp_host"] = $this->input->post("email_smtp_host");
                $email_config["smtp_port"] = $this->input->post("email_smtp_port");
                $email_config["smtp_user"] = $this->input->post("email_smtp_user");
                $email_config["smtp_pass"] = $this->input->post("email_smtp_pass");
            }

            $this->load->library('email', $email_config);
            $this->email->set_newline("\r\n");
            $this->email->from($this->input->post("email_sent_from_address"), $this->input->post("email_sent_from_name"));

            $this->email->to($test_email_to);
            $this->email->subject("Test message");
            $this->email->message("This is a test message to check mail configuration.");

            if ($this->email->send()) {
                echo json_encode(array("success" => true, 'message' => lang('test_mail_sent')));
                return false;
            } else {
                echo json_encode(array("success" => false, 'message' => lang('test_mail_send_failed')));
                // show_error($ci->email->print_debugger());
                return false;
            }
        }
        echo json_encode(array("success" => true, 'message' => lang('settings_updated')));
    }

    function ip_restriction() {
        $this->template->rander("settings/ip_restriction");
    }

    function save_ip_settings() {
        $this->Settings_model->save_setting("allowed_ip_addresses", $this->input->post("allowed_ip_addresses"));

        echo json_encode(array("success" => true, 'message' => lang('settings_updated')));
    }

    function db_backup() {
        $this->template->rander("settings/db_backup");
    }

    function client() {
        $team_members = $this->Users_model->get_all_where(array("deleted" => 0, "user_type" => "staff"))->result();
        $members_dropdown = array();

        foreach ($team_members as $team_member) {
            $members_dropdown[] = array("id" => $team_member->id, "text" => $team_member->first_name . " " . $team_member->last_name);
        }

        $view_data['members_dropdown'] = json_encode($members_dropdown);
        $this->template->rander("settings/client", $view_data);
    }

    function save_client_settings() {
        $settings = array("disable_client_login_and_signup", "client_message_users");

        foreach ($settings as $setting) {
            $this->Settings_model->save_setting($setting, $this->input->post($setting));
        }
        echo json_encode(array("success" => true, 'message' => lang('settings_updated')));
    }

}

/* End of file general_settings.php */
    /* Location: ./application/controllers/general_settings.php */
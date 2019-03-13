<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once("Pre_loader.php");

class Dashboard extends Pre_loader {

    public function index() {

        if ($this->login_user->user_type === "staff") {
            //check which widgets are viewable to current logged in user

            $view_data["show_invoice_statistics"] = true;
            $view_data["show_income_vs_expenses"] = true;

            $access_expense = $this->get_access_info("expense");
            $access_invoice = $this->get_access_info("invoice");

            $access_ticket = $this->get_access_info("ticket");
            $access_timecards = $this->get_access_info("attendance");

            $view_data["show_invoice_statistics"] = false;
            $view_data["show_ticket_status"] = false;
            $view_data["show_income_vs_expenses"] = false;
            $view_data["show_clock_status"] = false;
            if ($access_expense->access_type === "all" && $access_invoice->access_type === "all") {
                $view_data["show_invoice_statistics"] = true;
                $view_data["show_income_vs_expenses"] = true;
            }

            if ($access_ticket->access_type === "all") {
                $view_data["show_ticket_status"] = true;
            }

            if ($access_timecards->access_type === "all") {
                $view_data["show_clock_status"] = true;
            }

            $this->template->rander("dashboard/index", $view_data);
        } else {
            //client's dashboard    

            $options = array("id" => $this->login_user->client_id);
            $client_info = $this->Clients_model->get_details($options)->row();

            $view_data['show_invoice_info'] = true;
            $view_data['client_info'] = $client_info;
            $view_data['client_id'] = $client_info->id;
            $view_data['page_type'] = "dashboard";
            $this->template->rander("dashboard/client_dashboard", $view_data);
        }
    }

    public function save_sticky_note() {
        $this->Users_model->save(array("sticky_note" => $this->input->post("sticky_note")), $this->login_user->id);
    }

}

/* End of file dashboard.php */
/* Location: ./application/controllers/dashboard.php */
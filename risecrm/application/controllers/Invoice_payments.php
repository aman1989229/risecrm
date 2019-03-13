<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once("Pre_loader.php");

class Invoice_payments extends Pre_loader {

    function __construct() {
        parent::__construct();
        $this->init_permission_checker("invoice");
    }

    /* load invoice list view */

    function index() {
        if ($this->login_user->user_type === "staff") {
            $payment_methods = $this->Payment_methods_model->get_all_where(array("deleted" => 0))->result();

            $payment_method_dropdown = array(array("id" => "", "text" => "- " . lang("payment_methods") . " -"));
            foreach ($payment_methods as $value) {
                $payment_method_dropdown[] = array("id" => $value->id, "text" => $value->title);
            }
            $view_data['payment_method_dropdown'] = json_encode($payment_method_dropdown);
            $this->template->rander("invoices/payment_received", $view_data);
        } else {
            $view_data["client_info"] = $this->Clients_model->get_one($this->login_user->client_id);
            $view_data['client_id'] = $this->login_user->client_id;
            $view_data['page_type'] = "full";
            $this->template->rander("clients/payments/index", $view_data);
        }
    }

    /* load payment modal */

    function payment_modal_form() {
        $this->access_only_allowed_members();

        validate_submitted_data(array(
            "id" => "numeric",
            "invoice_id" => "numeric"
        ));

        $invoice_id = $this->input->post('invoice_id');

        $view_data['model_info'] = $this->Invoice_payments_model->get_one($this->input->post('id'));
        if (!$invoice_id) {
            $invoice_id = $view_data['model_info']->invoice_id;
        }
        $view_data['payment_methods_dropdown'] = $this->Payment_methods_model->get_dropdown_list(array("title"));
        $view_data['invoice_id'] = $invoice_id;
        $this->load->view('invoices/payment_modal_form', $view_data);
    }

    /* add or edit a payment */

    function save_payment() {
        $this->access_only_allowed_members();

        validate_submitted_data(array(
            "id" => "numeric",
            "invoice_id" => "required|numeric",
            "invoice_payment_method_id" => "required|numeric",
            "invoice_payment_date" => "required",
            "invoice_payment_amount" => "required"
        ));

        $id = $this->input->post('id');
        $invoice_payment_data = array(
            "invoice_id" => $this->input->post('invoice_id'),
            "payment_date" => $this->input->post('invoice_payment_date'),
            "payment_method_id" => $this->input->post('invoice_payment_method_id'),
            "note" => $this->input->post('invoice_payment_note'),
            "amount" => unformat_currency($this->input->post('invoice_payment_amount'))
        );

        $invoice_payment_id = $this->Invoice_payments_model->save($invoice_payment_data, $id);
        if ($invoice_payment_id) {
            $options = array("id" => $invoice_payment_id);
            $item_info = $this->Invoice_payments_model->get_details($options)->row();
            echo json_encode(array("success" => true, "invoice_id" => $item_info->invoice_id, "data" => $this->_make_payment_row($item_info), "invoice_total_view" => $this->_get_invoice_total_view($item_info->invoice_id), 'id' => $invoice_payment_id, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    /* delete or undo a payment */

    function delete_payment() {
        $this->access_only_allowed_members();

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');
        if ($this->input->post('undo')) {
            if ($this->Invoice_payments_model->delete($id, true)) {
                $options = array("id" => $id);
                $item_info = $this->Invoice_payments_model->get_details($options)->row();
                echo json_encode(array("success" => true, "invoice_id" => $item_info->invoice_id, "data" => $this->_make_payment_row($item_info), "invoice_total_view" => $this->_get_invoice_total_view($item_info->invoice_id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Invoice_payments_model->delete($id)) {
                $item_info = $this->Invoice_payments_model->get_one($id);
                echo json_encode(array("success" => true, "invoice_id" => $item_info->invoice_id, "invoice_total_view" => $this->_get_invoice_total_view($item_info->invoice_id), 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }

    /* list of invoice payments, prepared for datatable  */

    function payment_list_data($invoice_id = 0) {
        $this->access_only_allowed_members();

        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $payment_method_id = $this->input->post('payment_method_id');
        $options = array("start_date" => $start_date, "end_date" => $end_date, "invoice_id" => $invoice_id, "payment_method_id" => $payment_method_id);

        $list_data = $this->Invoice_payments_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_payment_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    /* list of invoice payments, prepared for datatable  */

    function payment_list_data_of_client($client_id = 0) {

        $this->access_only_allowed_members_or_client_contact($client_id);

        $options = array("client_id" => $client_id);
        $list_data = $this->Invoice_payments_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_payment_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    /* list of invoice payments, prepared for datatable  */

    function payment_list_data_of_project($project_id = 0) {
        $options = array("project_id" => $project_id);

        $list_data = $this->Invoice_payments_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_payment_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    /* prepare a row of invoice payment list table */

    private function _make_payment_row($data) {
        $invoice_url = "";
        $this->access_only_allowed_members_or_client_contact($data->client_id);

        if ($this->login_user->user_type == "staff") {
            $invoice_url = anchor(get_uri("invoices/view/" . $data->invoice_id), $data->invoice_id);
        } else {
            $invoice_url = anchor(get_uri("invoices/view_pdf/" . encode_id($data->invoice_id, "invoice_id")), $data->invoice_id, array("target" => "_blank"));
        }
        return array(
            $invoice_url,
            format_to_date($data->payment_date),
            $data->payment_method_title,
            $data->note,
            to_currency($data->amount, $data->currency),
            modal_anchor(get_uri("invoice_payments/payment_modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_payment'), "data-post-id" => $data->id))
            . js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("invoice_payments/delete_payment"), "data-action" => "delete"))
        );
    }

    /* invoice total section */

    private function _get_invoice_total_view($invoice_id = 0) {
        $view_data["invoice_total_summary"] = $this->Invoices_model->get_invoice_total_summary($invoice_id);
        return $this->load->view('invoices/invoice_total_section', $view_data, true);
    }

}

/* End of file payments.php */
/* Location: ./application/controllers/payments.php */
<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once("Pre_loader.php");

class Payment_methods extends Pre_loader {

    function __construct() {
        parent::__construct();
        $this->access_only_admin();
    }

    //load payment methods list
    function index() {
        $this->template->rander("payment_methods/index");
    }

    //load payment method add/edit form
    function modal_form() {

        validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['model_info'] = $this->Payment_methods_model->get_one($this->input->post('id'));
        $this->load->view('payment_methods/modal_form', $view_data);
    }

    //save a payment method
    function save() {

        validate_submitted_data(array(
            "id" => "numeric"
        ));

        $id = $this->input->post('id');
        $data = array(
            "title" => $this->input->post('title'),
            "description" => $this->input->post('description')
        );
        $save_id = $this->Payment_methods_model->save($data, $id);
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    //delete/undo a payment method
    function delete() {

        validate_submitted_data(array(
            "id" => "numeric"
        ));

        $id = $this->input->post('id');
        if ($this->input->post('undo')) {
            if ($this->Payment_methods_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Payment_methods_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }

    //prepare payment method list data for datatable.
    function list_data() {
        $list_data = $this->Payment_methods_model->get_details()->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    //get a payment method list row
    private function _row_data($id) {
        $options = array("id" => $id);
        $data = $this->Payment_methods_model->get_details($options)->row();
        return $this->_make_row($data);
    }

    //prepare payment method list row
    private function _make_row($data) {
        return array($data->title,
            $data->description,
            modal_anchor(get_uri("payment_methods/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_payment_method'), "data-post-id" => $data->id))
            . js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_payment_method'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("payment_methods/delete"), "data-action" => "delete"))
        );
    }

}

/* End of file payment_methods.php */
/* Location: ./application/controllers/payment_methods.php */
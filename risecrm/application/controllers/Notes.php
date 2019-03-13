<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once("Pre_loader.php");

class Notes extends Pre_loader {

    function __construct() {
        parent::__construct();
        $this->access_only_team_members();
    }

    //only allowed to access own notes
    protected function access_only_own_notes($user_id = 0) {
        if ($this->login_user->id !== $user_id) {
            redirect("forbidden");
        }
    }

    //load note list view
    function index() {
        $this->template->rander("notes/index");
    }

    function modal_form() {
        $view_data['model_info'] = $this->Notes_model->get_one($this->input->post('id'));
        $view_data['project_id'] = $this->input->post('project_id') ? $this->input->post('project_id') : $view_data['model_info']->project_id;
        $labels = explode(",", $this->Notes_model->get_label_suggestions($this->login_user->id));

        //opening existing note. check permission
        if ($view_data['model_info']->created_by) {
            $this->access_only_own_notes($view_data['model_info']->created_by);
        }

        $label_suggestions = array();
        foreach ($labels as $label) {
            if ($label && !in_array($label, $label_suggestions)) {
                $label_suggestions[] = $label;
            }
        }
        if (!count($label_suggestions)) {
            $label_suggestions = array("0" => "Important");
        }
        $view_data['label_suggestions'] = $label_suggestions;
        $this->load->view('notes/modal_form', $view_data);
    }

    function save() {
        validate_submitted_data(array(
            "id" => "numeric",
            "title" => "required",
            "project_id" => "numeric"
        ));

        $id = $this->input->post('id');

        $data = array(
            "title" => $this->input->post('title'),
            "description" => $this->input->post('description'),
            "created_by" => $this->login_user->id,
            "labels" => $this->input->post('labels'),
            "project_id" => $this->input->post('project_id')
        );

        if ($id) {
            //saving existing note. check permission
            $note_info = $this->Notes_model->get_one($id);
            $this->access_only_own_notes($note_info->created_by);
        } else {
            $data['created_at'] = get_current_utc_time();
        }

        $save_id = $this->Notes_model->save($data, $id);
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    function delete() {
        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');

        $note_info = $this->Notes_model->get_one($id);
        $this->access_only_own_notes($note_info->created_by);

        if ($this->input->post('undo')) {
            if ($this->Notes_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Notes_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }

    function list_data($project_id = 0) {
        $options = array("created_by" => $this->login_user->id);
        if ($project_id) {
            $options["project_id"] = $project_id;
        }
        $list_data = $this->Notes_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    private function _row_data($id) {
        $options = array("id" => $id);
        $data = $this->Notes_model->get_details($options)->row();
        return $this->_make_row($data);
    }

    private function _make_row($data) {
        $title = modal_anchor(get_uri("notes/view/" . $data->id), $data->title, array("class" => "edit", "title" => lang('note'), "data-post-id" => $data->id));
        $note_labels = "";
        if ($data->labels) {
            $labels = explode(",", $data->labels);
            foreach ($labels as $label) {
                $note_labels.="<span class='label label-info'>" . $label . "</span> ";
            }
            $title.="<br />" . $note_labels;
        }
        return array(
            $data->created_at,
            format_to_relative_time($data->created_at),
            $title,
            modal_anchor(get_uri("notes/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_note'), "data-post-id" => $data->id))
            . js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_note'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("notes/delete"), "data-action" => "delete"))
        );
    }

    function view() {
        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $model_info = $this->Notes_model->get_one($this->input->post('id'));

        $this->access_only_own_notes($model_info->created_by);

        $view_data['model_info'] = $model_info;
        $this->load->view('notes/view', $view_data);
    }

}

/* End of file notes.php */
/* Location: ./application/controllers/notes.php */
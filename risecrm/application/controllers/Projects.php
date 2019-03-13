<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once("Pre_loader.php");

class Projects extends Pre_loader {

    public function __construct() {
        parent::__construct();
        $this->load->helper('download');
    }

    /* load project view */

    function index() {
        redirect("projects/all_projects");
    }

    function all_projects() {
        if ($this->login_user->user_type === "staff") {
            $this->template->rander("projects/index");
        } else {
            $view_data['client_id'] = $this->login_user->client_id;
            $view_data['page_type'] = "full";
            $this->template->rander("clients/projects/index", $view_data);
        }
    }

    /* load project  add/edit modal */

    function modal_form() {
        $this->access_only_admin();
        $project_id = $this->input->post('id');
        $view_data['model_info'] = $this->Projects_model->get_one($project_id);
        $view_data['clients_dropdown'] = $this->Clients_model->get_dropdown_list(array("company_name"));

        $labels = explode(",", $this->Projects_model->get_label_suggestions());
        $label_suggestions = array();
        foreach ($labels as $label) {
            if ($label && !in_array($label, $label_suggestions)) {
                $label_suggestions[] = $label;
            }
        }
        if (!count($label_suggestions)) {
            $label_suggestions = array("0" => "");
        }
        $view_data['label_suggestions'] = $label_suggestions;


        $this->load->view('projects/modal_form', $view_data);
    }

    /* insert or update a project */

    function save() {
        $this->access_only_admin();
        $id = $this->input->post('id');
        $now = get_current_utc_time();
        $data = array(
            "title" => $this->input->post('title'),
            "description" => $this->input->post('description'),
            "client_id" => $this->input->post('client_id'),
            "start_date" => $this->input->post('start_date'),
            "deadline" => $this->input->post('deadline'),
            "created_date" => $now,
            "labels" => $this->input->post('labels'),
            "status" => $this->input->post('status') ? $this->input->post('status') : "open",
        );
        $save_id = $this->Projects_model->save($data, $id);
        if ($save_id) {
            if (!$id) {
                //add default project member after project creation
                $data = array(
                    "project_id" => $save_id,
                    "user_id" => $this->login_user->id,
                    "is_leader" => 1
                );
                $this->Project_members_model->save($data);
            }
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    /* delete or undo a project */

    function delete() {
        $this->access_only_admin();
        $id = $this->input->post('id');
        if ($this->input->post('undo')) {
            if ($this->Projects_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Projects_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }

    /* list of projcts, prepared for datatable  */

    function list_data() {
        $this->access_only_team_members();
        $options = array(
            "status" => $this->input->post("status"),
            "user_id" => $this->login_user->id,
            "is_admin" => $this->login_user->is_admin,
        );

        $list_data = $this->Projects_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    function projects_list_data_of_client($client_id) {

        $this->access_only_team_members_or_client_contact($client_id);

        $options = array("client_id" => $client_id, "status" => $this->input->post("status"));
        $list_data = $this->Projects_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    /* return a row of project list  table */

    private function _row_data($id) {
        $options = array("id" => $id);
        $data = $this->Projects_model->get_details($options)->row();
        return $this->_make_row($data);
    }

    /* prepare a row of project list table */

    private function _make_row($data) {

        $progress = $data->total_points ? round(($data->completed_points / $data->total_points) * 100) : 0;

        $class = "progress-bar-primary";
        if ($progress == 100) {
            $class = "progress-bar-success";
        }

        $progress_bar = "<div class='progress' title='$progress%'>
            <div  class='progress-bar $class' role='progressbar' aria-valuenow='$progress' aria-valuemin='0' aria-valuemax='100' style='width: $progress%'>
            </div>
        </div>";
        $dateline = format_to_date($data->deadline);

        if ($progress !== 100 && $data->status === "open" && get_my_local_time("Y-m-d") > $data->deadline) {
            $dateline = "<span class='text-danger mr5'>" . $dateline . "</span> ";
        } else if ($progress !== 100 && $data->status === "open" && get_my_local_time("Y-m-d") == $data->deadline) {
            $dateline = "<span class='text-warning mr5'>" . $dateline . "</span> ";
        }

        $title = anchor(get_uri("projects/view/" . $data->id), $data->title);
        $project_labels = "";
        if ($data->labels) {
            $labels = explode(",", $data->labels);
            foreach ($labels as $label) {
                $project_labels.="<span class='label label-info'  title='Label'>" . $label . "</span> ";
            }
            $title.="<br />" . $project_labels;
        }


        return array(
            anchor(get_uri("projects/view/" . $data->id), $data->id),
            $title,
            anchor(get_uri("clients/view/" . $data->client_id), $data->company_name),
            $dateline,
            $progress_bar,
            lang($data->status),
            modal_anchor(get_uri("projects/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_project'), "data-post-id" => $data->id))
            . js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_project'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("projects/delete"), "data-action" => "delete"))
        );
    }

    /* load project details view */

    function view($project_id = 0) {

        $view_data = $this->_get_project_info_data($project_id);
        $access_info = $this->get_access_info("invoice");
        $view_data["show_invoice_info"] = $access_info->access_type == "all" ? true : false;

        $view_data["show_timmer"] = true;
        if ($this->login_user->user_type === "client") {
            $view_data["show_timmer"] = false;
        }

        $this->template->rander("projects/details_view", $view_data);
    }

    /* prepare project info data for reuse */

    private function _get_project_info_data($project_id) {
        $options = array(
            "id" => $project_id,
            "user_id" => $this->login_user->id,
            "client_id" => $this->login_user->client_id,
            "is_admin" => $this->login_user->is_admin,
        );

        $project_info = $this->Projects_model->get_details($options)->row();
        $view_data['project_info'] = $project_info;

        if ($project_info) {
            $view_data['project_info'] = $project_info;
            $timer = $this->Timesheets_model->get_timer_info($project_id, $this->login_user->id)->row();

            if ($timer) {
                $view_data['timer_status'] = "open";
            } else {
                $view_data['timer_status'] = "";
            }

            $view_data['project_progress'] = $project_info->total_points ? round(($project_info->completed_points / $project_info->total_points) * 100) : 0;

            return $view_data;
        } else {
            show_404();
        }
    }

    /* load project overview section */

    function overview($project_id) {
        $this->access_only_team_members();
        $view_data = $this->_get_project_info_data($project_id);
        $task_statuses = $this->Tasks_model->get_task_statistics(array("project_id" => $project_id));

        $view_data["task_to_do"] = 0;
        $view_data["task_in_progress"] = 0;
        $view_data["task_done"] = 0;
        foreach ($task_statuses as $status) {
            $view_data["task_" . $status->status] = $status->total;
        }

        $view_data['project_id'] = $project_id;
        $offset = 0;
        $view_data['offset'] = $offset;
        $view_data['activity_logs_params'] = array("log_for" => "project", "log_for_id" => $project_id, "limit" => 20, "offset" => $offset);
        $this->load->view('projects/overview', $view_data);
    }

    /* load project overview section */

    function overview_for_client($project_id) {
        if ($this->login_user->user_type === "client") {
            $view_data = $this->_get_project_info_data($project_id);

            $view_data['project_id'] = $project_id;
            $this->load->view('projects/overview_for_client', $view_data);
        }
    }

    /* load project members add/edit modal */

    function project_member_modal_form() {
        $this->access_only_admin();
        $view_data['model_info'] = $this->Project_members_model->get_one($this->input->post('id'));
        $view_data['project_id'] = $this->input->post('project_id') ? $this->input->post('project_id') : $view_data['model_info']->project_id;
        $view_data['users_dropdown'] = $this->Users_model->get_dropdown_list(array("first_name", "last_name"), "id", array("user_type" => "staff"));
        $this->load->view('projects/project_members/modal_form', $view_data);
    }

    /* add a project members  */

    function save_project_member() {
        $this->access_only_admin();
        $data = array(
            "project_id" => $this->input->post('project_id'),
            "user_id" => $this->input->post('user_id')
        );
        $save_id = $this->Project_members_model->save($data);
        if ($save_id && $save_id == "exists") {
            //this member already exists.
            echo json_encode(array("success" => true, 'id' => $save_id));
        } else if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_project_member_row_data($save_id), 'id' => $save_id, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    /* delete/undo a project members  */

    function delete_project_member() {
        $this->access_only_admin();
        $id = $this->input->post('id');
        if ($this->input->post('undo')) {
            if ($this->Project_members_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_project_member_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Project_members_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }

    /* list of project members, prepared for datatable  */

    function project_member_list_data($project_id = 0) {
        $this->access_only_team_members();
        $options = array("project_id" => $project_id);
        $list_data = $this->Project_members_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_project_member_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    /* return a row of project member list */

    private function _project_member_row_data($id) {
        $options = array("id" => $id);
        $data = $this->Project_members_model->get_details($options)->row();
        return $this->_make_project_member_row($data);
    }

    /* prepare a row of project member list */

    private function _make_project_member_row($data) {
        $image_url = get_avatar($data->member_image);
        $member = get_team_member_profile_link($data->user_id, "<span class='avatar avatar-xs mr10'><img src='$image_url' alt='...'></span> $data->member_name");
        $link = "";
        if ($this->login_user->id != $data->user_id) {
            $link = modal_anchor(get_uri("messages/modal_form/" . $data->user_id), "<i class='fa fa-envelope-o'></i>", array("class" => "edit", "title" => lang('send_message')));
        }
        if ($this->login_user->is_admin && !$data->is_leader) {
            $link.= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_member'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("projects/delete_project_member"), "data-action" => "delete"));
        }
        $member = '<div class="pull-left">' . $member . '</div><div class="pull-right"><label class="label label-light ml10">' . $data->job_title . '</label></div>';
        return array($member,
            $link
        );
    }

    /* start/stop project timer */

    function timer($project_id, $timer_status = "start") {
        $this->access_only_team_members();
        $data = array(
            "project_id" => $project_id,
            "user_id" => $this->login_user->id,
            "status" => $timer_status,
        );
        $this->Timesheets_model->process_timer($data);
        $view_data = $this->_get_project_info_data($project_id);
        $this->load->view('projects/project_timer', $view_data);
    }

    /* load timesheets view */

    function timesheets($project_id) {
        $this->access_only_team_members();
        $view_data['project_id'] = $project_id;

        $project_members = $this->Project_members_model->get_project_members_dropdown_list($project_id)->result();
        $project_members_dropdown = array(array("id" => "", "text" => "- " . lang("member") . " -"));
        foreach ($project_members as $member) {
            $project_members_dropdown[] = array("id" => $member->user_id, "text" => $member->member_name);
        }
        $view_data['project_members_dropdown'] = json_encode($project_members_dropdown);
        $this->load->view("projects/timesheets/index", $view_data);
    }

    /* load timelog add/edit modal */

    function timelog_modal_form() {
        $this->access_only_team_members();
        $view_data['model_info'] = $this->Timesheets_model->get_one($this->input->post('id'));
        $view_data['project_id'] = $this->input->post('project_id') ? $this->input->post('project_id') : $view_data['model_info']->project_id;
        $this->load->view('projects/timesheets/modal_form', $view_data);
    }

    /* insert/update a timelog */

    function save_timelog() {
        $this->access_only_team_members();
        $id = $this->input->post('id');

        //convert to 24hrs time format
        $start_time = convert_time_to_24hours_format($this->input->post('start_time'));
        $end_time = convert_time_to_24hours_format($this->input->post('end_time'));

        //join date with time
        $start_date_time = $this->input->post('start_date') . " " . $start_time;
        $end_date_time = $this->input->post('end_date') . " " . $end_time;

        //add time offset
        $start_date_time = convert_date_local_to_utc($start_date_time);
        $end_date_time = convert_date_local_to_utc($end_date_time);

        $data = array(
            "project_id" => $this->input->post('project_id'),
            "start_time" => $start_date_time,
            "end_time" => $end_date_time
        );

        //save user_id only on insert and it will not be editable
        if (!$id) {
            $data["user_id"] = $this->input->post('user_id') ? $this->input->post('user_id') : $this->login_user->id;
        }


        $save_id = $this->Timesheets_model->save($data, $id);
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_timesheet_row_data($save_id), 'id' => $save_id, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    /* delete/undo a timelog */

    function delete_timelog() {
        $this->access_only_admin();
        $id = $this->input->post('id');
        if ($this->input->post('undo')) {
            if ($this->Timesheets_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_timesheet_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Timesheets_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }

    /* list of timesheets, prepared for datatable  */

    function timesheet_list_data($project_id = 0) {
        $this->access_only_team_members();
        $options = array("project_id" => $project_id, "status" => "none_open", "user_id" => $this->input->post("user_id"));
        $list_data = $this->Timesheets_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_timesheet_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    /* return a row of timesheet list  table */

    private function _timesheet_row_data($id) {
        $options = array("id" => $id);
        $data = $this->Timesheets_model->get_details($options)->row();
        return $this->_make_timesheet_row($data);
    }

    /* prepare a row of timesheet list table */

    private function _make_timesheet_row($data) {
        $image_url = get_avatar($data->logged_by_avatar);
        $user = "<span class='avatar avatar-xs mr10'><img src='$image_url' alt=''></span> $data->logged_by_user";

        $start_time = $data->start_time;
        $end_time = $data->end_time;

        return array(
            get_team_member_profile_link($data->user_id, $user),
            format_to_datetime($data->start_time),
            format_to_datetime($data->end_time),
            convert_seconds_to_time_format(abs(strtotime($end_time) - strtotime($start_time))),
            modal_anchor(get_uri("projects/timelog_modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_timelog'), "data-post-id" => $data->id))
            . js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_timelog'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("projects/delete_timelog"), "data-action" => "delete"))
        );
    }

    /* load milestones view */

    function milestones($project_id) {
        $view_data['project_id'] = $project_id;
        $this->load->view("projects/milestones/index", $view_data);
    }

    /* load milestone add/edit modal */

    function milestone_modal_form() {
        $this->access_only_admin();
        $view_data['model_info'] = $this->Milestones_model->get_one($this->input->post('id'));
        $view_data['project_id'] = $this->input->post('project_id') ? $this->input->post('project_id') : $view_data['model_info']->project_id;
        $this->load->view('projects/milestones/modal_form', $view_data);
    }

    /* insert/update a milestone */

    function save_milestone() {
        $this->access_only_admin();
        $id = $this->input->post('id');
        $data = array(
            "title" => $this->input->post('title'),
            "project_id" => $this->input->post('project_id'),
            "due_date" => $this->input->post('due_date')
        );
        $save_id = $this->Milestones_model->save($data, $id);
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_milestone_row_data($save_id), 'id' => $save_id, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    /* delete/undo a milestone */

    function delete_milestone() {
        $this->access_only_admin();
        $id = $this->input->post('id');
        if ($this->input->post('undo')) {
            if ($this->Milestones_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_milestone_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Milestones_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }

    /* list of milestones, prepared for datatable  */

    function milestones_list_data($project_id = 0) {
        $options = array("project_id" => $project_id);
        $list_data = $this->Milestones_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_milestone_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    /* return a row of milestone list  table */

    private function _milestone_row_data($id) {
        $options = array("id" => $id);
        $data = $this->Milestones_model->get_details($options)->row();
        return $this->_make_milestone_row($data);
    }

    /* prepare a row of milestone list table */

    private function _make_milestone_row($data) {

        //calculate milestone progress
        $progress = $data->total_points ? round(($data->completed_points / $data->total_points) * 100) : 0;
        $class = "progress-bar-primary";
        if ($progress == 100) {
            $class = "progress-bar-success";
        }

        $progress_bar = "<div class='progress' title='$progress%'>
            <div  class='progress-bar $class' role='progressbar' aria-valuenow='$progress' aria-valuemin='0' aria-valuemax='100' style='width: $progress%'>
            </div>
        </div>";

        //define milesone color based on due date
        $due_date = date("L", strtotime($data->due_date));
        $label_class = "";
        if ($progress == 100) {
            $label_class = "label-success";
        } else if ($progress !== 100 && get_my_local_time("Y-m-d") > $data->due_date) {
            $label_class = "label-danger";
        } else if ($progress !== 100 && get_my_local_time("Y-m-d") == $data->due_date) {
            $label_class = "label-warning";
        } else {
            $label_class = "label-primary";
        }

        $due_date = "<div class='milestone pull-left' title='" . format_to_date($data->due_date) . "'>
            <span class='label $label_class'>" . date("F", strtotime($data->due_date)) . "</span>
            <h1>" . date("d", strtotime($data->due_date)) . "</h1>
            <span>" . date("l", strtotime($data->due_date)) . "</span>
            </div>
            "
        ;

        return array(
            $due_date,
            $data->title,
            $progress_bar,
            modal_anchor(get_uri("projects/milestone_modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_milestone'), "data-post-id" => $data->id))
            . js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_milestone'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("projects/delete_milestone"), "data-action" => "delete"))
        );
    }

    /* load task list view */

    function tasks($project_id) {
        $this->access_only_team_members();
        $view_data['project_id'] = $project_id;
        $view_data['view_type'] = "project_tasks";
        $milestones = $this->Milestones_model->get_all_where(array("project_id" => $project_id, "deleted" => 0))->result();
        $milestone_dropdown = array(array("id" => "", "text" => "- " . lang("milestone") . " -"));
        foreach ($milestones as $milestone) {
            $milestone_dropdown[] = array("id" => $milestone->id, "text" => $milestone->title);
        }
        $view_data['milestone_dropdown'] = json_encode($milestone_dropdown);

        $this->load->view("projects/tasks/index", $view_data);
    }

    function my_tasks() {
        $this->access_only_team_members();
        $view_data['project_id'] = 0;
        $projects = $this->Tasks_model->get_my_projects_dropdown_list($this->login_user->id)->result();
        $projects_dropdown = array(array("id" => "", "text" => "- " . lang("project") . " -"));
        foreach ($projects as $project) {
            $projects_dropdown[] = array("id" => $project->project_id, "text" => $project->project_title);
        }
        $view_data['projects_dropdown'] = json_encode($projects_dropdown);

        $this->template->rander("projects/tasks/my_tasks", $view_data);
    }

    function task_view() {
        $this->access_only_team_members();
        $task_id = $this->input->post('id');
        $model_info = $this->Tasks_model->get_details(array("id" => $task_id))->row();
        $view_data['model_info'] = $model_info;

        $task_labels = "";
        if ($model_info->labels) {
            $labels = explode(",", $model_info->labels);
            foreach ($labels as $label) {
                $task_labels.="<span class='label label-info'  title='Label'>" . $label . "</span> ";
            }
        }

        $view_data['labels'] = $task_labels;

        $options = array("task_id" => $task_id);
        $view_data['comments'] = $this->Project_comments_model->get_details($options)->result();
        $view_data['task_id'] = $task_id;

        $this->load->view('projects/tasks/view', $view_data);
    }

    /* task add/edit modal */

    function task_modal_form() {
        $this->access_only_team_members();
        $view_data['model_info'] = $this->Tasks_model->get_one($this->input->post('id'));
        $project_id = $this->input->post('project_id') ? $this->input->post('project_id') : $view_data['model_info']->project_id;
        $view_data['milestones_dropdown'] = array(0 => "None") + $this->Milestones_model->get_dropdown_list(array("title"), "id", array("project_id" => $project_id));

        $project_members = $this->Project_members_model->get_project_members_dropdown_list($project_id)->result();
        $project_members_dropdown = array();
        foreach ($project_members as $member) {
            $project_members_dropdown[$member->user_id] = $member->member_name;
        }
        $view_data['assign_to_dropdown'] = $project_members_dropdown;

        $labels = explode(",", $this->Tasks_model->get_label_suggestions($project_id));
        $label_suggestions = array();
        foreach ($labels as $label) {
            if ($label && !in_array($label, $label_suggestions)) {
                $label_suggestions[] = $label;
            }
        }
        if (!count($label_suggestions)) {
            $label_suggestions = array("0" => "");
        }
        $view_data['label_suggestions'] = $label_suggestions;
        $view_data['points_dropdown'] = array(1 => "1 " . lang("point"), 2 => "2 " . lang("points"), 3 => "3 " . lang("points"), 4 => "4 " . lang("points"), 5 => "5 " . lang("points"));

        $view_data['project_id'] = $project_id;
        $this->load->view('projects/tasks/modal_form', $view_data);
    }

    /* insert/upadate a task */

    function save_task() {
        $this->access_only_team_members();
        $id = $this->input->post('id');

        $data = array(
            "title" => $this->input->post('title'),
            "description" => $this->input->post('description'),
            "project_id" => $this->input->post('project_id'),
            "assigned_to" => $this->input->post('assigned_to'),
            "milestone_id" => $this->input->post('milestone_id'),
            "points" => $this->input->post('points'),
            "status" => $this->input->post('status'),
            "labels" => $this->input->post('labels'),
            "deadline" => $this->input->post('deadline') ? $this->input->post('deadline') : "0000-00-00"
        );
        $save_id = $this->Tasks_model->save($data, $id);
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_task_row_data($save_id), 'id' => $save_id, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    /* upadate a task status */

    function save_task_status($id = 0) {
        $this->access_only_team_members();
        $data = array(
            "status" => $this->input->post('value')
        );

        $save_id = $this->Tasks_model->save($data, $id);

        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_task_row_data($save_id), 'id' => $save_id, "message" => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, lang('error_occurred')));
        }
    }

    /* delete or undo a task */

    function delete_task() {
        $this->access_only_admin();
        $id = $this->input->post('id');
        if ($this->input->post('undo')) {
            if ($this->Tasks_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_task_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Tasks_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }

    /* list of tasks, prepared for datatable  */

    function tasks_list_data($project_id = 0) {
        $this->access_only_team_members();
        $status = $this->input->post('status') ? implode(",", $this->input->post('status')) : "";
        $milestone_id = $this->input->post('milestone_id');
        $options = array("project_id" => $project_id, "status" => $status, "milestone_id" => $milestone_id);
        $list_data = $this->Tasks_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_task_row($data, "project_tasks");
        }
        echo json_encode(array("data" => $result));
    }

    /* list of tasks, prepared for datatable  */

    function my_tasks_list_data() {
        $this->access_only_team_members();
        $status = $this->input->post('status') ? implode(",", $this->input->post('status')) : "";
        $project_id = $this->input->post('project_id');
        $options = array("assigned_to" => $this->login_user->id, "status" => $status, "project_id" => $project_id);
        $list_data = $this->Tasks_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_task_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    /* return a row of task list table */

    private function _task_row_data($id) {
        $options = array("id" => $id);
        $data = $this->Tasks_model->get_details($options)->row();
        return $this->_make_task_row($data);
    }

    /* prepare a row of task list table */

    private function _make_task_row($data) {
        $title = modal_anchor(get_uri("projects/task_view"), $data->title, array("title" => lang('task_info') . " #$data->id", "data-post-id" => $data->id));
        $task_labels = "";
        if ($data->labels) {
            $labels = explode(",", $data->labels);
            foreach ($labels as $label) {
                $task_labels.="<span class='label label-info'  title='Label'>" . $label . "</span> ";
            }
        }
        $title.="<span class='pull-right'>" . $task_labels . "</span>";


        $project_title = anchor(get_uri("projects/view/" . $data->project_id), $data->project_title);

        $image_url = get_avatar($data->assigned_to_avatar);
        $assigned_to_user = "<span class='avatar avatar-xs mr10'><img src='$image_url' alt='...'></span> $data->assigned_to_user";
        $assigned_to = get_team_member_profile_link($data->assigned_to, $assigned_to_user);

        $status_class = "";
        $checkbox_class = "checkbox-blank";
        if ($data->status === "to_do") {
            $status_class = "b-warning";
        } else if ($data->status === "in_progress") {
            $status_class = "b-primary";
        } else {
            $checkbox_class = "checkbox-checked";
            $status_class = "b-success";
        }


        $check_status = js_anchor("<span class='$checkbox_class'></span>", array('title' => "", "class" => "", "data-id" => $data->id, "data-value" => $data->status === "done" ? "to_do" : "done", "data-act" => "update-task-status-checkbox")) . $data->id;

        $status = js_anchor(lang($data->status), array('title' => "", "class" => "", "data-id" => $data->id, "data-value" => $data->status, "data-act" => "update-task-status"));
        $dateline_text = "-";
        if ($data->deadline) {
            $dateline_text = format_to_date($data->deadline);
            if (get_my_local_time("Y-m-d") > $data->deadline && $data->status != "done") {
                $dateline_text = "<span class='text-danger'>" . $dateline_text . "</span> ";
            } else if (get_my_local_time("Y-m-d") == $data->deadline && $data->status != "done") {
                $dateline_text = "<span class='text-warning'>" . $dateline_text . "</span> ";
            }
        }

        $options = modal_anchor(get_uri("projects/task_modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_task'), "data-post-id" => $data->id));
        if ($this->login_user->is_admin) {
            $options.= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_task'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("projects/delete_task"), "data-action" => "delete"));
        }
        return array(
            $check_status,
            $title,
            $dateline_text,
            $project_title,
            $assigned_to,
            $status,
            $options,
            $status_class
        );
    }

    /* load comments view */

    function comments($project_id) {
        $this->access_only_team_members();


        $options = array("project_id" => $project_id);
        $view_data['comments'] = $this->Project_comments_model->get_details($options)->result();
        $view_data['project_id'] = $project_id;
        $this->load->view("projects/comments/index", $view_data);
    }

    /* load comments view */

    function customer_feedback($project_id) {
        $options = array("customer_feedback_id" => $project_id); //customer feedback id and project id is same
        $view_data['comments'] = $this->Project_comments_model->get_details($options)->result();
        $view_data['customer_feedback_id'] = $project_id;
        $this->load->view("projects/comments/index", $view_data);
    }

    /* save project comments */

    function save_comment() {
        $id = $this->input->post('id');

        $target_path = get_setting("timeline_file_path");
        $files_data = move_files_from_temp_dir_to_permanent_dir($target_path);

        $data = array(
            "created_by" => $this->login_user->id,
            "created_at" => get_current_utc_time(),
            "project_id" => $this->input->post('project_id'),
            "file_id" => $this->input->post('file_id') ? $this->input->post('file_id') : 0,
            "task_id" => $this->input->post('task_id') ? $this->input->post('task_id') : 0,
            "customer_feedback_id" => $this->input->post('customer_feedback_id') ? $this->input->post('customer_feedback_id') : 0,
            "comment_id" => $this->input->post('comment_id') ? $this->input->post('comment_id') : 0,
            "description" => $this->input->post('description'),
            "files" => $files_data
        );

        $save_id = $this->Project_comments_model->save_comment($data, $id);
        if ($save_id) {
            $data = "";
            if ($this->input->post("reload_list")) {
                $options = array("id" => $save_id);
                $view_data['comments'] = $this->Project_comments_model->get_details($options)->result();
                $data = $this->load->view("projects/comments/comment_list", $view_data, true);
            }
            echo json_encode(array("success" => true, "data" => $data, 'message' => lang('comment_submited')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    /* load all replies of a comment */

    function view_comment_replies($comment_id) {
        $view_data['reply_list'] = $this->Project_comments_model->get_details(array("comment_id" => $comment_id))->result();
        $this->load->view("projects/comments/reply_list", $view_data);
    }

    /* show comment reply form */

    function comment_reply_form($comment_id, $type_id = 0, $type = "project") {
        $view_data['comment_id'] = $comment_id;

        if ($type === "project") {
            $view_data['project_id'] = $project_id;
        } else if ($type === "task") {
            $view_data['task_id'] = $task_id;
        } else if ($type === "file") {
            $view_data['file_id'] = $file_id;
        }
        $this->load->view("projects/comments/reply_form", $view_data);
    }

    /* load files view */

    function files($project_id) {
        $this->access_only_team_members();
        $options = array("project_id" => $project_id);
        $view_data['files'] = $this->Project_files_model->get_details($options)->result();
        $view_data['project_id'] = $project_id;
        $this->load->view("projects/files/index", $view_data);
    }

    function view_file($file_id = 0) {
        $this->access_only_team_members();
        $file_info = $this->Project_files_model->get_details(array("id" => $file_id))->row();
        $file_url = get_file_uri("files/project_files/" . $file_info->project_id . "/" . $file_info->file_name);
        $view_data["file_url"] = $file_url;
        $view_data["is_image_file"] = is_image_file($file_info->file_name);
        $view_data["file_info"] = $file_info;
        $options = array("file_id" => $file_id);
        $view_data['comments'] = $this->Project_comments_model->get_details($options)->result();
        $view_data['file_id'] = $file_id;
        $this->load->view("projects/files/view", $view_data);
    }

    /* file upload modal */

    function file_modal_form() {
        $this->access_only_team_members();
        $view_data['model_info'] = $this->Project_files_model->get_one($this->input->post('id'));
        $view_data['project_id'] = $this->input->post('project_id') ? $this->input->post('project_id') : $view_data['model_info']->project_id;
        $this->load->view('projects/files/modal_form', $view_data);
    }

    /* save project file data and move temp file to parmanent file directory */

    function save_file() {
        $this->access_only_team_members();
        $project_id = $this->input->post('project_id');
        $files = $this->input->post("files");
        $success = false;
        $now = get_current_utc_time();

        $target_path = getcwd() . "/" . get_setting("project_file_path") . $project_id . "/";

        //process the fiiles which has been uploaded by dropzone
        if ($files && get_array_value($files, 0)) {
            foreach ($files as $file) {
                $file_name = $this->input->post('file_name_' . $file);
                $new_file_name = move_temp_file($file_name, $target_path);
                if ($new_file_name) {
                    $data = array(
                        "project_id" => $project_id,
                        "file_name" => $new_file_name,
                        "description" => $this->input->post('description_' . $file),
                        "file_size" => $this->input->post('file_size_' . $file),
                        "created_at" => $now,
                        "uploaded_by" => $this->login_user->id
                    );
                    $success = $this->Project_files_model->save($data);
                } else {
                    $success = false;
                }
            }
        }
        //process the files which has been submitted manually
        if ($_FILES) {
            $files = $_FILES['manualFiles'];
            if ($files && count($files) > 0) {
                $description = $this->input->post('description');
                foreach ($files["tmp_name"] as $key => $file) {
                    $temp_file = $file;
                    $file_name = $files["name"][$key];
                    $file_size = $files["size"][$key];

                    $new_file_name = move_temp_file($file_name, $target_path, $temp_file);
                    if ($new_file_name) {
                        $data = array(
                            "project_id" => $project_id,
                            "file_name" => $new_file_name,
                            "description" => get_array_value($description, $key),
                            "file_size" => $file_size,
                            "created_at" => $now,
                            "uploaded_by" => $this->login_user->id
                        );
                        $success = $this->Project_files_model->save($data);
                    }
                }
            }
        }

        if ($success) {
            echo json_encode(array("success" => true, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    /* upload a post file */

    function upload_file() {
        upload_file_to_temp();
    }

    /* check valid file for project */

    function validate_project_file() {
        return validate_post_file($this->input->post("file_name"));
    }

    /* delete a file */

    function delete_file() {
        $this->access_only_admin();
        $id = $this->input->post('id');
        if ($this->input->post('undo')) {
            if ($this->Project_files_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_file_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Project_files_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }

    /* download a file */

    function download_file($id) {
        $this->access_only_team_members();
        $this->load->library('zip');
        $file_info = $this->Project_files_model->get_one($id);
        $file_path = getcwd() . "/" . get_setting("project_file_path") . $file_info->project_id . "/" . $file_info->file_name;
        if (file_exists($file_path)) {
            $data = file_get_contents($file_path); // Read the file's contents
            force_download($file_info->file_name, $data);
        } else {
            die("No such file or directory.");
        }
    }

    /* download files by zip */

    function download_comment_files($id) {
        $this->load->library('zip');
        $files = $this->Project_comments_model->get_one($id)->files;
        $file_exists = false;
        if ($files) {
            $files = unserialize($files);
            $timeline_file_path = get_setting("timeline_file_path");

            $file_path = getcwd() . '/' . $timeline_file_path;

            foreach ($files as $file) {
                $path = $file_path . get_array_value($file, 'file_name');
                if (file_exists($path)) {
                    $this->zip->read_file($path);
                    $file_exists = true;
                }
            }
        }

        if ($file_exists) {
            $this->zip->download('domuments.zip');
        } else {
            die("No such file or directory.");
        }
    }

    /* list of files, prepared for datatable  */

    function files_list_data($project_id = 0) {
        $this->access_only_team_members();
        $options = array("project_id" => $project_id);
        $list_data = $this->Project_files_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_file_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    /* return a row of file list table */

    private function _file_row_data($id) {
        $options = array("id" => $id);
        $data = $this->Project_files_model->get_details($options)->row();
        return $this->_make_file_row($data);
    }

    /* prepare a row of file list table */

    private function _make_file_row($data) {
        $file_icon = get_file_icon(strtolower(pathinfo($data->file_name, PATHINFO_EXTENSION)));

        $image_url = get_avatar($data->uploaded_by_user_image);
        $uploaded_by = "<span class='avatar avatar-xs mr10'><img src='$image_url' alt='...'></span> $data->uploaded_by_user_name";
        $uploaded_by = get_team_member_profile_link($data->uploaded_by, $uploaded_by);

        $description = "<div class='pull-left'>" .
                js_anchor(remove_file_prefix($data->file_name), array('title' => "", "data-toggle" => "app-modal", "data-url" => get_uri("projects/view_file/" . $data->id)));

        if ($data->description) {
            $description .= "<br /><span>" . $data->description . "</span></div>";
        } else {
            $description .="</div>";
        }

        $options = anchor(get_uri("projects/download_file/" . $data->id), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
        if ($this->login_user->is_admin) {
            $options.= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("projects/delete_file"), "data-action" => "delete"));
        }

        return array($data->id,
            "<div class='fa fa-$file_icon font-22 mr10 pull-left'></div>" . $description,
            convert_file_size($data->file_size),
            $uploaded_by,
            format_to_datetime($data->created_at),
            $options
        );
    }

    /* load notes view */

    function notes($project_id) {
        $this->access_only_team_members();
        $view_data['project_id'] = $project_id;
        $this->load->view("projects/notes/index", $view_data);
    }

    /* load history view */

    function history($offset = 0, $project_id = 0) {
        $this->access_only_team_members();
        $view_data['project_id'] = $project_id;
        $view_data['offset'] = $offset;
        $view_data['activity_logs_params'] = array("log_for" => "project", "log_for_id" => $project_id, "limit" => 20, "offset" => $offset);
        $this->load->view("projects/history/index", $view_data);
    }

    /* load project members view */

    function members($project_id = 0) {
        $this->access_only_team_members();
        $view_data['project_id'] = $project_id;
        $this->load->view("projects/project_members/index", $view_data);
    }

    /* load payments tab  */

    function payments($project_id) {
        $this->access_only_team_members();
        if ($project_id) {
            $view_data['project_info'] = $this->Projects_model->get_details(array("id" => $project_id))->row();
            $view_data['project_id'] = $project_id;
            $this->load->view("projects/payments/index", $view_data);
        }
    }

    /* load invoices tab  */

    function invoices($project_id) {
        $this->access_only_team_members();
        if ($project_id) {
            $view_data['project_id'] = $project_id;
            $view_data['project_info'] = $this->Projects_model->get_details(array("id" => $project_id))->row();
            $this->load->view("projects/invoices/index", $view_data);
        }
    }

}

/* End of file projects.php */
/* Location: ./application/controllers/projects.php */
<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once("Pre_loader.php");

class Tickets extends Pre_loader {

    function __construct() {
        parent::__construct();
        $this->init_permission_checker("ticket");
    }

    // load ticket list view
    function index() {
       if ($this->login_user->user_type === "staff") {
           $this->template->rander("tickets/index");
       }  else{
            $view_data['client_id'] = $this->login_user->client_id;
            $view_data['page_type'] = "full";
            $this->template->rander("clients/tickets/index", $view_data);
        }
    }

    //load new tickt modal 
    function modal_form() {
        validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['ticket_types_dropdown'] = $this->Ticket_types_model->get_dropdown_list(array("title"), "id");

        $view_data['model_info'] = $this->Tickets_model->get_one($this->input->post('id'));
        $view_data['client_id'] = $this->input->post('client_id');
        $view_data['applicant_id']=$this->input->post('applicant_id');
        if ($this->login_user->user_type == "client") {
            $view_data['client_id'] = $this->login_user->client_id;
        } else {
            $view_data['clients_dropdown'] = $this->Clients_model->get_dropdown_list(array("company_name"));
        }
          
        //team member list
           if ($applicant_id) {
            $view_data['team_members_info'] = $this->Users_model->get_one($applicant_id);
        } else {

           
            $view_data['team_members_dropdown'] = array("" => "-") + $this->Users_model->get_dropdown_list(array("first_name", "last_name"), "id", $where);
        }

        $this->load->view('tickets/modal_form', $view_data);
    }

    // add a new ticket
    function save() {
         
        validate_submitted_data(array(
            "client_id" => "required|numeric",
            "ticket_type_id" => "required|numeric",
            "applicant_id"=>"required|numeric"
        ));
        

        $client_id = $this->input->post('client_id');
        
       // $this->access_only_allowed_members();

        $ticket_type_id = $this->input->post('ticket_type_id');
        $applicant_id=$this->input->post('applicant_id');
           $id = $this->input->post('id');
        //if this logged in user is a client then overwrite the client id
        if ($this->login_user->user_type === "client") {
            $client_id = $this->login_user->client_id;
        }


        $now = get_current_utc_time();
        $ticket_data = array(
            "title" => $this->input->post('title'),
            "client_id" => $client_id,
            "ticket_type_id" => $ticket_type_id,
            "applicant_id"=>$applicant_id,
            "created_by" => $this->login_user->id,
            "created_at" => $now,
            "last_activity_at" => $now
        );

        $ticket_id = $this->Tickets_model->save($ticket_data,$id);
          
        $target_path = get_setting("timeline_file_path");
        $files_data = move_files_from_temp_dir_to_permanent_dir($target_path);


        if ($ticket_id) {
            //ticket added. now add a comment in this ticket
            $comment_data = array(
                "description" => $this->input->post('description'),
                "ticket_id" => $ticket_id,
                "created_by" => $this->login_user->id,
                "created_at" => $now,
                "files" => $files_data
            );
            $this->Ticket_comments_model->save($comment_data);
            echo json_encode(array("success" => true, "data" => $this->_row_data($ticket_id), 'id' => $ticket_id, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    /* upload a file */

    function upload_file() {
        upload_file_to_temp();
    }

    /* check valid file for ticket */

    function validate_ticket_file() {
        return validate_post_file($this->input->post("file_name"));
    }

    // list of tickets, prepared for datatable 
    function list_data() {
       // $this->access_only_allowed_members();

        $status = $this->input->post("status");
        $applicant_id=$this->input->post('applicant_id');

        $options = array("status" => $status, "access_type" => $this->access_type,"allowed_members" => $this->allowed_members,"applicant_id"=>$applicant_id);

        $list_data = $this->Tickets_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    // list of tickets of a specific client, prepared for datatable 
    function ticket_list_data_of_client($client_id) {
        $this->access_only_allowed_members_or_client_contact($client_id);
       $applicant_id = $this->input->post('applicant_id');
        $options = array("client_id" => $client_id, "access_type" => $this->access_type,"applicant_id" => $applicant_id);
        
        $list_data = $this->Tickets_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }


  /* function ticket_list_data($applicant_id){
       $applicant_id = $this->input->post('applicant_id');
          $options = array("access_type" => $this->access_type,"applicant_id" => $applicant_id);

           $list_data = $this->Tickets_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
   }
*/

    // return a row of ticket list table 
    private function _row_data($id) {
        $options = array("id" => $id, "access_type" => $this->access_type);

        $data = $this->Tickets_model->get_details($options)->row();
        return $this->_make_row($data);
    }

    //prepare a row of ticket list table
    private function _make_row($data) {
         $meta_info = $this->_prepare_team_info($data);
        $ticket_status_class = "label-danger";
        if ($data->status === "new") {
            $ticket_status_class = "label-warning";
        } else if ($data->status === "closed") {
            $ticket_status_class = "label-success";
        } else if ($data->status === "client_replied" && $this->login_user->user_type === "client") {
            $data->status = "open"; //don't show client_replied status to client
        }
    

        $ticket_status = "<span class='label $ticket_status_class large'>" . lang($data->status) . "</span> ";
        return array($data->id,
            anchor(get_uri("tickets/view/" . $data->id), $data->title),
            $data->company_name ? anchor(get_uri("clients/view/" . $data->client_id), $data->company_name) : "-",
            $data->ticket_type,
            format_to_relative_time($data->last_activity_at),
            $ticket_status,
           get_team_member_profile_link($data->applicant_id, $meta_info->applicant_meta),
            modal_anchor(get_uri("tickets/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_ticket'),"data-post-id" => $data->id))
           
        );
    }

    private function _prepare_team_info($data) {
        $image_url = get_avatar($data->applicant_avatar);
        $data->applicant_meta = "<span class='avatar avatar-xs mr10'><img src='$image_url' alt=''></span>" . $data->applicant_name;
   return $data;
    }

    // load ticket details view 
    function view($ticket_id = 0) {


        if ($ticket_id) {
            $options = array("id" => $ticket_id);
            $options["access_type"] = $this->access_type;

            $ticket_info = $this->Tickets_model->get_details($options)->row();
            $this->access_only_allowed_members_or_client_contact($ticket_info->client_id);


            if ($ticket_info) {
                $view_data['ticket_info'] = $ticket_info;

                $comments_options = array("ticket_id" => $ticket_id);
                $view_data['comments'] = $this->Ticket_comments_model->get_details($comments_options)->result();

                $this->template->rander("tickets/view", $view_data);
            } else {
                show_404();
            }
        }
    }

    function save_comment() {
        $ticket_id = $this->input->post('ticket_id');
        $now = get_current_utc_time();

        $target_path = get_setting("timeline_file_path");
        $files_data = move_files_from_temp_dir_to_permanent_dir($target_path);

        $comment_data = array(
            "description" => $this->input->post('description'),
            "ticket_id" => $ticket_id,
            "created_by" => $this->login_user->id,
            "created_at" => $now,
            "files" => $files_data
        );

        validate_submitted_data(array(
            "description" => "required",
            "ticket_id" => "required|numeric"
        ));

        $comment_id = $this->Ticket_comments_model->save($comment_data);
        if ($comment_id) {
            //update ticket status;
            if ($this->login_user->user_type === "client") {
                $ticket_data = array(
                    "status" => "client_replied",
                    "last_activity_at" => $now
                );
            } else {
                $ticket_data = array(
                    "status" => "open",
                    "last_activity_at" => $now
                );
            }
            $this->Tickets_model->save($ticket_data, $ticket_id);

            $comments_options = array("id" => $comment_id);
            $view_data['comment'] = $this->Ticket_comments_model->get_details($comments_options)->row();
            $comment_view = $this->load->view("tickets/comment_row", $view_data, true);
            echo json_encode(array("success" => true, "data" => $comment_view, 'message' => lang('comment_submited')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    function save_ticket_status($ticket_id = 0) {
        $data = array(
            "status" => $this->input->post('value')
        );

        $save_id = $this->Tickets_model->save($data, $ticket_id);
        if ($save_id) {
            $options = array("id" => $ticket_id, "access_type" => $this->access_type);

            $ticket_info = $this->Tickets_model->get_details($options)->row();

            $this->access_only_allowed_members_or_client_contact($ticket_info->client_id);

            $view_data['ticket_info'] = $ticket_info;
            $title_view = $this->load->view("tickets/ticket_sub_title", $view_data, true);
            echo json_encode(array("success" => true, "data" => $title_view, "message" => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, lang('error_occurred')));
        }
    }

    /* download files by zip */

    function download_comment_files($id) {
        $this->load->library('zip');
        $files = $this->Ticket_comments_model->get_one($id)->files;
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

     //view leave list of login user
    function ticket_info() {
        $view_data['applicant_id'] = $this->login_user->id;
        if ($this->input->is_ajax_request()) {
            $this->load->view("team_members/ticket_info", $view_data);
        } else {
            $view_data['page_type'] = "full";
            $this->template->rander("team_members/ticket_info", $view_data);
        }
    }


}



/* End of file tickets.php */
/* Location: ./application/controllers/tickets.php */
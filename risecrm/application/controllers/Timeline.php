<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once("Pre_loader.php");

class Timeline extends Pre_loader {

    public function __construct() {
        parent::__construct();
        $this->access_only_team_members();
    }

    /* load timeline view */

    function index() {
        $members_options = array(
            "status" => "active",
            "user_type" => "staff",
            "exclude_user_id" => $this->login_user->id
        );
        $view_data['team_members'] = $this->Users_model->get_details($members_options)->result();
        $this->template->rander("timeline/index", $view_data);
    }

    /* save a post */

    function save() {
        validate_submitted_data(array(
            "description" => "required"
        ));

        $id = $this->input->post('id');

        $target_path = get_setting("timeline_file_path");

        $files_data = move_files_from_temp_dir_to_permanent_dir($target_path);

        $data = array(
            "created_by" => $this->login_user->id,
            "created_at" => get_current_utc_time(),
            "post_id" => $this->input->post('post_id'),
            "description" => $this->input->post('description'),
            "share_with" => "",
            "files" => $files_data,
        );
        $save_id = $this->Posts_model->save($data, $id);
        if ($save_id) {
            $data = "";
            if ($this->input->post("reload_list")) {
                $options = array("id" => $save_id);
                $view_data['posts'] = $this->Posts_model->get_details($options)->result;
                $view_data['result_remaining'] = 0;
                $view_data['is_first_load'] = false;
                $data = $this->load->view("timeline/post_list", $view_data, true);
            }
            echo json_encode(array("success" => true, "data" => $data, 'message' => lang('comment_submited')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    /* load all replies of a post */

    function view_post_replies($post_id) {
        $view_data['reply_list'] = $this->Posts_model->get_details(array("post_id" => $post_id))->result;
        $this->load->view("timeline/reply_list", $view_data);
    }

    /* show post reply form */

    function post_reply_form($post_id) {
        $view_data['post_id'] = $post_id;
        $this->load->view("timeline/reply_form", $view_data);
    }

    /* upload a post file */

    function upload_file() {
        upload_file_to_temp();
    }

    /* check valid file for post */

    function validate_post_file() {
        return validate_post_file($this->input->post("file_name"));
    }

    function download_files($id) {
        $this->load->library('zip');
        $files = $this->Posts_model->get_one($id)->files;
        $files = unserialize($files);
        $timeline_file_path = get_setting("timeline_file_path");

        $file_path = getcwd() . '/' . $timeline_file_path;
        $file_exists = false;
        foreach ($files as $file) {
            $path = $file_path . get_array_value($file, 'file_name');
            if (file_exists($path)) {
                $this->zip->read_file($path);
                $file_exists = true;
            }
        }
        if ($file_exists) {
            $this->zip->download('domuments.zip');
        } else {
            die("No such file or directory.");
        }
    }

    /* load more posts */

    function load_more_posts($offset = 0) {
        timeline_widget(array("limit" => 20, "offset" => $offset));
    }

}

/* End of file timeline.php */
    /* Location: ./application/controllers/timeline.php */
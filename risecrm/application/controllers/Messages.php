<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once("Pre_loader.php");

class Messages extends Pre_loader {

    function __construct() {
        parent::__construct();
    }

    function index() {
        redirect("messages/inbox");
    }

    /* show new message modal */

    function modal_form($user_id = 0) {
        /*
         * team members can send message to all team members
         * clients can only send message to team members (as defined on Client settings)
         * team members can send message to clients (as defined on Client settings)
         */
        $client_message_users = get_setting("client_message_users");
        if ($this->login_user->user_type === "staff") {
            //user is team member
            $client_message_users_array = explode(",", $client_message_users);
            if (in_array($this->login_user->id, $client_message_users_array)) {
                //user can send message to clients
                $users = $this->Users_model->get_team_members_and_clients("", "", $this->login_user->id)->result();
            } else {
                //user can send message only to team members
                $users = $this->Users_model->get_team_members_and_clients("staff", "", $this->login_user->id)->result();
            }
        } else {
            //user is a client contact
            if ($client_message_users) {
                $users = $this->Users_model->get_team_members_and_clients("staff", $client_message_users)->result();
            } else {
                //client can't send message to any team members
                redirect("forbidden");
            }
        }


        $view_data['users_dropdown'] = array("" => "-");
        if ($user_id) {
            $view_data['message_user_info'] = $this->Users_model->get_one($user_id);
        } else {
            foreach ($users as $user) {
                $client_tag = "";
                if ($user->user_type === "client") {
                    $client_tag = "  - " . lang("client").": ".$user->company_name ."";
                }
                $view_data['users_dropdown'][$user->id] = $user->first_name . " " . $user->last_name . $client_tag;
            }
            /// $view_data['users_dropdown'] = array("" => "-") + $this->Users_model->get_dropdown_list(array("first_name", "last_name"), "id", array("user_type" => "staff", "id !=" => $this->login_user->id));
        }

        $this->load->view('messages/modal_form', $view_data);
    }

    /* show inbox */

    function inbox($auto_select_index = "") {
        $view_data['mode'] = "inbox";
        $view_data['auto_select_index'] = $auto_select_index;
        $this->template->rander("messages/index", $view_data);
    }

    /* show sent items */

    function sent_items($auto_select_index = "") {
        $view_data['mode'] = "sent_items";
        $view_data['auto_select_index'] = $auto_select_index;
        $this->template->rander("messages/index", $view_data);
    }

    /* list of messages, prepared for datatable  */

    function list_data($mode = "inbox") {
        if ($mode !== "inbox") {
            $mode = "sent_items";
        }

        $options = array("user_id" => $this->login_user->id, "mode" => $mode);
        $list_data = $this->Messages_model->get_list($options)->result();

        $result = array();

        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data, $mode);
        }

        echo json_encode(array("data" => $result));
    }

    /* return a message details */

    function view($encrypted_id = 0, $mode = "", $reply = 0) {
        $message_id = decode_id($encrypted_id, "message_id");
        check_required_hidden_fields(array($message_id));

        $message_mode = $mode;
        if ($reply == 1 && $mode == "inbox") {
            $message_mode = "sent_items";
        } else if ($reply == 1 && $mode == "sent_items") {
            $message_mode = "inbox";
        }

        $options = array("id" => $message_id, "user_id" => $this->login_user->id, "mode" => $message_mode);
        $view_data["message_info"] = $this->Messages_model->get_details($options)->row();

        //change message status to read
        $this->Messages_model->set_message_status_as_read($view_data["message_info"]->id, $this->login_user->id);

        $replies_options = array("message_id" => $message_id, "user_id" => $this->login_user->id);
        $view_data["replies"] = $this->Messages_model->get_details($replies_options)->result();

        $view_data["encrypted_message_id"] = $encrypted_id;
        $view_data["mode"] = $mode;
        $view_data["is_reply"] = $reply;
        echo json_encode(array("success" => true, "data" => $this->load->view("messages/view", $view_data, true), "test" => $options));
    }

    /* prepare a row of message list table */

    private function _make_row($data, $mode = "") {
        $image_url = get_avatar($data->user_image);
        $created_at = format_to_relative_time($data->created_at);
        $encrypted_id = encode_id($data->main_message_id, "message_id");
        $label = "";
        $reply = "";
        $status = "";
        $subject = $data->subject;
        if ($mode == "inbox") {
            $status = $data->status;
        }
        if ($data->reply_subject) {
            $label = " <label class='label label-success inline-block'>" . lang('reply') . "</label>";
            $reply = "1";
            $subject = $data->reply_subject;
        }

        $message = "<div class='pull-left message-row $status' data-id='$encrypted_id' data-index='$data->main_message_id' data-reply='$reply'><div class='media-left'>
                        <span class='avatar avatar-xs'>
                            <img src='$image_url' />
                        </span>
                    </div>
                    <div class='media-body'>
                        <div class='media-heading'>
                            <strong> $data->user_name</strong>
                                  <span class='text-off pull-right'>$created_at</span>
                        </div>
                        $label $subject
                    </div></div>
                  
                ";
        return array(
            $message,
            $data->created_at,
            $status
        );
    }

    /* send new message */

    function send_message() {
        $message_data = array(
            "from_user_id" => $this->login_user->id,
            "to_user_id" => $this->input->post('to_user_id'),
            "subject" => $this->input->post('subject'),
            "message" => $this->input->post('message'),
            "created_at" => get_current_utc_time()
        );

        if ($this->Messages_model->save($message_data)) {
            echo json_encode(array("success" => true, 'message' => lang('message_sent')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    /* reply to an existing message */

    function reply() {
        $encrypted_message_id = $this->input->post('message_id');
        $message_id = decode_id($encrypted_message_id, "message_id");
        check_required_hidden_fields(array($message_id));

        $message_info = $this->Messages_model->get_one($message_id);
        if ($message_info->id) {
            //check, where we have to send this message
            $to_user_id = 0;
            if ($message_info->from_user_id === $this->login_user->id) {
                $to_user_id = $message_info->to_user_id;
            } else {
                $to_user_id = $message_info->from_user_id;
            }

            $message_data = array(
                "from_user_id" => $this->login_user->id,
                "to_user_id" => $to_user_id,
                "message_id" => $message_id,
                "subject" => "",
                "message" => $this->input->post('reply_message'),
                "created_at" => get_current_utc_time()
            );
            $save_id = $this->Messages_model->save($message_data);

            if ($save_id) {
                $options = array("id" => $save_id, "user_id" => $this->login_user->id);
                $view_data['reply_info'] = $this->Messages_model->get_details($options)->row();
                echo json_encode(array("success" => true, 'message' => lang('message_sent'), 'data' => $this->load->view("messages/reply_row", $view_data, true)));
                return;
            }
        }
        echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
    }

    /* prepare notifications */

    function get_notifications() {
        $notifiations = $this->Messages_model->get_notifications($this->login_user->id, $this->login_user->message_checked_at);
        $view_data['notifications'] = $notifiations->result();
        echo json_encode(array("success" => true, 'total_messages' => $notifiations->num_rows(), 'notification_list' => $this->load->view("messages/notifications", $view_data, true)));
    }

    function update_notification_checking_status() {
        $now = get_current_utc_time();
        $this->Users_model->save(array("message_checked_at" => $now), $this->login_user->id);
    }

  function delete( $id )
    {  
        $this->load->model('Messages_model');
       
         $this->Messages_model->delete_message($id);
       
         return redirect('Messages/sent_items/'.$id);
    }


}

/* End of file messages.php */
/* Location: ./application/controllers/messages.php */
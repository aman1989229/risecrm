<?php

class Template {

    public function rander($view, $data = array()) {
        $ci = get_instance();
        $data['content_view'] = $view;
        $ci->load->view('layout/index', $data);
    }

}

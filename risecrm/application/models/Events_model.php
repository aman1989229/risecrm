<?php

class Events_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'events';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $events_table = $this->db->dbprefix('events');
        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $events_table.id=$id";
        }

        $start_date = get_array_value($options, "start_date");
        if ($start_date) {
            $where .= " AND DATE($events_table.start_date)>='$start_date'";
        }

        $end_date = get_array_value($options, "end_date");
        if ($end_date) {
            $where .= " AND DATE($events_table.end_date)<='$end_date'";
        }

        $user_id = get_array_value($options, "user_id");
        if ($user_id) {
            $where .= " AND $events_table.created_by=$user_id";
        }

        $limit = get_array_value($options, "limit");
        $limit = $limit ? $limit : "20000";
        $offset = get_array_value($options, "offset");
        $offset = $offset ? $offset : "0";

        $sql = "SELECT $events_table.*
        FROM $events_table
        WHERE $events_table.deleted=0 $where
        ORDER BY $events_table.start_date ASC
        LIMIT $offset, $limit";
        return $this->db->query($sql);
    }

    function count_events_today($user_id = 0) {
        $events_table = $this->db->dbprefix('events');
        $now = get_my_local_time("Y-m-d");
        $sql = "SELECT COUNT($events_table.id) AS total
        FROM $events_table
        WHERE $events_table.deleted=0 AND $events_table.created_by = $user_id AND ($events_table.start_date='$now' OR $events_table.end_date='$now')";
        return $this->db->query($sql)->row()->total;
    }

}

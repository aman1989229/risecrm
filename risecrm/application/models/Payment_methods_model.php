<?php

class Payment_methods_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'payment_methods';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $payment_methods_table = $this->db->dbprefix('payment_methods');
        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where = " AND $payment_methods_table.id=$id";
        }

        $sql = "SELECT $payment_methods_table.*
        FROM $payment_methods_table
        WHERE $payment_methods_table.deleted=0 $where";
        return $this->db->query($sql);
    }

}

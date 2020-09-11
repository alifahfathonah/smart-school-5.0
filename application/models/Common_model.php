<?php

class Common_model extends CI_Model {

    public function dbSelectRow($selection, $table, $where) {
        $sql = "Select " . $selection . " From ". $table . " Where ";
        if ($where) {
            $sql .= $where;
        }
        $result = $this->db->query($sql);
        return $result->row();
    }

    public function dbSelect($selection, $table, $where) {
        $sql = "Select " . $selection . " From " . $table . " Where ";
        if ($where) {
            $sql .= $where;
        }
        $result = $this->db->query($sql);
        return $result->result();
    }
    
    public function dbQuery($sql) {
        $result = $this->db->query($sql);
        return $result->result();
    }

    public function dbInsert($table, $data) {
        $this->db->insert($table, $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    function update_where($tbl_name, $where, $data) {
        $this->db->where($where);
        return $this->db->update($tbl_name, $data);
    }

    function delete($column, $id, $table) {
        $this->db->where($column, $id);
        $res = $this->db->delete($table);
        return $res;
    }       
}

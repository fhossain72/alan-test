<?php

if(!defined('BASEPATH'))
    exit('No direct script access allowed');

class Global_model extends CI_Model {
    function __construct() {
        parent::__construct();
    }

    public function insert($table, $data) {
        $this->db->insert($table, $data);
        return $this->db->insert_id();
    }

    /**
     * @param $table
     * @param $data
     * @param $where
     *
     * @return mixed
     */
    public function update($table, $data, $where) {
        $this->db->where($where);
        return $this->db->update($table, $data);
    }

    /**
     * @param $table
     * @param $where
     *
     * @return mixed
     */
    public function delete($table, $where) {
        return $this->db->delete($table, $where);
    }

    public function get_data($table, $where) {
        $query = $this->db->get_where($table, $where);
        if($query->result()) {
            return $query->row();
        }
        else {
            return FALSE;
        }
    }

    public function get_row($table, $where, $field_rows = '*', $order_by = false, $where_in = false) {
        $this->db->select($field_rows)->from($table);

        if (!empty($where)) {
            $this->db->where($where);
        }

        if (!empty($where_in)) {
            $this->db->where_in($where_in['key'], $where_in['values']);
        }

        if (!empty($order_by)) {
            $this->db->order_by($order_by['filed'], $order_by['order']);
        }

        $query = $this->db->get();
        if ($query->result()) {
            return $query->row();
        } else {
            return FALSE;
        }
    }

    public function get_row_join($table, $where = FALSE, $field_rows = '*', $order_by = FALSE, $where_in_parmas = FALSE, $join_parmas = FALSE, $group_by = FALSE) {
        $this->db->select($field_rows);
        $this->db->from($table);

        if(!empty($join_parmas)) {
            foreach ($join_parmas as $join_item) {
                if(isset($join_item['type'])) {
                    $this->db->join($join_item['table'], $join_item['relation'], $join_item['type']);
                }
                else {
                    $this->db->join($join_item['table'], $join_item['relation']);
                }
            }
        }

        if(!empty($where)) {
            $this->db->where($where);
        }

        if(!empty($where_in_parmas)) {
            foreach ($where_in_parmas as $where_in) {
                $this->db->where_in($where_in['key'], $where_in['values']);
            }
        }

        if(!empty($group_by)) {
            $this->db->group_by($group_by);
        }

        if(!empty($order_by)) {
            $this->db->order_by($order_by['field'], $order_by['order']);
        }

        $query = $this->db->get();

        if($query->num_rows() > 0) {
            return $query->row();
        }
    }

    public function get($table, $where = FALSE, $field_rows = '*', $limit = FALSE, $order_by = FALSE, $where_in = FALSE, $group_by = FALSE) {
        $this->db->select($field_rows)->from($table);

        if(!empty($where)) {
            $this->db->where($where);
        }

        if(!empty($where_in)) {
            $this->db->where_in($where_in['key'], $where_in['values']);
        }

        if(!empty($limit)) {
            $this->db->limit($limit['limit'], $limit['start']);
        }

        if(!empty($group_by)) {
            $this->db->group_by($group_by);
        }

        if(!empty($order_by)) {
            $this->db->order_by($order_by['filed'], $order_by['order']);
        }

        $query = $this->db->get();
        if($query->num_rows() > 0) {
            return $query->result();
        }
        else {
            return FALSE;
        }
    }

    public function get_count($table, $where = FALSE, $group_by = FALSE) {
        $this->db->from($table);
        if(!empty($where)) {
            $this->db->where($where);
        }

        if(!empty($group_by)) {
            $this->db->group_by($group_by);
        }
        return $this->db->count_all_results();
    }

    public function get_join($table, $where = FALSE, $field_rows = '*', $limit = FALSE, $order_by = FALSE, $where_in_parmas = FALSE, $join_parmas = FALSE, $group_by = FALSE) {
        $this->db->select($field_rows);
        $this->db->from($table);

        if(!empty($join_parmas)) {
            foreach ($join_parmas as $join_item) {
                if(isset($join_item['type'])) {
                    $this->db->join($join_item['table'], $join_item['relation'], $join_item['type']);
                }
                else {
                    $this->db->join($join_item['table'], $join_item['relation']);
                }
            }
        }

        if(!empty($where)) {
            $this->db->where($where);
        }

        if(!empty($where_in_parmas)) {
            foreach ($where_in_parmas as $where_in) {
                $this->db->where_in($where_in['key'], $where_in['values']);
            }
        }

        if (!empty($limit)) {
            $this->db->limit($limit['limit'], $limit['start']);
        }

        if(!empty($group_by)) {
            $this->db->group_by($group_by);
        }

        if(!empty($order_by)) {
            $this->db->order_by($order_by['field'], $order_by['order']);
        }

        $query = $this->db->get();

        if($query->num_rows() > 0) {
            return $query->result();
        }
    }
}
<?php

/**
 * @package      : Model
 * @subpackage   : User
 * @category     : Admin
 * @author       : Leeladitya Kumar Dhali <aditya.cse04@gmail.com> 
 * @copyright    : Technobd Web Solution (Pvt.) Ltd, Aditya 2015 @ Buet91 Club
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class User_model extends CI_Model {

    public $_table;
    public $_primary_key;

    function __construct() {
        parent::__construct();

        $this->_table = 'rma_users';
        $this->_primary_key = 'user_id';
    }

    // insert new record into user table
    public function create($data) {
        $this->db->insert('rma_users', $data);
        return $this->db->affected_rows();
    }

    // get the login information
    public function login($user_name) {
        $this->db->select('*')->from('rma_users')->where('user_name', $user_name);
        $query = $this->db->get();
        return $query->row();
    }

    //get the information using email
    public function check_email($email) {
        $this->db->select('*')->from('rma_users')->where('email', $email);
        $query = $this->db->get();
        return $query->row();
    }

    // get the specific user information
    public function getSingleUserInfo($user_id) {
        $this->db->select('*')->from('rma_users')->where('user_id', $user_id);
        $query = $this->db->get();
        return $query->row();
    }

    // update the specific user information
    public function update($data, $user_id) {
        if ($this->db->update('rma_users', $data, "user_id = " . $user_id . ""))
            return TRUE;
        else
            return FALSE;
    }

    // get the information based on filtaring
    public function getAll($count = false, $params = array(), $num = null, $offset = null) {
        $select = $this->db->select('*')->from('rma_users');
        $select->order_by('user_id', 'DESC');
        if (count($params)) {
            if (!empty($params['own_level_id']))
                $select->where("level_id >", $params['own_level_id']);
        }
        if ($num) {
            $select->limit($num, $offset);
        }
        if ($count) {
            return $select->count_all_results();
        } else {
            $query = $this->db->get();
            return $query->result();
        }
    }

    // delete single information
    public function delete($user_id = 0) {
        $this->db->delete('rma_users', array('user_id' => $user_id));
        return $this->db->affected_rows();
    }

    /*     * ******* Permission section *************** */

    // save the permission
    public function save_permission($data) {
        $this->db->insert('permissions', $data);
        return $this->db->affected_rows();
    }

    // update permission
    public function update_permission($data, $permission_id) {
        if ($this->db->update('permissions', $data, array('permission_id' => $permission_id)))
            return TRUE;
        else
            return FALSE;
    }

    public function get_permission($params = array()) {
        $this->db->select('*')->from('permissions')
                ->where('level_id', $params['level_id'])
                ->where('module', $params['module'])
                ->where('action', $params['action']);
        $query = $this->db->get();
        return $query->row();
    }

}

/* End of file user_model.php */
/* Location: ./application/model/admin/user_model.php */
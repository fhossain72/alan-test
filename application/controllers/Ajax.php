<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends CI_Controller {
    function __construct() {
        parent::__construct();
        //check login if user is login or not if nor redirect to login page
        if(!adminLoginCheck()) {
            $this->session->set_flashdata('success_msg', 'Pleae Login First !!');
            redirect('auth/login');
        }
    }

    public function load_order_rma_content() {
        if($this->input->post('order_number') || $this->input->get('order_number')) {
            $order_number = $this->input->post('order_number') ? $this->input->post('order_number') : $this->input->get('order_number');
            $user_id      = get_current_user_id();

            // get order items
            $order_items = $this->global_model->get('order_item', array('order_id' => $order_number));

            // process ajax html content
            $html = '<div class="panel-body"><p style="color:#d00;">Invalid order request!</p></div>';

            if(!empty($order_items)) {
                // insert search log
                $search_log_data             = array();
                $search_log_data['order_id'] = $order_number;
                $search_log_data['user_id']  = $user_id;
                $search_log_data['added']    = date('Y-m-d H:i:s');
                $this->global_model->insert('rma_search_log', $search_log_data);

                $data                 = array();
                $data['order_items']  = $order_items;
                $data['order_number'] = $order_number;
                $data['user_id']      = $user_id;

                $html = $this->load->view('rma/ajax_order_search', $data, TRUE);
                //echo $html;
                //exit;
            }

            echo json_encode($html);
            exit;
        }
    }
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rma extends CI_Controller {

    public $_module_name;
    public $_module;

    function __construct() {
        parent::__construct();

        // set the global variable
        $this->_module_name = 'RMA';
        $this->_module      = 'rma';

        //check login if user is login or not if nor redirect to login page
        if(!adminLoginCheck()) {
            redirect('auth/login');
        }
    }

    public function index() {
        $data                = array();
        $data['page_active'] = $this->_module . '_manage';
        $data['page_title']  = 'Return List';

        // where clause params
        $params = array('user_id' => get_current_user_id());
        if($this->input->get('order_id') ){
            $params['order_id'] = $this->input->get('order_id');
        }

        // pagination data
        $per_page      = 10;
        $data['start'] = $offset = ($this->input->get('page')) ? ($per_page * ($this->input->get('page') - 1)) : 0;
        $data['total'] = $total_rows = $this->global_model->get_count('rma_parent_request', $params);

        // pagination args
        $paging_args = array('limit' => $per_page,
                             'start' => $offset
        );

        // create pagination
        createPagging('rma', $total_rows, $per_page);

        // order by args
        $order_by = array('filed' => 'id',
                          'order' => 'DESC'
        );

        // get the all information
        $data['rma_parent_request'] = $this->global_model->get('rma_parent_request', $params, '*', $paging_args, $order_by);

        // load the views
        $data['layout'] = $this->load->view($this->_module . '/manage', $data, TRUE);
        $this->load->view('template', $data);
    }

    // add new information
    public function request() {
        $data['page_active'] = $this->_module . '_request';
        $data['page_title']  = 'Submit Return';

        // check post submit
        if($this->input->post('submit')) {
            $this->form_validation->set_rules('order_number', 'Order Number', 'trim|required');
            $this->form_validation->set_rules('order_items[]', 'Order Items', 'trim|required');
            $this->form_validation->set_rules('variation_ids[]', 'Items', 'trim|required');

            // check the validation
            if($this->form_validation->run()) {
                $user_id       = get_current_user_id();
                $order_number  = $this->input->post('order_number');
                $order_items   = $this->input->post('order_items');
                $variation_ids = $this->input->post('variation_ids');
                $shipping_cost = $this->input->post('shipping_cost');

                $inserted_items = 0;

                // insert parent request log
                $rma_parent_request_data                  = array();
                $rma_parent_request_data['order_id']      = $order_number;
                $rma_parent_request_data['user_id']       = $user_id;
                $rma_parent_request_data['shipping_cost'] = $shipping_cost;
                $rma_parent_request_data['total_request'] = 1;
                $rma_parent_request_data['added']         = date('Y-m-d H:i:s');

                if($parent_request_id = $this->global_model->insert('rma_parent_request', $rma_parent_request_data)) {
                    foreach ($variation_ids as $variation_id) {
                        $order_item      = $order_items[$variation_id];
                        $product_id      = $order_item['product_id'];
                        $item_name       = $order_item['item_name'];
                        $item_quantity   = $order_item['qty'];
                        $return_quantity = $order_item['return_quantity'];
                        $return_reason   = $order_item['return_reason'];

                        if($return_quantity > 0 && !empty($return_reason)) {
                            // insert request log
                            $rma_request_data                      = array();
                            $rma_request_data['parent_request_id'] = $parent_request_id;
                            $rma_request_data['order_id']          = $order_number;
                            $rma_request_data['user_id']           = $user_id;
                            $rma_request_data['product_id']        = $product_id;
                            $rma_request_data['variation_id']      = $variation_id;
                            $rma_request_data['item_name']         = $item_name;
                            $rma_request_data['item_quantity']     = $item_quantity;
                            $rma_request_data['return_quantity']   = $return_quantity;
                            $rma_request_data['return_reason']     = $return_reason;
                            $rma_request_data['added']             = date('Y-m-d H:i:s');

                            if(isset($order_item['bundle_product_id'])) {
                                $rma_request_data['is_bundle']         = 1;
                                $rma_request_data['bundle_product_id'] = $order_item['bundle_product_id'];
                            }

                            if($this->global_model->insert('rma_request', $rma_request_data)) {
                                $inserted_items++;

                                $parent_variation_id = $order_item['parent_variation_id'];
                                $this->db->query("UPDATE pl_product_stock SET stock_quantity = stock_quantity + $return_quantity WHERE variation_id = $parent_variation_id");
                            }
                        }
                    }

                    $this->global_model->update('rma_parent_request', array('total_request' => $inserted_items), array('id' => $parent_request_id));
                }

                if($inserted_items > 0) {
                    $this->session->set_flashdata('success_msg', 'Return request submitted successfully!');
                    redirect(site_url('rma'));
                }
                else {
                    $data['error_msg'] = 'Request not submitted successfully!';
                }
            }
        }

        // load the views
        $data['layout'] = $this->load->view($this->_module . '/request', $data, TRUE);
        $this->load->view('template', $data);
    }
}
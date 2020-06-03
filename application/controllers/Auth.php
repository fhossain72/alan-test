<?php
if(!defined('BASEPATH'))
    exit('No direct script access allowed');

class Auth extends CI_Controller {
    public $_pageGroup;
    public $module;

    function __construct() {
        parent::__construct();
        // load specific model
        $this->load->model('user_model');

        // load specific variable
        $this->_pageGroup = "User";
        $this->module     = "auth";
    }

    public function index() {
        if(adminLoginCheck()) {
            redirect('rma');
        }
        else {
            redirect(site_url('auth/login'));
        }
    }

    // login page for user panel
    public function login() {
        $data               = array();
        $data['pageGroup']  = $this->_pageGroup;
        $data['page_title'] = "Login";

        // check user login details
        if(adminLoginCheck()) {
            redirect('rma');
        }

        // login submit process
        if($this->input->post('submit')) {
            $this->form_validation->set_rules('user_name', 'Username', 'required|min_length[3]|max_length[30]');
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[3]|max_length[30]');

            if($this->form_validation->run()) {
                $data['user_name'] = $this->input->post('user_name');
                $data['password']  = $this->input->post('password');

                // get user details from db
                $result = $this->user_model->login($this->input->post('user_name'));

                if(!empty($result)) {
                    $given_password = md5($this->input->post('password'));

                    if($given_password == $result->password) {
                        if($result->status == 1) {
                            // set user session
                            $user_data = array(
                                'user_id'    => $result->user_id,
                                'user_name'  => $result->user_name,
                                'first_name' => $result->first_name,
                                'status'     => $result->status,
                                'email'      => $result->email,
                                'photo'      => $result->photo
                            );
                            $this->session->set_userdata($user_data);

                            // set login success message
                            $this->session->set_flashdata('success_msg', 'You have login successfully !!');
                            redirect('rma');
                        }
                        else {
                            // set user inactive message
                            $data['error_msg'] = "User is not active!! Please Active First!!";
                        }
                    }
                    else {
                        // set password incorrect message
                        $data['error_msg'] = "Password is incorrect try again";
                    }
                }
                else {
                    // set username incorrect message
                    $data['error_msg'] = "Username is incorrect try again";
                }
            }
        }

        // load views
        $data['layout'] = $this->load->view('login', $data, TRUE);
        $this->load->view('template', $data);
    }

    // logout from panel
    public function logout() {
        // session destroy
        $this->session->sess_destroy();

        // set logout success message
        $this->session->set_flashdata('success_msg', 'Successfully logout');
        redirect('auth/login');
    }

    public function change_password() {
        $data               = array();
        $data['pageGroup']  = $this->_pageGroup;
        $data['page_title'] = "Change Password";

        // check user login details
        if(!adminLoginCheck()) {
            redirect('auth/login');
        }

        // change password submit process
        if($this->input->post('submit')) {
            $this->form_validation->set_rules('old_password', 'Old Password', 'required|min_length[3]|max_length[30]|callback_validate_credentials');
            $this->form_validation->set_rules('new_password', 'New Password', 'required|min_length[3]|max_length[30]');
            $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[new_password]');

            if($this->form_validation->run()) {
                $user_id = get_current_user_id();

                $updateData             = array();
                $updateData['password'] = md5($this->input->post('new_password'));

                if($this->user_model->update($updateData, $user_id)) {
                    $data['success_msg'] = "Password changed successfully!";
                }
                else {
                    $data['error_msg'] = "Password faild to change!";
                }
            }
        }

        // load the views
        $data['layout'] = $this->load->view('change_password', $data, TRUE);
        $this->load->view('template', $data);
    }

    // forgot password
    public function forget_password() {
        $data               = array();
        $data['pageGroup']  = $this->_pageGroup;
        $data['page_title'] = "Forget Password";

        // check user login details
        if(adminLoginCheck()) {
            redirect('rma');
        }

        // check if click on the submit button
        if($this->input->post('submit')) {
            // set the validation rule
            $this->form_validation->set_rules('user_name', 'Username or Email ', 'trim|required');

            // run the validation
            if($this->form_validation->run()) {
                $params = "`email` = '" . $this->input->post('user_name') . "' OR `user_name`='" . $this->input->post('user_name') . "'";

                // get data from database
                $user = $this->global_model->get_row('rma_users', $params, '*');

                // check user data exist or not
                if($user) {
                    // generate the password and reset code
                    $updateData                  = array();
                    $updateData['code']          = $code = uniqid();
                    $updateData['code_lifetime'] = date('Y-m-d H:i:s', strtotime('+24 hours', time()));

                    if($this->global_model->update('rma_users', $updateData, array('user_id' => $user->user_id))) {
                        // send mail to user
                        $template_data            = array();
                        $template_data['user']    = $user;
                        $template_data['code']    = $code;
                        $mail_data                = array();
                        $mail_data['header_text'] = 'Password reset request';
                        $mail_data['contents']    = $this->load->view('mail_templates/mail_password_reset', $template_data, TRUE);
                        $mail_data['footer_text'] = $this->config->item('site_title');
                        $message                  = $this->load->view('mail_templates/master_template', $mail_data, TRUE);

                        if(mailSend($user->email, 'Password reset request for ' . $this->config->item('site_title'), $message)) {
                            $this->session->set_flashdata('success_msg', "Password reset email sent to your email");

                            redirect("auth/login");
                        }
                        else {
                            $data['error_msg'] = "Error: mail not send....";
                        }
                    }
                }
                else {
                    $data['error_msg'] = "Invalid username or email";
                }
            }
        }

        // load views
        $data['layout'] = $this->load->view('forget_password', $data, TRUE);
        $this->load->view('template', $data);
    }

    // reset password
    public function reset_password($code) {
        $data               = array();
        $data['pageGroup']  = $this->_pageGroup;
        $data['page_title'] = "Reset Password";

        // check user login details
        if(adminLoginCheck()) {
            redirect('rma');
        }

        // validate user by reset code
        $params = array(
            'code_lifetime >=' => date('Y-m-d H:i:s'),
            'code'             => $code
        );

        $user = $this->global_model->get_row('rma_users', $params, '*');

        if($user) {
            if($this->input->post('submit')) {
                $this->form_validation->set_rules('new_password', 'New Password', 'trim|required');
                $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'trim|required|matches[new_password]');

                if($this->form_validation->run()) {
                    // update the login information
                    $updateData                  = array();
                    $updateData['password']      = md5($this->input->post('new_password'));
                    $updateData['code']          = '';
                    $updateData['code_lifetime'] = '';
                    $this->global_model->update('rma_users', $updateData, array('user_id' => $user->user_id));

                    // set success message
                    $this->session->set_flashdata('success_msg', 'Your Password reset successfully.');
                    redirect("auth/login");
                }
            }

            // load views
            $data['layout'] = $this->load->view('reset_password', $data, TRUE);
            $this->load->view('template', $data);
        }
        else {
            // set error message
            $this->session->set_flashdata('error_msg', "The link is invalid. Please generate the new link");
            redirect("auth/forget_password");
        }
    }

    // validate existing user access to change password
    public function validate_credentials() {
        $user_name    = $this->session->userdata('user_name');
        $result       = $this->user_model->login($user_name);
        $old_password = $this->input->post('old_password');

        if(!empty($result)) {
            $given_password = md5($old_password);

            if($given_password == $result->password) {
                return TRUE;
            }
            else {
                $this->form_validation->set_message('validate_credentials', 'Old password not match!');
                return FALSE;
            }
        }
        else {
            $this->form_validation->set_message('validate_credentials', 'User not found!');
            return FALSE;
        }
    }

}

/* End of file auth.php */
/* Location: ./application/controllers/auth.php */
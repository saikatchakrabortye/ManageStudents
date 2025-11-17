<?php
defined('BASEPATH') OR exit('No direct access allowed');
class Login extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('UserModel');
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->library('form_validation');

    }
    
    public function index() {
        /*protected function checkLogin(){
        /*If logged in, then no login again until signout done 
        if($this->session->userdata('loggedIn')){
            redirect('Dashboard');
        }
        }
        $this->checkLogin(); */
        $this->load->view('LoginView');
    }
    public function authenticate() {
        // Get email and password from POST data
        $email = $this->input->post('email');
        $password = $this->input->post('password');
        
        if($this->UserModel->authenticate($email, $password)){
		//set session data
        $user=$this->UserModel->getUserByEmail($email);
		$session_data=array(
		'email' => $user->email,
        'name' =>$user->name,
        'profilePic'=>$user->profile_pic,
		'loggedIn' => TRUE,
        'role'=>$this->UserModel->getRoleNameFromRoleId($user->role_id),
        'userId'=>$user->role_id);
	$this->session->set_userdata($session_data);
	redirect('Dashboard');
	} else{
		$this->session->set_flashdata('error', 'Invalid email or password');
            	redirect('Login');
	}
    }

    public function validateField() {
        $field = $this->input->post('field');
        $value = $this->input->post('value');
        
        $rules = [
            'name' => 'required|min_length[8]|max_length[30]|regex_match[/^[a-zA-Z]+( [a-zA-Z]+)*$/]',
            'email' => 'required|valid_email|max_length[100]',
            'phone' => 'required|regex_match[/^\+?[1-9]\d{1,14}$/]|min_length[10]|max_length[10]',
            'address' => 'required|min_length[10]|max_length[255]|regex_match[/^[a-zA-Z0-9\s\-\.,#]+$/]',
            'city' => 'required|min_length[2]|max_length[50]|regex_match[/^[a-zA-Z\s\-]+$/]',
            //'profile_pic' => 'uploaded[profile_pic]|max_size[profile_pic,5120]|is_image[profile_pic]|mime_in[profile_pic,image/jpeg,image/png,image/gif,image/webp]|ext_in[profile_pic,jpg,jpeg,png,gif,webp]',
            //'password' => 'min_length[8]|max_length[255]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/]',
            //'confirm_password' => 'matches[password]',
            'status' => 'required|in_list[active,alumni,inactive]'
        ];
        
        $this->form_validation->reset_validation();
        $this->form_validation->set_data([$field => $value]);
        
        if (isset($rules[$field])) {
            $this->form_validation->set_rules($field, $field, $rules[$field]);
        }
        
        if ($this->form_validation->run()) {
            echo json_encode([
                'success' => true,
                'message' => 'Valid!'
            ]);
        } else {
            $error_message = form_error($field);
            echo json_encode([
                'success' => false,
                'message' => $error_message
            ]);
        }
    }

    private function validateAllFields($post_data, $is_edit = false) {
        $all_valid = true;
        $this->form_validation->reset_validation();
        
        foreach ($post_data as $field => $value) {
            $rules = [
                'name' => 'required|min_length[8]|max_length[30]|regex_match[/^[a-zA-Z]+( [a-zA-Z]+)*$/]',
                'email' => 'required|valid_email|max_length[100]',
                'phone' => 'required|regex_match[/^\+?[1-9]\d{1,14}$/]|min_length[10]|max_length[10]',
                'address' => 'required|min_length[10]|max_length[255]|regex_match[/^[a-zA-Z0-9\s\-\.,#]+$/]',
                'city' => 'required|min_length[2]|max_length[50]|regex_match[/^[a-zA-Z\s\-]+$/]',
                //'password' => ($is_edit ? 'min_length[8]' : 'required|min_length[8]') . '|max_length[255]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/]',
                //'confirm_password' => 'matches[password]',
                'status' => 'required|in_list[active,alumni,inactive]'
            ];
            
            if (isset($rules[$field])) {
                $this->form_validation->set_data([$field => $value]);
                $this->form_validation->set_rules($field, $field, $rules[$field]);
                
                if (!$this->form_validation->run()) {
                    $all_valid = false;
                }
            }
        }
        
        return $all_valid;
    }

    
}
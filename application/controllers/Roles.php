<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Roles extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->library('session');
        $this->load->helper('url');
        $this->load->library('form_validation');
        // Check auth for ALL methods in this controller
        if (!$this->session->userdata('loggedIn') || !$this->session->userdata('userId')) {
            redirect('Login');
        }

        $this->load->model('RoleModel');
    }

    public function index() {
        if($this->session->userdata('userId') && $this->session->userdata('role') === 'Admin'){
        $this->load->view('RoleDashboard');}
        else{
            die("No Permissions");
        }
    }

   public function getRoles() {
        header('Content-Type: application/json');

        /****Code for without pagenation */
        /*$students = $this->StudentModel->getAllStudents();
        echo json_encode($students); // Converts PHP objects to JSON string*/
        /****Code for without pagenation ends here */
        
        /****Code for with pagenation starts here */
        // Get pagination parameters
        $page = $this->input->get('page') ?: 1;
        $limit = $this->input->get('limit') ?: 10;
        
        /**For Search Function, just add this below line */
        $search = $this->input->get('search') ?: ''; // NEW: Get search term from URL; Controller Step (1/2)
        /*Search Functionality specific code line ends here */

        $offset = ($page - 1) * $limit;
        
        // Fetch paginated data
        /**For Pagenation without search, use below code line */
        /*$students = $this->StudentModel->getPaginatedStudents($limit, $offset);*/
        /**Code line ends here, if not using search functionality */

        $roles = $this->RoleModel->getPaginatedRoles($limit, $offset, $search); // Pass search term if using search functionality; Controller Step (2/2)

        $total = $this->RoleModel->getTotalRoles($search);
        
        // Return both students and total count
        echo json_encode([
            'roles' => $roles,
            'total' => $total
        ]);
        /****Code for with pagenation ends here */
    }


    public function addRole() {
        header('Content-Type: application/json');
        
        // Validate fields and check result
        /*if (!$this->validateAllFields($this->input->post())) {
            // Get validation errors
            $errors = $this->form_validation->error_array();
            echo json_encode([
                'success' => false, 
                'message' => 'Validation failed', 
                'errors' => $errors
            ]);
            return;
        }*/
            if (!$this->validateAllFields($this->input->post())) {
            $errors = $this->form_validation->error_array();
            
            // Combine all errors into one message
            $errorMessages = implode(', ', $errors);
            
            echo json_encode([
                'success' => false, 
                'message' => 'Validation failed: ' . $errorMessages,
                'errors' => $errors
            ]);
            return;
        }

        $data = [
            'role_name' => $this->input->post('roleName'),
            'description' => $this->input->post('description')
        ];
        
        try {
        $this->RoleModel->addRole($data);
        echo json_encode(['success' => true, 'message' => 'Role added successfully']);
    } catch (Exception $e) {
        // Check if it's a duplicate entry error
        if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
            echo json_encode(['success' => false, 'message' => 'Role already exists']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error adding role: ' . $e->getMessage()]);
        }
    }
    }

    public function validateField() {
        $field = $this->input->post('field');
        $value = $this->input->post('value');
        
        $rules = [
            'name' => 'required|min_length[8]|max_length[30]|regex_match[/^[a-zA-Z]+( [a-zA-Z]+)*$/]',
            'roleName' => 'required|min_length[2]|max_length[30]|regex_match[/^[a-zA-Z\s]+$/]',
            'email' => 'required|valid_email|max_length[100]',
            'phone' => 'required|regex_match[/^\+?[1-9]\d{1,14}$/]|min_length[10]|max_length[10]',
            'address' => 'required|min_length[10]|max_length[255]|regex_match[/^[a-zA-Z0-9\s\-\.,#]+$/]',
            'description' => 'required|min_length[10]|max_length[255]|regex_match[/^[a-zA-Z0-9\s\-\.,#]+$/]',
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
            'roleName' => 'required|min_length[2]|max_length[30]|regex_match[/^[a-zA-Z\s]+$/]',
            'email' => 'required|valid_email|max_length[100]',
            'phone' => 'required|regex_match[/^\+?[1-9]\d{1,14}$/]|min_length[10]|max_length[10]',
            'address' => 'required|min_length[10]|max_length[255]|regex_match[/^[a-zA-Z0-9\s\-\.,#]+$/]',
            'description' => 'required|min_length[10]|max_length[255]|regex_match[/^[a-zA-Z0-9\s\-\.,#]+$/]',
            'city' => 'required|min_length[2]|max_length[50]|regex_match[/^[a-zA-Z\s\-]+$/]',
            //'profile_pic' => 'uploaded[profile_pic]|max_size[profile_pic,5120]|is_image[profile_pic]|mime_in[profile_pic,image/jpeg,image/png,image/gif,image/webp]|ext_in[profile_pic,jpg,jpeg,png,gif,webp]',
            //'password' => 'min_length[8]|max_length[255]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/]',
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
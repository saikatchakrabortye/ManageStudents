<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->library('session');
        $this->load->helper('url');
        $this->load->library('form_validation');
        // Check auth for ALL methods in this controller
        if (!$this->session->userdata('loggedIn') || !$this->session->userdata('userId')) {
            redirect('Login');
        }

        $this->load->model('UserModel');
    }

    public function index() {
        if($this->session->userdata('userId') && $this->session->userdata('role') === 'Admin'){
        $this->load->view('UserDashboard');}
        else{
            die("No Permissions");
        }
    }

   public function getUsers() {
        header('Content-Type: application/json');

        /****Code for without pagenation */
        /*$users = $this->UserModel->getAllUsers();
        echo json_encode($users); // Converts PHP objects to JSON string*/
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

        $users = $this->UserModel->getPaginatedUsers($limit, $offset, $search); // Pass search term if using search functionality; Controller Step (2/2)

        $total = $this->UserModel->getTotalUsers($search);
        
        // Return both students and total count
        echo json_encode([
            'users' => $users,
            'total' => $total
        ]);
        /****Code for with pagenation ends here */
    }

    public function getCities() {
        header('Content-Type: application/json');
        echo json_encode($this->UserModel->getCities());
    }

    public function addUser() {
        header('Content-Type: application/json');
        
        $profilePicFilename = $this->uploadFile();

        $data = [
            'name' => $this->input->post('name'),
            'email' => $this->input->post('email'),
            'phone' => $this->input->post('phone'),
            'address' => $this->input->post('address'),
            'city' => $this->input->post('city'),
            'dob' => $this->input->post('dob'),
            'password' => $this->input->post('password'),
            'status' => 'active',
            'role_id' => $this->input->post('roleId')
        ];
        // Add profile pic only if file was uploaded
        if ($profilePicFilename) {
            $data['profile_pic'] = $profilePicFilename;
        }

        try {
        $this->UserModel->addUsers($data);
        echo json_encode(['success' => true, 'message' => 'User added successfully']);
    } catch (Exception $e) {
        // Check if it's a duplicate entry error
        if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
            echo json_encode(['success' => false, 'message' => 'Email or phone number already exists']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error adding user: ' . $e->getMessage()]);
        }
    }
    }

    public function uploadFile() {
    // Check if profile_pic file is received
        if (empty($_FILES['profile_pic']['name']) || $_FILES['profile_pic']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        // Upload profile picture
        $base_dir = getcwd() . '/uploads/';
        $upload_path = $base_dir . 'profile_pics/users/';
        $file_extension = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
        $profile_filename = uniqid() . '.' . $file_extension;
        $profile_file_path = $upload_path . $profile_filename;

        if (!move_uploaded_file($_FILES['profile_pic']['tmp_name'], $profile_file_path)) {
            return null;
        }
        return $profile_filename;
    }
    
    public function getRoles() {
        header('Content-Type: application/json');
        
        // Assuming you have a method in your model to get roles
        $roles = $this->UserModel->getAllRoles();
        
        echo json_encode($roles);
    }

    public function getProfileData() {
        // Return user profile data as JSON
        header('Content-Type: application/json');
        
        if (!$this->session->userdata('loggedIn')) {
            echo json_encode(['error' => 'Not logged in']);
            return;
        }
        
        echo json_encode([
            'name' => $this->session->userdata('name'),
            'email' => $this->session->userdata('email'),
            'profilePic' => $this->session->userdata('profilePic'),
            'role' => $this->session->userdata('role')
        ]);
    }

        public function logout() {
        $this->session->sess_destroy();
        redirect('Login');
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
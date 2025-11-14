<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('UserModel');
    }

    public function index() {
        $this->load->view('UserDashboard');
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

        $this->UserModel->addUsers($data);
        
        echo json_encode(['success' => true, 'message' => 'User added successfully']);
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
}
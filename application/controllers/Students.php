<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Students extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('StudentModel');
    }

    public function index() {
        $this->load->view('StudentDashboard');
    }

   public function getStudents() {
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

        $students = $this->StudentModel->getPaginatedStudents($limit, $offset, $search); // Pass search term if using search functionality; Controller Step (2/2)

        $total = $this->StudentModel->getTotalStudents();
        
        // Return both students and total count
        echo json_encode([
            'students' => $students,
            'total' => $total
        ]);
        /****Code for with pagenation ends here */
    }

    public function getCities() {
        header('Content-Type: application/json');
        echo json_encode($this->StudentModel->getCities());
    }

    public function addStudent() {
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
            'status' => 'active'
        ];
        // Add profile pic only if file was uploaded
        if ($profilePicFilename) {
            $data['profile_pic_id'] = $profilePicFilename;
        }

        $this->StudentModel->addStudent($data);
        
        echo json_encode(['success' => true, 'message' => 'Student added successfully']);
    }

    public function uploadFile() {
    // Check if profile_pic file is received
        if (empty($_FILES['profile_pic']['name']) || $_FILES['profile_pic']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        // Upload profile picture
        $base_dir = getcwd() . '/uploads/';
        $upload_path = $base_dir . 'profile_pics/students/';
        $file_extension = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
        $profile_filename = uniqid() . '.' . $file_extension;
        $profile_file_path = $upload_path . $profile_filename;

        if (!move_uploaded_file($_FILES['profile_pic']['tmp_name'], $profile_file_path)) {
            return null;
        }
        return $profile_filename;
    }
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Students extends MY_Controller {
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

        $total = $this->StudentModel->getTotalStudents($search);
        
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
        //Using Centralized Validation Function
        $validation = $this->validate('addStudent');
        if (isset($validation['error'])) {
            // Handle error
            // Combine all errors into one message
            $errorMessages = implode(', ', $validation['error']);
            
            echo json_encode([
                'success' => false, 
                'message' => 'Validation failed: ' . $errorMessages,
                'errors' => $validation['error']
            ]);
            return;
        }

        //$profilePicFilename = $this->uploadFile();
        $fileValidation = $this->validateFile('profile_pic');
        if (isset($fileValidation['success'])) {
            $upload_path = FCPATH . 'uploads/profile_pics/students/';
            $file_path = $upload_path . $fileValidation['safe_filename']; // Use safe filename
            if ($this->compressAndSaveImage(
                $fileValidation['file_data']['tmp_name'],
                $fileValidation['mime_type'],
                $file_path
            )) {
                $profilePicFilename = $fileValidation['safe_filename'];
            }
        }


        $data = [
            //'name' => $this->input->post('name'),
            'name' => $validation['data']['name'], // Using validated data from $validation['data']; now raw user input
            'email' => $validation['data']['email'],
            'phone' => $validation['data']['phone'],
            'address' => $validation['data']['address'],
            'city' => $validation['data']['city'],
            'dob' => $validation['data']['dob'],
            'password' => $validation['data']['password'], // Hash password!
            'status' => 'active',
            'profile_pic_id' => $profilePicFilename ?? null // Use null if no file uploaded/processed
        ];

        try {
        $this->StudentModel->addStudent($data);
        echo json_encode(['success' => true, 'message' => 'Student added successfully']);
    } catch (Exception $e) {
        // Check if it's a duplicate entry error
        if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
            echo json_encode(['success' => false, 'message' => 'Email or phone number already exists']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error adding student: ' . $e->getMessage()]);
        }
    }
    }

    public function validateField() {

        $field = $this->input->post('field');
        $value = $this->input->post('value');
        // Use centralized validation rules instead of hardcoding
        $this->config->load('validationRules', TRUE);
        $rules = $this->config->item('addStudent', 'validationRules');
        
    $rulesAssoc = array_column($rules, null, 'field');

    if (!isset($rulesAssoc[$field])) {
        echo json_encode([
            'success' => false,
            'message' => 'Validation rule not found'
        ]);
        return;
    }
    
    $fieldRule = $rulesAssoc[$field];
    
    $this->form_validation->reset_validation();
    $this->form_validation->set_data([$field => $value]);

    // Set custom error messages if they exist
    if (isset($fieldRule['errors'])) {
        foreach ($fieldRule['errors'] as $rule => $message) {
            $this->form_validation->set_message($rule, $message);
        }
    }
    
    $this->form_validation->set_rules($field, $fieldRule['label'], $fieldRule['rules']);
    
    if ($this->form_validation->run()) {
        echo json_encode([
            'success' => true,
            'message' => 'Valid!'
        ]);
    } else {
        $error_message = form_error($field);
        // Clean the error message properly
        $clean_error = strip_tags($error_message);
        echo json_encode([
            'success' => false,
            'message' => $clean_error
        ]);
    }
    }
}
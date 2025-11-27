<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Students extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('StudentModel');
    }

    public function index() {

        if(!checkPermission('student.dashboard')) {
            die("No Permissions to Students dashboard");
            /*$this->session->set_flashdata('error', 'No Permissions to Students dashboard');
            redirect('Login'); // or redirect to previous page
            return;*/
        }
        // Pagenation with search
        // Get the inputs needed for pagenation
        
        $currentPage = $this->input->get('page') ?? 1; // if page exists in URL like /students?page=2 then get that value else default to page 1
        $search = $this->input->get('search') ?? ''; // NEW: Get search term from URL
        $recordsToShowPerPage = 5;
        $data['students'] = $this->StudentModel->getStudentsForPage($currentPage, $recordsToShowPerPage, $search); // search parameter added
        $data['currentPage'] = $currentPage; // we modify this currentPage variable to navigate through pages

        $totalRecords = $this->StudentModel->getTotalStudentsCount($search); // search  parameter added
        $totalPages = ceil($totalRecords / $recordsToShowPerPage);
        $data['totalPages'] = $totalPages;
        

        //$data['students']=$this->StudentModel->getAllStudentsData();
        $this->load->view('StudentDashboard', $data);
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
            'cityId' => $validation['data']['cityId'],
            'dob' => $validation['data']['dob'],
            'password' => password_hash($validation['data']['password'], PASSWORD_DEFAULT), // Hash password!
            'status' => 'active',
            'profilePic' => $profilePicFilename ?? null, // Use null if no file uploaded/processed
            'createdByUserId' => $this->session->userdata('userId') // Assuming userId is stored in session upon login
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

    public function updateStudent() {
        header('Content-Type: application/json');
         //Using Centralized Validation Function
        $validation = $this->validate('editStudent');
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

        $studentId = $this->input->post('studentId');
        $data = [
            'name' => $this->input->post('name'),
            'email' => $this->input->post('email'),
            'phone' => $this->input->post('phone'),
            'address' => $this->input->post('address'),
            'cityId' => $this->input->post('cityId'),
            'dob' => $this->input->post('dob'),
            'updatedByUserId' => $this->session->userdata('userId') // if you have user sessions
        ];
        
        // Handle password update only if provided
        if ($this->input->post('password')) {
            $data['password'] = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
        }
        
        // Handle profile picture upload
        /*if (!empty($_FILES['profile_pic']['name'])) {
            
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
            $upload = $this->uploadProfilePic();
            if ($profilePicFilename) {
                $data['profilePic'] = $profilePicFilename ?? null;
            }
        }*/
        
        $result = $this->StudentModel->updateStudent($studentId, $data);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Student updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update student']);
        }
    }

    public function deactivateStudent() {
            $studentId = $this->input->post('studentId');
            $status = $this->input->post('status'); // 'active' or 'inactive'
            $result = $this->StudentModel->deactivateStudent($studentId, $status);
            
            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
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
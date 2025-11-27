<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->model('UserModel');
    }

    public function index() {
        /*if($this->session->userdata('userId') && $this->session->userdata('role') === 'Admin'){
        $this->load->view('UserDashboard');}
        else{
            die("No Permissions");
        }*/
        

        // Pagenation with search
        // Get the inputs needed for pagenation
        
        $currentPage = $this->input->get('page') ?? 1; // if page exists in URL like /students?page=2 then get that value else default to page 1
        //$search = $this->input->get('search') ?? ''; // NEW: Get search term from URL
        $recordsToShowPerPage = 5;
        $data['users'] = $this->UserModel->getUsersForPage($currentPage, $recordsToShowPerPage); // search parameter added
        $data['currentPage'] = $currentPage; // we modify this currentPage variable to navigate through pages

        $totalRecords = $this->UserModel->getTotalUsersCount(); // search  parameter added
        $totalPages = ceil($totalRecords / $recordsToShowPerPage);
        $data['totalPages'] = $totalPages;
        

        //$data['students']=$this->StudentModel->getAllStudentsData();
        $this->load->view('UserDashboard', $data);
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
        
        //Using Centralized Validation Function
        $validation = $this->validate('addUser');
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
            $upload_path = FCPATH . 'uploads/profile_pics/users/';
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
            'roleId' => $validation['data']['roleId'],
            'profilePic' => $profilePicFilename ?? null // Use null if no file uploaded/processed
        ];
        

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

    public function updateUser() {
        header('Content-Type: application/json');
         //Using Centralized Validation Function
        $validation = $this->validate('editUser');
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

        $userId = $this->input->post('userId');
        $data = [
            'name' => $this->input->post('name'),
            'email' => $this->input->post('email'),
            'phone' => $this->input->post('phone'),
            'address' => $this->input->post('address'),
            'cityId' => $this->input->post('cityId'),
            'dob' => $this->input->post('dob')
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
        
        $result = $this->UserModel->updateUser($userId, $data);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'User updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update user']);
        }
    }

    public function deactivateUser() {
            $userId = $this->input->post('userId');
            $status = $this->input->post('status'); // 'active' or 'inactive'
            $result = $this->UserModel->deactivateUser($userId, $status);
            
            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
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
            'role' => $this->session->userdata('role'),
            'roleId' => $this->session->userdata('roleId')
        ]);
    }

        public function logout() {
        $this->session->sess_destroy();
        redirect('Login');
    }

    public function validateField() {
        header('Content-Type: application/json');
        $field = $this->input->post('field');
        $value = $this->input->post('value');

        // Use centralized validation rules instead of hardcoding
        $this->config->load('validationRules', TRUE);
        $rules = $this->config->item('addUser', 'validationRules');
        
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
    /*public function uploadFile() {
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
    }*/
    
}
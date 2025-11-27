<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Roles extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('RoleModel');
    }

    public function index() {
        /*if($this->session->userdata('userId') && $this->session->userdata('role') === 'Admin'){

        $this->load->view('RoleDashboard');}
        else{
            die("No Permissions");
        }*/
        $data['roles']=$this->RoleModel->getAllRolesData();
        $this->load->view('RoleDashboard', $data);
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
        
        //Using Centralized Validation Function
        $validation = $this->validate('addRole');
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

        $data = [
            'name' => $validation['data']['roleName'],
            'description' => $validation['data']['description']
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
        header('Content-Type: application/json');
        $field = $this->input->post('field');
        $value = $this->input->post('value');

        // Use centralized validation rules instead of hardcoding
        $this->config->load('validationRules', TRUE);
        $rules = $this->config->item('addRole', 'validationRules');
        
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
            $clean_error = strip_tags($error_message);
            echo json_encode([
                'success' => false,
                'message' => $clean_error
            ]);
        }
    }

    
}
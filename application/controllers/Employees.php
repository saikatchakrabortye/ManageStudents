<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employees extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('EmployeeModel');
        $this->load->model('DesignationModel');
        $this->load->model('EmployeeCtcModel');
    }

    public function index()
    {
        $data['employees'] = $this->EmployeeModel->getAllEmployees();
        $data['designations'] = $this->DesignationModel->getAllDesignations();
        //$this->load->view("EmployeeDashboard", $data);
        $this->renderWithSidebar('EmployeeDashboard', $data);
    }

    public function addEmployee() {
    header('Content-Type: application/json');
    
        // Using Centralized Validation Function
        $validation = $this->validate('addEmployee');
        if (isset($validation['error'])) {
            // Handle error - Combine all errors into one message
            $errorMessages = implode(', ', $validation['error']);
            
            echo json_encode([
                'success' => false, 
                'message' => 'Validation failed: ' . $errorMessages,
                'errors' => $validation['error']
            ]);
            return;
        }

        $data = [
            'name' => $validation['data']['name'], // Using validated data
            'gender' => $validation['data']['gender'],
            'dob' => $validation['data']['dob'],
            'phone' => $validation['data']['phone'],
            'email' => $validation['data']['email'],
            'password' => password_hash($validation['data']['password'], PASSWORD_DEFAULT),
            'designationId' => $validation['data']['designationId'],
            'joiningDate' => $validation['data']['joiningDate']
            //'createdByUserId' => $this->session->userdata('userId') // Assuming userId is stored in session upon login
        ];

        try {
            $employeeId = $this->EmployeeModel->addEmployee($data);
            // Get the created employee data

            $employee = $this->EmployeeModel->getEmployeeById($employeeId);
            echo json_encode(['success' => true, 'message' => 'Employee added successfully', 'employee' => [
                'id' => $employee->publicId,
                'name' => $employee->name,
                'createdAt' => $employee->createdAt
            ]
            ]);
        } catch (Exception $e) {
            // Check if it's a duplicate entry error
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                echo json_encode(['success' => false, 'message' => 'Email or phone number already exists']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error adding employee: ' . $e->getMessage()]);
            }
        }
    }

        public function getEmployeeById($id) {
        // Set content type to JSON
        header('Content-Type: application/json');
        
        try {
            // Get employee details from model
            $employee = $this->EmployeeModel->getEmployeeById($id);
            
            if ($employee) {
                // Return employee data as JSON
                echo json_encode([
                    'success' => true,
                    'id' => $employee->publicId,
                    'name' => $employee->name,
                    'gender' => $employee->gender,
                    'dob' => $employee->dob,
                    'phone' => $employee->phone,
                    'email' => $employee->email,
                    'joiningDate' => $employee->joiningDate,
                    'designationId' => $employee->designationId,
                    'designationName' => $employee->designationName, // Note: This is the designation name from join
                    'status' => $employee->status,
                    'createdAt' => date('d-m-Y H:i:s', strtotime($employee->createdAt)), //$employee->createdAt,
                    'updatedAt' => date('d-m-Y H:i:s', strtotime($employee->updatedAt)) //$employee->updatedAt
                ]);
            } else {
                // Employee not found
                echo json_encode([
                    'success' => false,
                    'message' => 'Employee not found'
                ]);
            }
        } catch (Exception $e) {
            // Handle any errors
            echo json_encode([
                'success' => false,
                'message' => 'Error fetching employee details: ' . $e->getMessage()
            ]);
        }
    }

    public function updateEmployee() {
    header('Content-Type: application/json');

    $employeePublicId = $this->input->post('employeeId');
    
    // Using Centralized Validation Function
    $validation = $this->validate('updateEmployee');
    if (isset($validation['error'])) {
        $errorMessages = implode(', ', $validation['error']);
        echo json_encode([
            'success' => false, 
            'message' => 'Validation failed: ' . $errorMessages,
            'errors' => $validation['error']
        ]);
        return;
    }

    // Manual uniqueness check for update (excluding current employee)
    $email = $validation['data']['email'];
    $phone = $validation['data']['phone'];
    
    // Check if email exists for other employees
    $this->db->where('email', $email);
    $this->db->where('publicId !=', $employeePublicId);
    $emailExists = $this->db->get('employees')->row();
    
    // Check if phone exists for other employees
    $this->db->where('phone', $phone);
    $this->db->where('publicId !=', $employeePublicId);
    $phoneExists = $this->db->get('employees')->row();
    
    if ($emailExists) {
        echo json_encode(['success' => false, 'message' => 'Email already exists for another employee']);
        return;
    }
    
    if ($phoneExists) {
        echo json_encode(['success' => false, 'message' => 'Phone number already exists for another employee']);
        return;
    }

    $data = [
        'name' => $validation['data']['name'],
        'gender' => $validation['data']['gender'],
        'dob' => $validation['data']['dob'],
        'phone' => $validation['data']['phone'],
        'email' => $validation['data']['email'],
        'joiningDate' => $validation['data']['joiningDate'],
        'designationId' => $validation['data']['designationId'],
        //'updatedByUserId' => $this->session->userdata('userId')
    ];

    try {
        $this->EmployeeModel->updateEmployee($employeePublicId, $data);
        echo json_encode(['success' => true, 'message' => 'Employee updated successfully']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error updating employee: ' . $e->getMessage()]);
    }
}

    public function setEmployeeStatus() {
        header('Content-Type: application/json');
        
        $employeeId = $this->input->post('employeeId');
        $status = $this->input->post('status');
        
        if (!$employeeId || !in_array($status, ['active', 'inactive'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
            return;
        }

        try {
            $this->EmployeeModel->deactivateEmployee($employeeId, $status);
            echo json_encode(['success' => true, 'message' => 'Employee status updated successfully']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error updating employee status: ' . $e->getMessage()]);
        }
    }
    
    public function getAllEmployeesForDropdown() {
        header('Content-Type: application/json');
        
        try {
            
            $employees = $this->EmployeeModel->getAllEmployeesForDropdown();
            
            // Format the response
            $formattedEmployees = array_map(function($employee) {
                return [
                    'id' => $employee->publicId, // Use publicId for display
                    'name' => $employee->name,
                    'designationName' => $employee->designationName,
                    'joiningDate' => $employee->joiningDate
                ];
            }, $employees);
            
            echo json_encode($formattedEmployees);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error fetching employees: ' . $e->getMessage()
            ]);
        }
    }

    // Add this to your existing Employees controller class

/**
 * Check if CTC record exists for an employee
 */
public function checkCtcRecordExists($employeePublicId)
{
    header('Content-Type: application/json');
    
    try {
        // Get the internal employee ID
        $employeeId = $this->EmployeeCtcModel->getEmployeeIdFromPublicId($employeePublicId);
        
        if (!$employeeId) {
            echo json_encode([
                'success' => false,
                'message' => 'Employee not found'
            ]);
            return;
        }
        
        $latestCtcRecord = $this->EmployeeCtcModel->getLatestCtcRecordOfEmployee($employeeId);
        
        if ($latestCtcRecord) {
            echo json_encode([
                'exists' => true,
                'id' => $latestCtcRecord->id,
                'effectiveStartDate' => $latestCtcRecord->effectiveStartDate,
                'ctcPerYear' => $latestCtcRecord->yearlyCtc,
                'createdAt' => $latestCtcRecord->createdAt,
                'updatedAt' => $latestCtcRecord->updatedAt
            ]);
        } else {
            echo json_encode([
                'exists' => false,
                'message' => 'No CTC record found for this employee'
            ]);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}

/**
 * Add new CTC record
 */
public function addCtc()
{
    header('Content-Type: application/json');
    
    $this->load->model('EmployeeCtcModel');
    
    $employeePublicId = $this->input->post('employeeId');
    $effectiveStartDate = $this->input->post('effectiveStartDate');
    $yearlyCtc = $this->input->post('yearlyCtc');
    
    // Basic validation
    if (!$employeePublicId || !$effectiveStartDate || !$yearlyCtc) {
        echo json_encode([
            'success' => false,
            'message' => 'All fields are required'
        ]);
        return;
    }
    
    // Get internal employee ID
    $employeeId = $this->EmployeeCtcModel->getEmployeeIdFromPublicId($employeePublicId);
    
    if (!$employeeId) {
        echo json_encode([
            'success' => false,
            'message' => 'Employee not found'
        ]);
        return;
    }
    
    // Check if CTC already exists for this employee
    /*$existingCtc = $this->EmployeeCtcModel->getEmployeeCtc($employeePublicId);
    if ($existingCtc) {
        echo json_encode([
            'success' => false,
            'message' => 'CTC record already exists for this employee. Please update instead.'
        ]);
        return;
    }*/
    

    $joiningDateOfEmployee = $this->EmployeeModel->getEmployeeById($employeeId)->joiningDate;
    if(strtotime($effectiveStartDate) < strtotime($joiningDateOfEmployee)) {
        echo json_encode([
            'success' => false,
            'message' => 'Effective Start Date cannot be earlier than Employee Joining Date: ' . $joiningDateOfEmployee
        ]);
        return;
    }
    
    $data = [
        'employeeId' => $employeeId,
        'effectiveStartDate' => $effectiveStartDate,
        'yearlyCtc' => $yearlyCtc
    ];
    
    try {
        $result = $this->EmployeeCtcModel->addEmployeeCtc($data);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'CTC added successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to add CTC'
            ]);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

/**
 * Update existing CTC record
 */
public function updateCtc()
{
    header('Content-Type: application/json');
    
    $this->load->model('EmployeeCtcModel');
    
    $ctcId = $this->input->post('ctcId');
    $employeePublicId = $this->input->post('employeeId');
    $effectiveStartDate = $this->input->post('effectiveStartDate');
    $yearlyCtc = $this->input->post('yearlyCtc');
    
    // Basic validation
    if (!$ctcId || !$employeePublicId || !$effectiveStartDate || !$yearlyCtc) {
        echo json_encode([
            'success' => false,
            'message' => 'All fields are required'
        ]);
        return;
    }
    
    // Verify the CTC record exists and belongs to this employee
    $ctcRecord = $this->EmployeeCtcModel->getCtcById($ctcId);
    if (!$ctcRecord) {
        echo json_encode([
            'success' => false,
            'message' => 'CTC record not found'
        ]);
        return;
    }
    
    // Get internal employee ID
    $employeeId = $this->EmployeeCtcModel->getEmployeeIdFromPublicId($employeePublicId);
    
    if ($ctcRecord->employeeId != $employeeId) {
        echo json_encode([
            'success' => false,
            'message' => 'CTC record does not belong to this employee'
        ]);
        return;
    }

    $joiningDateOfEmployee = $this->EmployeeModel->getEmployeeById($employeeId)->joiningDate;
    if(strtotime($effectiveStartDate) < strtotime($joiningDateOfEmployee)) {
        echo json_encode([
            'success' => false,
            'message' => 'Effective Start Date cannot be earlier than Employee Joining Date: ' . $joiningDateOfEmployee
        ]);
        return;
    }
    
    $data = [
        'effectiveStartDate' => $effectiveStartDate,
        'yearlyCtc' => $yearlyCtc,
        'employeeId' => $employeeId
    ];
    
    try {
        $result = $this->EmployeeCtcModel->updateEmployeeCtc($ctcId, $data);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'CTC updated successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update CTC'
            ]);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

public function getCtcRevisionsForEmployee($employeePublicId)
{
    header('Content-Type: application/json');
    
    // Get internal employee ID
    $employeeId = $this->EmployeeCtcModel->getEmployeeIdFromPublicId($employeePublicId);
    
    if (!$employeeId) {
        echo json_encode([
            'success' => false,
            'message' => 'Employee not found'
        ]);
        return;
    }
    
    $result = $this->EmployeeCtcModel->getAllCtcRecordOfEmployee($employeeId);
    echo json_encode($result);
}
}

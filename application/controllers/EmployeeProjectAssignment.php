<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EmployeeProjectAssignment extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('EmployeeProjectAssignmentModel');
        $this->load->model('EmployeeModel');
        $this->load->model('ProjectModel');
    }
    
    /**
     * Display the employee project assignment page
     */
    public function index() {
        $this->renderWithSidebar('EmployeeProjectAssignmentList');
    }
    
    /**
     * Get all employees for dropdown
     */
    public function getAllEmployeesForDropdown() {
        try {
            $employees = $this->EmployeeProjectAssignmentModel->getAllEmployeesForDropdown();
            
            echo json_encode([
                'success' => true,
                'employees' => $employees
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get all projects for dropdown
     */
    public function getAllProjectsForDropdown() {
        try {
            $projects = $this->EmployeeProjectAssignmentModel->getAllProjectsForDropdown();
            
            echo json_encode([
                'success' => true,
                'projects' => $projects
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get assignments for a specific employee
     */
    public function getEmployeeAssignments() {
        try {
            if ($this->session->userdata('designationId') != 10) // for non admin
            {
                $employeePublicId = $this->session->userdata('employeePublicId');
            }
            else{
                $employeePublicId = $this->input->post('employeePublicId');
            }
            
            
            if (empty($employeePublicId)) {
                throw new Exception("Employee ID is required");
            }
            
            $assignments = $this->EmployeeProjectAssignmentModel->getAssignmentsByEmployee($employeePublicId);
            
            // Format dates
            foreach ($assignments as $assignment) {
                $assignment->assignedFrom = date('d-m-Y', strtotime($assignment->assignedFrom));
            }
            
            echo json_encode([
                'success' => true,
                'assignments' => $assignments
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Assign project to employee
     */
    public function assignProjectToEmployee() {
        try {
            $employeePublicId = $this->input->post('employeeId');
            $projectPublicId = $this->input->post('projectId');
            $assignFrom = $this->input->post('assignFrom');
            
            // Validate inputs
            if (empty($employeePublicId) || empty($projectPublicId) || empty($assignFrom)) {
                throw new Exception("All fields are required");
            }
            
            // Get IDs from public IDs
            $employeeId = $this->EmployeeProjectAssignmentModel->getEmployeeIdFromPublicId($employeePublicId);
            $projectId = $this->EmployeeProjectAssignmentModel->getProjectIdFromPublicId($projectPublicId);
            
            if (!$employeeId) {
                throw new Exception("Invalid employee selected");
            }
            
            if (!$projectId) {
                throw new Exception("Invalid project selected");
            }

            $project = $this->ProjectModel->getProjectById($projectId);
            $employee = $this->EmployeeModel->getEmployeeById($employeeId);

            if ($assignFrom < $project->startDate || $assignFrom < $employee->joiningDate) {
                throw new Exception("Assignment date cannot be before project start date or employee joining date");
            }
            
            // Prepare data for insertion
            $data = [
                'projectId' => $projectId,
                'employeeId' => $employeeId,
                'assignedFrom' => $assignFrom,
                'status' => 'active'
            ];
            
            // Assign project to employee
            $result = $this->EmployeeProjectAssignmentModel->assignProjectToEmployee($data);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Project assigned to employee successfully'
                ]);
            } else {
                throw new Exception("Failed to assign project to employee");
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Toggle assignment status
     */
    public function toggleAssignmentStatus() {
        try {
            $assignmentId = $this->input->post('assignmentId');
            $status = $this->input->post('status');
            
            if (empty($assignmentId) || empty($status)) {
                throw new Exception("Missing required parameters");
            }
            
            $result = $this->EmployeeProjectAssignmentModel->toggleAssignmentStatus($assignmentId, $status);
            
            if ($result) {
                $message = $status === 'active' 
                    ? 'Assignment activated successfully' 
                    : 'Assignment deactivated successfully';
                
                echo json_encode([
                    'success' => true,
                    'message' => $message
                ]);
            } else {
                throw new Exception("Failed to update assignment status");
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
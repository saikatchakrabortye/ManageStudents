<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProjectAssignment extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('ProjectAssignmentModel');
        $this->load->model('ProjectModel');
        $this->load->model('EmployeeModel');
    }
    
    /**
     * Display the project assignment page
     */
    public function index() {
        $this->renderWithSidebar('ProjectAssignmentList');
    }
    
    /**
     * Get all projects for dropdown
     */
    public function getAllProjectsForDropdown() {
        try {
            $projects = $this->ProjectAssignmentModel->getAllProjectsForDropdown();
            
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
     * Get all employees for dropdown
     */
    public function getAllEmployeesForDropdown() {
        try {
            $employees = $this->ProjectAssignmentModel->getAllEmployeesForDropdown();
            
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
     * Get assignments for a specific project
     */
    public function getProjectAssignments() {
        try {
            $projectPublicId = $this->input->post('projectPublicId');
            
            if (empty($projectPublicId)) {
                throw new Exception("Project ID is required");
            }
            
            $assignments = $this->ProjectAssignmentModel->getAssignmentsByProject($projectPublicId);
            
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
     * Assign employee to project
     */
    public function assignEmployeeToProject() {
        try {
            $projectPublicId = $this->input->post('projectId');
            $employeePublicId = $this->input->post('employeeId');
            $assignFrom = $this->input->post('assignFrom');
            
            // Validate inputs
            if (empty($projectPublicId) || empty($employeePublicId) || empty($assignFrom)) {
                throw new Exception("All fields are required");
            }
            
            // Get IDs from public IDs
            $projectId = $this->ProjectAssignmentModel->getProjectIdFromPublicId($projectPublicId);
            $employeeId = $this->ProjectAssignmentModel->getEmployeeIdFromPublicId($employeePublicId);
            
            if (!$projectId) {
                throw new Exception("Invalid project selected");
            }
            
            if (!$employeeId) {
                throw new Exception("Invalid employee selected");
            }

            $project = $this->ProjectModel->getProjectById($projectId);
            $employee = $this->EmployeeModel->getEmployeeById($employeeId);

            if ($assignFrom < $project->startDate || $assignFrom < $employee->joiningDate) {
                throw new Exception("Assignment date cannot be before project start date or employee joining date");
            }
            
            // Check if employee is already actively assigned
            /*if ($this->ProjectAssignmentModel->isEmployeeAlreadyAssigned($projectId, $employeeId)) {
                throw new Exception("Employee is already assigned to this project");
            }*/
            
            // Prepare data for insertion
            $data = [
                'projectId' => $projectId,
                'employeeId' => $employeeId,
                'assignedFrom' => $assignFrom,
                'status' => 'active'
            ];
            
            // Assign employee to project
            $result = $this->ProjectAssignmentModel->assignEmployeeToProject($data);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Employee assigned to project successfully'
                ]);
            } else {
                throw new Exception("Failed to assign employee to project");
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
            
            $result = $this->ProjectAssignmentModel->toggleAssignmentStatus($assignmentId, $status);
            
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
    
    /**
     * Remove assignment
     */
    public function removeAssignment() {
        try {
            $assignmentId = $this->input->post('assignmentId');
            
            if (empty($assignmentId)) {
                throw new Exception("Assignment ID is required");
            }
            
            $result = $this->ProjectAssignmentModel->removeAssignment($assignmentId);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Assignment removed successfully'
                ]);
            } else {
                throw new Exception("Failed to remove assignment");
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
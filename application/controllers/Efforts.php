<?php
defined ('BASEPATH') or exit('No direct access allowed');

class Efforts extends MY_Controller
{
    public function __construct() {
        parent:: __construct();
        $this->load->model('EffortModel');
        $this->load->model('EmployeeProjectAssignmentModel');
    }

    public function index()
    {
        $employeeId = $this->session->userdata('employeeId');
    $designationId = $this->session->userdata('designationId');
    
    // Get filter values from GET request
    $fromDate = $this->input->get('fromDate');
    $toDate = $this->input->get('toDate');
    $filterEmployeeId = $this->input->get('employeeId');
    
    // Set default dates to today if not provided
    if (empty($fromDate)) {
        $fromDate = date('Y-m-d');
    }
    if (empty($toDate)) {
        $toDate = date('Y-m-d');
    }
    
    // Validate date range
    if (strtotime($fromDate) > strtotime($toDate)) {
        $data['error'] = 'From date cannot be greater than To date';
        $fromDate = $toDate = date('Y-m-d'); // Reset to today
    }
    
    // For non-admin users
    if ($designationId != 10) {
        // Non-admin can only see their own efforts
        $data['efforts'] = $this->EffortModel->getFilteredEffortsForEmployee($employeeId, $fromDate, $toDate) ?? [];
        $data['totalHoursWorked'] = $this->EffortModel->getTotalHoursWorkedForEmployee($employeeId);
    } else {
        // Admin users
        if (!empty($filterEmployeeId)) {
            // Filter by specific employee
            $data['efforts'] = $this->EffortModel->getFilteredEffortsForEmployee($filterEmployeeId, $fromDate, $toDate) ?? [];
            $data['totalHoursWorked'] = $this->EffortModel->getTotalHoursWorkedForEmployee($filterEmployeeId);
        } else {
            // Show all employees' efforts
            $data['efforts'] = $this->EffortModel->getAllFilteredEfforts($fromDate, $toDate) ?? [];
            $data['totalHoursWorked'] = null; // Cannot calculate total for all employees
        }
        
        // Load employee model for dropdown
        $this->load->model('EmployeeModel');
        $data['employees'] = $this->EmployeeModel->getAllEmployeesForDropdown();
    }
    
    // Pass filter values back to view
    $data['fromDate'] = $fromDate;
    $data['toDate'] = $toDate;
    $data['filterEmployeeId'] = $filterEmployeeId;
    $data['designationId'] = $designationId;
    
    $this->renderWithSideBar('EffortsView', $data);
    }

        /**
     * Add new effort
     */
    public function addEffort() {
        header('Content-Type: application/json');
    
        // Get employee ID from session
        $employeeId = $this->session->userdata('employeeId');
        $date = $this->input->post('date');
        $effortDuration = $this->input->post('effortDuration');
        $projectPublicId = $this->input->post('projectId');

        // Validate inputs
        if (empty($date) || empty($effortDuration) || empty($projectPublicId)) {
            echo json_encode([
                'success' => false, 
                'message' => 'All fields are required'
            ]);
            return;
        }

        try {
            // Get project ID from public ID
            $this->load->model('EmployeeProjectAssignmentModel');
            $projectId = $this->EmployeeProjectAssignmentModel->getProjectIdFromPublicId($projectPublicId);
            
            if (!$projectId) {
                throw new Exception("Invalid project selected");
            }

            // Prepare data
            $data = [
                'projectId' => $projectId,
                'employeeId' => $employeeId,
                'effortDate' => $date,
                'duration' => $effortDuration
            ];

            // Add effort
            $effortPublicId = $this->EffortModel->addEffort($data);
            
            // Get the created effort data
            $effort = $this->EffortModel->getEffortByPublicId($effortPublicId);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Effort added successfully', 
                'effort' => [
                    'publicId' => $effort->publicId,
                    'effortDate' => $effort->effortDate,
                    'duration' => $effort->duration,
                    'projectName' => $effort->projectName,
                    'createdAt' => $effort->createdAt
                ]
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false, 
                'message' => 'Error adding effort: ' . $e->getMessage()
            ]);
        }
    }

    public function updateEffortDuration() {
    header('Content-Type: application/json');
    
    // Only admin can update
    $designationId = $this->session->userdata('designationId');
    if ($designationId != 10) {
        echo json_encode([
            'success' => false, 
            'message' => 'Only admin can update effort duration'
        ]);
        return;
    }
    
    $effortPublicId = $this->input->post('effortPublicId');
    $effortDuration = $this->input->post('effortDuration');
    
    // Validate inputs
    if (empty($effortPublicId) || empty($effortDuration)) {
        echo json_encode([
            'success' => false, 
            'message' => 'Effort ID and duration are required'
        ]);
        return;
    }
    
    try {
        // Update effort duration
        $result = $this->EffortModel->updateEffortDuration($effortPublicId, $effortDuration);
        
        if ($result) {
            // Get updated effort data
            $effort = $this->EffortModel->getEffortByPublicId($effortPublicId);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Effort duration updated successfully', 
                'effort' => [
                    'publicId' => $effort->publicId,
                    'effortDate' => $effort->effortDate,
                    'duration' => $effort->duration,
                    'projectName' => $effort->projectName
                ]
            ]);
        } else {
            throw new Exception("Failed to update effort duration");
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false, 
            'message' => 'Error updating effort: ' . $e->getMessage()
        ]);
    }
}
}
<?php
defined ('BASEPATH') or exit('No direct access allowed');

class Efforts extends MY_Controller
{
    public function __construct() {
        parent:: __construct();
        $this->load->model('EffortModel');
        $this->load->model('EmployeeProjectAssignmentModel');
        $this->load->model('ProjectModel');
        $this->load->model('EmployeeModel');
    }

    public function index()
    {
        $employeeId = $this->session->userdata('employeeId');
        $designationId = $this->session->userdata('designationId');
        
        // Get filter values from GET request
        $fromDate = $this->input->get('fromDate');
        $toDate = $this->input->get('toDate');
        $filterEmployeeId = $this->input->get('employeeId');
        $filterProjectId = $this->input->get('projectId'); // New project filter
        
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
            $data['efforts'] = $this->EffortModel->getFilteredEffortsForEmployee($employeeId, $fromDate, $toDate, $filterProjectId) ?? [];
            
            // Load assigned projects for non-admin
            $data['projects'] = $this->EmployeeProjectAssignmentModel->getAssignedProjectsForEmployee($employeeId);
        } else {
            // Admin users
            if (!empty($filterEmployeeId)) {
                // Filter by specific employee
                $data['efforts'] = $this->EffortModel->getFilteredEffortsForEmployee($filterEmployeeId, $fromDate, $toDate, $filterProjectId) ?? [];
            } else {
                // Show all employees' efforts
                $data['efforts'] = $this->EffortModel->getAllFilteredEfforts($fromDate, $toDate, $filterProjectId) ?? [];
            }
            
            // Load employee model for dropdown
            $data['employees'] = $this->EmployeeModel->getAllEmployeesForDropdown();
            
            // Load all projects for admin
            $data['projects'] = $this->ProjectModel->getAllProjects();
        }
        
        // Calculate total hours for displayed efforts
        $data['totalHoursWorked'] = $this->calculateTotalHoursForEfforts($data['efforts']);
        
        // Pass filter values back to view
        $data['fromDate'] = $fromDate;
        $data['toDate'] = $toDate;
        $data['filterEmployeeId'] = $filterEmployeeId;
        $data['filterProjectId'] = $filterProjectId;
        $data['designationId'] = $designationId;
        
        $this->renderWithSideBar('EffortsView', $data);
    }
    
    /**
     * Calculate total hours for a list of efforts
     */
    private function calculateTotalHoursForEfforts($efforts)
    {
        $totalMinutes = 0;
        
        foreach ($efforts as $effort) {
            // Split duration (format: HH:MM or HH:MM:SS)
            $durationParts = explode(':', $effort->duration);
            $hours = intval($durationParts[0]);
            $minutes = isset($durationParts[1]) ? intval($durationParts[1]) : 0;
            
            $totalMinutes += ($hours * 60) + $minutes;
        }
        
        // Convert total minutes to hours:minutes format
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        
        return sprintf('%d:%02d', $hours, $minutes);
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
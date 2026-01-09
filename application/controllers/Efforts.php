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
        
        // Convert total minutes to proper hours:minutes format
        // Handle 95 minutes -> 1 hour 35 minutes conversion
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        
        // Format as "3h/45m"
        return sprintf('%dh/%02dm', $hours, $minutes);
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

            // Get project details to validate date
            $project = $this->ProjectModel->getProjectById($projectId);
            
            if (!$project) {
                throw new Exception("Project not found");
            }

            // Validate effort date against project dates
            $this->validateEffortDateAgainstProject($date, $project);

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
            
            // Convert duration to "3h/45m" format
            $durationParts = explode(':', $effort->duration);
            $hours = intval($durationParts[0]);
            $minutes = isset($durationParts[1]) ? intval($durationParts[1]) : 0;
            $formattedDuration = sprintf('%dh/%02dm', $hours, $minutes);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Effort added successfully', 
                'effort' => [
                    'publicId' => $effort->publicId,
                    'effortDate' => $effort->effortDate,
                    'duration' => $formattedDuration,
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
            // Get the effort to check its date
            $effort = $this->EffortModel->getEffortByPublicId($effortPublicId);
            
            if (!$effort) {
                throw new Exception("Effort not found");
            }
            
            // Get project details to validate date
            $project = $this->ProjectModel->getProjectById($effort->projectId);
            
            if ($project) {
                // Validate effort date against project dates
                $this->validateEffortDateAgainstProject($effort->effortDate, $project);
            }
            
            // Update effort duration
            $result = $this->EffortModel->updateEffortDuration($effortPublicId, $effortDuration);
            
            if ($result) {
                // Get updated effort data
                $effort = $this->EffortModel->getEffortByPublicId($effortPublicId);
                
                // Convert duration to "3h/45m" format
                $durationParts = explode(':', $effort->duration);
                $hours = intval($durationParts[0]);
                $minutes = isset($durationParts[1]) ? intval($durationParts[1]) : 0;
                $formattedDuration = sprintf('%dh/%02dm', $hours, $minutes);
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Effort duration updated successfully', 
                    'effort' => [
                        'publicId' => $effort->publicId,
                        'effortDate' => $effort->effortDate,
                        'duration' => $formattedDuration,
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
    
    /**
     * Convert duration to proper format (32hr 95min becomes 33hr 35min)
     * This handles minutes overflow
     */
    private function convertDuration($hours, $minutes) {
        // Handle minute overflow (e.g., 95 minutes = 1 hour 35 minutes)
        $totalMinutes = ($hours * 60) + $minutes;
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        
        return sprintf('%dh/%02dm', $hours, $minutes);
    }
    
    /**
     * Validate effort date against project start and end dates
     * 
     * @param string $effortDate The effort date to validate
     * @param object $project The project object containing startDate and endDate
     * @throws Exception If effort date is invalid relative to project dates
     */
    private function validateEffortDateAgainstProject($effortDate, $project) {
        // Check if project has a start date
        if (!empty($project->startDate)) {
            // Validate effort date is on or after project start date
            $effortDateTime = new DateTime($effortDate);
            $projectStartDate = new DateTime($project->startDate);
            
            if ($effortDateTime < $projectStartDate) {
                $formattedStartDate = $projectStartDate->format('Y-m-d');
                throw new Exception("Effort date cannot be before project start date ($formattedStartDate)");
            }
            
            // Optional: Also check if effort date is before project end date (if end date exists)
            if (!empty($project->endDate)) {
                $projectEndDate = new DateTime($project->endDate);
                if ($effortDateTime > $projectEndDate) {
                    $formattedEndDate = $projectEndDate->format('Y-m-d');
                    throw new Exception("Effort date cannot be after project end date ($formattedEndDate)");
                }
            }
        }
    }
}
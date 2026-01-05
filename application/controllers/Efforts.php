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
        if ($designationId != 10){
        $data['efforts'] = $this->EffortModel->getAllEffortsForEmployeeId($employeeId) ?? []; // returns an empty array when result is null; i.e no result found. Else gives error
        } else {
            $data['efforts'] = $this->EffortModel->getAllEfforts();
        }
        $data['totalHoursWorked'] = $this->EffortModel->getTotalHoursWorkedForEmployee($employeeId);
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
}
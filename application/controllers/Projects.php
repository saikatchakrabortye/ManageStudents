<?php
defined('BASEPATH') OR exit("No direct access allowed");
class Projects extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('ProjectModel');
        $this->load->model('ClientModel');
    }

    public function index() {
        $data['projects'] = $this->ProjectModel->getAllProjectsForlisting();
        $data['clients'] = $this->ClientModel->getAllClientsForDropdown();
        //$this->load->view('ProjectDashboard', $data);
        $this->renderWithSidebar('ProjectDashboard', $data);
    }

    public function getAllClientsForDropdown() {
        header('Content-Type: application/json');
        $clients = $this->ClientModel->getAllClientsForDropdown();
        echo json_encode(['success' => true, 'clients' => $clients]);
    }

    public function addProject() {
        header('Content-Type: application/json');
    
        // Using Centralized Validation Function
        $validation = $this->validate('addProject');
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
        $internalIdObject = $this->ClientModel->getIdFromPublicId($validation['data']['clientId']);
        $data = [
            'name' => $validation['data']['name'], // Using validated data
            'clientId' => $internalIdObject->id,
            'startDate' => $validation['data']['startDate']    
        ];

        try {
            $projectPublicId = $this->ProjectModel->addProject($data);
            // Get the created project data

            $project = $this->ProjectModel->getProjectByPublicId($projectPublicId);
            echo json_encode(['success' => true, 'message' => 'Project added successfully', 'project' => [
                'publicId' => $project->publicId,
                'name' => $project->name,
                'clientId' => $project->clientId,
                'startDate' => $project->startDate,
                'createdAt' => $project->createdAt
            ]
            ]);
        } catch (Exception $e) {
            // Check if it's a duplicate entry error
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                echo json_encode(['success' => false, 'message' => 'Project name already exists']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error adding project: ' . $e->getMessage()]);
            }
        }
    }

    public function toggleProjectStatus() {
        header('Content-Type: application/json');
        
        $publicId = $this->input->post('publicId');
        $status = $this->input->post('status');
        
        if (!$publicId || !in_array($status, ['active', 'inactive'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
            return;
        }

        try {
            $this->ProjectModel->toggleProjectStatus($publicId, $status);
            echo json_encode(['success' => true, 'message' => 'Project status updated successfully']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error updating project status: ' . $e->getMessage()]);
        }
    }

    public function getProjectByPublicId() {
        header('Content-Type: application/json');
        
        //$publicId = $this->input->post('publicId');

        // Get raw JSON input.
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $publicId = $data['publicId'] ?? null;

        if ($publicId) {
            $project = $this->ProjectModel->getProjectByPublicId($publicId);
            if ($project) {
                echo json_encode(['success' => true, 'project' => $project]);
                exit;
            } else {
                echo json_encode(['success' => false, 'message' => 'Project not found']);
                exit;
            }
        }
        echo json_encode(['success' => false, 'message' => 'Missing project ID']);
    exit;
    }

    public function updateProject() {
        header('Content-Type: application/json');
    
        // Using Centralized Validation Function
        $validation = $this->validate('updateProject');
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
        //$internalIdObject = $this->ClientModel->getIdFromPublicId($validation['data']['clientId']);
        $data = [
            'name' => $validation['data']['name'], // Using validated data
            //'clientId' => $internalIdObject->id,
            'startDate' => $validation['data']['startDate']    
        ];

        try {
            $this->ProjectModel->updateProject($validation['data']['publicId'], $data);
            echo json_encode(['success' => true, 'message' => 'Project updated successfully']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error updating project: ' . $e->getMessage()]);
        }
    }
}
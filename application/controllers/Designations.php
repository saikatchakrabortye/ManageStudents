<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Designations extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('DesignationModel');
    }

    public function index() {
        $data['designations'] = $this->DesignationModel->getAllDesignations();
        $this->load->view("DesignationDashboard", $data);
    }

    public function addDesignation() {
        header('Content-Type: application/json');
        
        // Using Centralized Validation Function
        $validation = $this->validate('addDesignation');
        if (isset($validation['error'])) {
            $errorMessages = implode(', ', $validation['error']);
            
            echo json_encode([
                'success' => false, 
                'message' => 'Validation failed: ' . $errorMessages,
                'errors' => $validation['error']
            ]);
            return;
        }

        $data = [
            'name' => $validation['data']['name'],
            //'status' => $validation['data']['status']
            //'createdByUserId' => $this->session->userdata('userId')
        ];

        try {
            $designationId = $this->DesignationModel->addDesignation($data);
            
            // Get the created designation data
            $designation = $this->DesignationModel->getDesignationById($designationId);
            echo json_encode([
                'success' => true, 
                'message' => 'Designation added successfully', 
                'designation' => [
                    'id' => $designation->id,
                    'name' => $designation->name,
                    'status' => $designation->status,
                    'createdAt' => $designation->createdAt
                ]
            ]);
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate Designation entry') !== false) {
                echo json_encode(['success' => false, 'message' => 'Designation name already exists']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error adding designation: ' . $e->getMessage()]);
            }
        }
    }

    public function getDesignationById($id) {
        header('Content-Type: application/json');
        
        try {
            $designation = $this->DesignationModel->getDesignationById($id);
            
            if ($designation) {
                echo json_encode([
                    'success' => true,
                    'id' => $designation->id,
                    'name' => $designation->name,
                    'status' => $designation->status,
                    'createdAt' => date('d-m-Y H:i:s', strtotime($designation->createdAt)), //$designation->createdAt,
                    'updatedAt' => date('d-m-Y H:i:s', strtotime($designation->updatedAt)) //$designation->updatedAt
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Designation not found'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error fetching designation details: ' . $e->getMessage()
            ]);
        }
    }

    public function updateDesignation() {
        header('Content-Type: application/json');

        $designationId = $this->input->post('designationId');
        
        // Using Centralized Validation Function
        $validation = $this->validate('updateDesignation');
        if (isset($validation['error'])) {
            $errorMessages = implode(', ', $validation['error']);
            echo json_encode([
                'success' => false, 
                'message' => 'Validation failed: ' . $errorMessages,
                'errors' => $validation['error']
            ]);
            return;
        }

        // Manual uniqueness check for update (excluding current designation)
        $name = $validation['data']['name'];
        
        $this->db->where('name', $name);
        $this->db->where('id !=', $designationId);
        $nameExists = $this->db->get('designations')->row();
        
        if ($nameExists) {
            echo json_encode([
                'success' => false, 
                'message' => 'Designation name already exists for another designation'
            ]);
            return;
        }

        $data = [
            'name' => $validation['data']['name'],
            'status' => $validation['data']['status']
            //'updatedByUserId' => $this->session->userdata('userId')
        ];

        try {
            $this->DesignationModel->updateDesignation($designationId, $data);
            echo json_encode([
                'success' => true, 
                'message' => 'Designation updated successfully'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false, 
                'message' => 'Error updating designation: ' . $e->getMessage()
            ]);
        }
    }

    public function setDesignationStatus() {
        header('Content-Type: application/json');
        
        $designationId = $this->input->post('designationId');
        $status = $this->input->post('status');
        
        if (!$designationId || !in_array($status, ['active', 'inactive'])) {
            echo json_encode([
                'success' => false, 
                'message' => 'Invalid parameters'
            ]);
            return;
        }

        try {
            $this->DesignationModel->deactivateDesignation($designationId, $status);
            echo json_encode([
                'success' => true, 
                'message' => 'Designation status updated successfully'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false, 
                'message' => 'Error updating designation status: ' . $e->getMessage()
            ]);
        }
    }
}
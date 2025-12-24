<?php
defined('BASEPATH') OR exit("No direct access allowed");
class Clients extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('ClientModel');
    }

    public function index() {
        $data['clients'] = $this->ClientModel->getAllClientsForListing();
        //$this->load->view('ClientDashboard', $data);
        $this->renderWithSidebar('ClientDashboard', $data);
    }

    public function addClient() {
        header('Content-Type: application/json');
    
        // Using Centralized Validation Function
        $validation = $this->validate('addClient');
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
            'name' => $validation['data']['name'] // Using validated data
        ];

        try {
            // Get the created client data

            $client = $this->ClientModel->addClient($data);
            
            echo json_encode(['success' => true, 'message' => 'Client added successfully', 'client' => [
                'publicId' => $client->publicId,
                'name' => $client->name,
                'createdAt' => $client->createdAt
            ]
            ]);
        } catch (Exception $e) {
            // Check if it's a duplicate entry error
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                echo json_encode(['success' => false, 'message' => 'Client name already exists']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error adding client: ' . $e->getMessage()]);
            }
        }
    }
    public function toggleClientStatus() {
        header('Content-Type: application/json');
        
        $publicId = $this->input->post('publicId');
        $status = $this->input->post('status');
        
        if (!$publicId || !in_array($status, ['active', 'inactive'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
            return;
        }

        try {
            $this->ClientModel->toggleClientStatus($publicId, $status);
            echo json_encode(['success' => true, 'message' => 'Client status updated successfully']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error updating client status: ' . $e->getMessage()]);
        }
    }
}
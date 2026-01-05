<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EmployeeProjectAssignmentModel extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    /**
     * Get all active employees for dropdown
     */
    public function getAllEmployeesForDropdown() {
        $this->db->select('e.publicId, e.name, d.name as designationName');
        $this->db->from('employees e');
        $this->db->join('designations d', 'e.designationId = d.id', 'left');
        $this->db->where('e.designationId !=', '10');
        $this->db->where('e.status', 'active');
        $this->db->order_by('e.name', 'asc');
        $query = $this->db->get();
        
        return $query->result();
    }
    
    /**
     * Get all active projects for dropdown
     */
    public function getAllProjectsForDropdown() {
        $this->db->select('p.publicId, p.name, c.name as clientName');
        $this->db->from('projects p');
        $this->db->join('clients c', 'p.clientId = c.id', 'left');
        $this->db->where('p.status', 'active');
        $this->db->order_by('p.name', 'asc');
        $query = $this->db->get();
        
        return $query->result();
    }
    
    /**
     * Get employee ID from public ID
     */
    public function getEmployeeIdFromPublicId($publicId) {
        $query = $this->db->select('id')
                          ->from('employees')
                          ->where('publicId', $publicId)
                          ->where('status', 'active')
                          ->get();
        
        if ($query->num_rows() > 0) {
            return $query->row()->id;
        }
        return null;
    }
    
    /**
     * Get project ID from public ID
     */
    public function getProjectIdFromPublicId($publicId) {
        $query = $this->db->select('id')
                          ->from('projects')
                          ->where('publicId', $publicId)
                          ->where('status', 'active')
                          ->get();
        
        if ($query->num_rows() > 0) {
            return $query->row()->id;
        }
        return null;
    }
    
    /**
     * Assign project to employee
     */
    public function assignProjectToEmployee($data) {
        // First, check if there's an active assignment
        $this->db->where('projectId', $data['projectId']);
        $this->db->where('employeeId', $data['employeeId']);
        $this->db->where('status', 'active');
        $activeQuery = $this->db->get('projectAssignedTo');
        
        if ($activeQuery->num_rows() > 0) {
            throw new Exception("Employee is already actively assigned to this project");
        }
        
        // Check for any existing assignment (including inactive)
        $this->db->where('projectId', $data['projectId']);
        $this->db->where('employeeId', $data['employeeId']);
        $allQuery = $this->db->get('projectAssignedTo');
        
        if ($allQuery->num_rows() > 0) {
            // Employee was previously assigned but is now inactive
            $existing = $allQuery->row();
            
            // Reactivate with new assignment date
            $this->db->where('id', $existing->id);
            return $this->db->update('projectAssignedTo', [
                'status' => 'active',
                'assignedFrom' => $data['assignedFrom'],
                'updatedAt' => date('Y-m-d H:i:s')
            ]);
        }
        
        // Brand new assignment - never been assigned before
        return $this->db->insert('projectAssignedTo', $data);
    }
    
    /**
     * Get all assignments for a specific employee
     */
    public function getAssignmentsByEmployee($employeePublicId) {
        // First get employee ID
        $employeeId = $this->getEmployeeIdFromPublicId($employeePublicId);
        
        if (!$employeeId) {
            return [];
        }
        
        $this->db->select('pat.id, pat.assignedFrom, pat.status, 
                          p.publicId as projectPublicId, p.name as projectName,
                          c.name as clientName');
        $this->db->from('projectAssignedTo pat');
        $this->db->join('projects p', 'pat.projectId = p.id');
        $this->db->join('clients c', 'p.clientId = c.id', 'left');
        $this->db->where('pat.employeeId', $employeeId);
        $this->db->order_by('pat.assignedFrom', 'desc');
        $query = $this->db->get();
        
        return $query->result();
    }
    
    /**
     * Toggle assignment status
     */
    public function toggleAssignmentStatus($assignmentId, $status) {
        $this->db->where('id', $assignmentId);
        return $this->db->update('projectAssignedTo', [
            'status' => $status,
            'updatedAt' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Check if employee is already assigned to project
     */
    public function isEmployeeAlreadyAssigned($projectId, $employeeId) {
        $this->db->where('projectId', $projectId);
        $this->db->where('employeeId', $employeeId);
        $this->db->where('status', 'active');
        $query = $this->db->get('projectAssignedTo');
        
        return $query->num_rows() > 0;
    }
}
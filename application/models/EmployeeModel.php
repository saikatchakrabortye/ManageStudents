<?php
defined('BASEPATH') OR exit('No direct access allowed');
class EmployeeModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    } 


public function getAllEmployees() // needed to view in employees table
{
    return $this->db->select('employees.*, designations.name as designationName')
    ->from('employees')
    ->join('designations', 'employees.designationId = designations.id')
    ->get()
    ->result();
}

public function getEmployeeById($id) 
{
    return $this->db->select('employees.*, designations.name as designationName')
    ->from('employees')
    ->join('designations', 'employees.designationId = designations.id')
    ->where('employees.id', $id)
    ->get()->row();
}

public function addEmployee($data)
{
    // Check for duplicate email or phone before inserting
    $this->db->where('email', $data['email']);
    $this->db->or_where('phone', $data['phone']);
    $query = $this->db->get('employees');
    
    if ($query->num_rows() > 0) {
        throw new Exception('Duplicate entry');
    }
    
    // If no duplicates, proceed with insert
    // Insert first to get auto-increment ID
    $this->db->insert('employees', $data);
    $employeeId = $this->db->insert_id();
    // Generate publicId
    $publicId = 'EMP' . str_pad($employeeId, 6, '0', STR_PAD_LEFT);
    
    // Update with publicId
    $this->db->where('id', $employeeId);
    $this->db->update('employees', ['publicId' => $publicId]);
    
    return $employeeId;
}

public function updateEmployee($employeePublicId, $data) 
{
    $this->db->where('publicId', $employeePublicId);
    return $this->db->update('employees', $data);
}

public function deactivateEmployee($employeeId, $status) 
{
    $this->db->where('id', $employeeId);
    return $this->db->update('employees', ['status' => $status]);
}

public function getAllEmployeesForDropdown() {
    // Get all active employees with their designation
    $this->db->select('e.id, e.publicId, e.name, e.joiningDate, d.name as designationName');
    $this->db->from('employees e');
    $this->db->join('designations d', 'e.designationId = d.id', 'left');
    $this->db->where('e.status', 'active');
    $this->db->order_by('e.name', 'asc');
    $query = $this->db->get();
            
            return $query->result();
    }
}
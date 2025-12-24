<?php
defined('BASEPATH') OR exit('No direct access allowed');
class EmployeeModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    } 


public function getAllEmployees() // needed to view in employees table
{
    return $this->db->select('employees.*, designations.name as designationName, (SELECT yearlyCtc FROM employeeCtc 
                              WHERE employeeId = employees.id 
                              ORDER BY effectiveStartDate DESC 
                              LIMIT 1) as yearlyCtc' )
    ->from('employees')
    ->join('designations', 'employees.designationId = designations.id', 'left')
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
    $employeeId = $this->db->insert_id(); // insert_id() method returns auto-incremented ID from last insert operation
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

    /*public function authenticate($email, $password) {
        	
    		$query = $this->db->get_where('employees', array('email' => $email));

            
    
    		if($query->num_rows() == 1) {
        		$employee = $query->row();
        		//return $employee->password === $password; // Use it for Plain text password comparison
                return password_verify($password, $employee->password); // Verify hashed password
    		}
    
    		return false;
    	}

        public function getEmployeeByEmail($email) {
        $query = $this->db->get_where('employees', array('email' => $email));
        return $query->row();
    }*/

    
}
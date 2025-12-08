<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EmployeeCtcModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /** Check if CTC record exists for an employee ***/
    public function getEmployeeCtc($employeeId)
    {
        // First get the internal employee ID using the publicId
        $employee = $this->db->select('id')
                            ->from('employees')
                            ->where('publicId', $employeeId)
                            ->get()
                            ->row();
        
        if (!$employee) {
            return null;
        }
        
        // Get the latest CTC record for this employee
        $this->db->select('*')
                ->from('employeeCtc')
                ->where('employeeId', $employee->id)
                ->order_by('effectiveStartDate', 'DESC')
                ->limit(1);
        $query = $this->db->get();
        
        return $query->row();
    }

    /**Get CTC record by its ID*/
    public function getCtcById($ctcId)
    {
        return $this->db->select('*')
                       ->from('employeeCtc')
                       ->where('id', $ctcId)
                       ->get()
                       ->row();
    }

    /**Add new CTC record*/
    public function addEmployeeCtc($data)
    {
        // Validate that effectiveStartDate is not before employee's joining date
        $employee = $this->db->select('joiningDate')
                            ->from('employees')
                            ->where('id', $data['employeeId'])
                            ->get()
                            ->row();
        
        if ($employee && strtotime($data['effectiveStartDate']) < strtotime($employee->joiningDate)) {
            throw new Exception('Effective start date cannot be before joining date');
        }
        
        return $this->db->insert('employeeCtc', $data);
    }

    /**Update existing CTC record*/
    public function updateEmployeeCtc($ctcId, $data)
    {
        // Get previous CTC record
        $previousCtc = $this->getCtcById($ctcId);
        
        if (!$previousCtc) {
            throw new Exception('CTC record not found');
        }
        
        // Validate that new date is equal to or after previous date
        if (strtotime($data['effectiveStartDate']) < strtotime($previousCtc->effectiveStartDate)) {
            throw new Exception('New effective date must be after the previous effective date');
        }
        
        $this->db->where('id', $ctcId);
        return $this->db->update('employeeCtc', $data);
    }

    /**
     * Get the employee ID from public ID
     */
    public function getEmployeeIdFromPublicId($publicId)
    {
        $employee = $this->db->select('id')
                            ->from('employees')
                            ->where('publicId', $publicId)
                            ->get()
                            ->row();
        
        return $employee ? $employee->id : null;
    }
}
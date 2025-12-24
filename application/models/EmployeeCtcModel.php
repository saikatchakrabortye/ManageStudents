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

    public function getLatestEffectiveStartDate($employeeId) {
        $this->db->select('effectiveStartDate');
        $this->db->from('employeeCtc');
        $this->db->where('employeeId', $employeeId);
        $this->db->order_by('effectiveStartDate', 'DESC');
        $this->db->limit(1);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $row = $query->row();
            return $row->effectiveStartDate;
        }
        
        return null;
    }

    public function getLatestCtc($employeeId) {
        $this->db->select('yearlyCtc');
        $this->db->from('employeeCtc');
        $this->db->where('employeeId', $employeeId);
        $this->db->order_by('effectiveStartDate', 'DESC');
        $this->db->limit(1);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $row = $query->row();
            return $row->yearlyCtc;
        }
        
        return null;
    }

    public function getLatestCtcRecordOfEmployee($employeeId) {
        $this->db->select('*');
        $this->db->from('employeeCtc');
        $this->db->where('employeeId', $employeeId);
        $this->db->order_by('effectiveStartDate', 'DESC');
        $this->db->limit(1);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $row = $query->row();
            return $row;
        }
        
        return null;
    }

    public function getSecondLatestRecord($employeeId) {
        $this->db->select('*');
        $this->db->from('employeeCtc');
        $this->db->where('employeeId', $employeeId);
        $this->db->order_by('effectiveStartDate', 'DESC');
        $this->db->limit(1, 1); // LIMIT 1 OFFSET 1
        
        $query = $this->db->get();
        
        return $query->row();
    }

    public function getAllCtcRecordOfEmployee($employeeId) {
        $this->db->select('*');
        $this->db->from('employeeCtc');
        $this->db->where('employeeId', $employeeId);
        $this->db->order_by('effectiveStartDate', 'DESC');
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            
            return $query->result();
        }
        
        return null;
    }

    /**Add new CTC record*/
    public function addEmployeeCtc($data)
    {
        // Using Method Chaining to build the SQL query
        // Checking duplicate CTC for same effective start date
        $countRecords = $this->db->where('employeeId', $data['employeeId'])
            ->where('effectiveStartDate', $data['effectiveStartDate'])
            ->from('employeeCtc')
            ->count_all_results();
        if ($countRecords > 0) {
            throw new Exception('Duplicate CTC for same effective start date not allowed');
        }

        /**Handling when no record exist for a particular employee */
        $latestEffectiveStartDate = $this->getLatestEffectiveStartDate($data['employeeId']);
        
        if ($latestEffectiveStartDate != null)
        {
        if (strtotime($data['effectiveStartDate']) < strtotime($latestEffectiveStartDate)) {
            throw new Exception('Effective start date must be after effective start date of last CTC record');
        }
        }

        // Logic to set effective end date of previous record
        $latestRecord = $this->getLatestCtcRecordOfEmployee($data['employeeId']);
        $effectiveEndDate = date('Y-m-d', strtotime($data['effectiveStartDate'] . ' -1 day'));
        if ($latestRecord) {
            $this->db->where('id', $latestRecord->id);
            $this->db->update('employeeCtc', ['effectiveEndDate' => $effectiveEndDate]);
        }
        return $this->db->insert('employeeCtc', $data);
    }

    /**Update existing CTC record*/
    public function updateEmployeeCtc($ctcId, $data)
    {
        // Get previous CTC record
        //$previousCtc = $this->getCtcById($ctcId);

        $effectiveStartDateOfSecondLatest = $this->getSecondLatestRecord($data['employeeId']);
        
        //if (!$previousCtc) {
        if (!$effectiveStartDateOfSecondLatest) // Means, this is the first record
        {
            // logic implemented in controller. If updating first record, the updated date must be >= employee joining date
        }else
        {
            // Validate that new date is after previous date
            if (strtotime($data['effectiveStartDate']) <= strtotime($effectiveStartDateOfSecondLatest->effectiveStartDate)) {
                throw new Exception('New effective date must be after' . $effectiveStartDateOfSecondLatest->effectiveStartDate);
            }
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
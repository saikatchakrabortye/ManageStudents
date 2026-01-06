<?php
defined ('BASEPATH') OR exit('No direct access allowed!');
/* EFFORTS TABLE STRUCTURE
    create table efforts (
    id bigint primary key auto_increment,
    publicId varchar(20),
    projectId bigint not null,
    employeeId bigint not null,
    effortDate date not null,
    duration time not null,
    createdAt timestamp default current_timestamp,
    updatedAt timestamp default current_timestamp on update current_timestamp,
    status enum('active', 'inactive') default 'active',
    constraint fk_efforts_projectId_projects_id foreign key (projectId) references projects(id),
    constraint fk_efforts_employeeId_employees_id foreign key (employeeId) references employees(id));
*/
class EffortModel extends CI_Model 
{
    public function __construct() {
        parent:: __construct();
        $this->load->database();
    }

    public function addEffort($data)
    {
        // Validation 1: Date cannot be in future
        if (strtotime($data['effortDate']) > strtotime(date('Y-m-d'))) {
            throw new Exception("Effort date cannot be in the future");
        }
        // For same date and same projectId and same employeeId, there cannot be duplicate efforts data.
        $this->db->where('projectId', $data['projectId']);
        $this->db->where('employeeId', $data['employeeId']);
        $this->db->where('effortDate', $data['effortDate']);
        $query = $this->db->get('efforts');
        
        if ($query->num_rows() > 0) {
            throw new Exception("Effort already logged for this project on selected date");
        }

        // Task 1: Insert the record
        $this->db->insert('efforts', $data);

        // Task 2: Get auto-incremented Id of last insert operation
        $effortId = $this->db->insert_id();

        // Task 3: Create a public Id using a logic
        $generatedPublicId = 'SYSEFF' . date('Y'). str_pad($effortId, 6, '0', STR_PAD_LEFT);

        // Task 4: Update the value of publicId of the last insert; which was previously null
        $this->db->where('id', $effortId);
        $this->db->update('efforts', ['publicId' => $generatedPublicId]);

        return $generatedPublicId;
    }

    public function getAllEffortsForEmployeeId($employeeId) // Will be used for listing
    {
        return $this->db->select('efforts.*, projects.name as projectName')
        ->from('efforts')
        ->join('projects', 'efforts.projectId = projects.id', 'left')
        ->where('efforts.employeeId', $employeeId)
        ->get()
        ->result();
    }
    
    public function getAllEfforts() // Will be used for listing
    {
        return $this->db->select('efforts.*, projects.name as projectName, employees.name as employeeName')
        ->from('efforts')
        ->join('projects', 'efforts.projectId = projects.id', 'left')
        ->join('employees', 'efforts.employeeId = employees.id', 'left')
        ->get()
        ->result();
    }
    
    /**
     * Get total hours worked for an employee
     * Converts minutes to hours:minutes format
     */
    public function getTotalHoursWorkedForEmployee($employeeId) 
    {
        // Query to sum all duration for the employee
        $this->db->select("SEC_TO_TIME(SUM(TIME_TO_SEC(duration))) as total_duration");
        $this->db->from('efforts');
        $this->db->where('employeeId', $employeeId);
        $this->db->where('status', 'active');
        $query = $this->db->get();
        
        $result = $query->row();
        
        if ($result && $result->total_duration) {
            return $this->formatDuration($result->total_duration);
        }
        
        return '0:00'; // Return 0 hours if no efforts found
    }
    
    /**
     * Format duration from HH:MM:SS to HH:MM
     * Converts total minutes to hours:minutes
     */
    private function formatDuration($duration) 
    {
        if (!$duration) return '0:00';
        
        // Split the duration into hours, minutes, seconds
        list($hours, $minutes, $seconds) = explode(':', $duration);
        
        $totalMinutes = ($hours * 60) + (int)$minutes;
        
        // Calculate hours and remaining minutes
        $formattedHours = floor($totalMinutes / 60);
        $formattedMinutes = $totalMinutes % 60;
        
        // Format with leading zeros
        return sprintf('%d:%02d', $formattedHours, $formattedMinutes);
    }

    public function getEffortByPublicId($publicId)
    {
        return $this->db->select('efforts.*, projects.name as projectName')
        ->from('efforts')
        ->join('projects', 'efforts.projectId = projects.id', 'left')
        ->where('efforts.publicId', $publicId)
        ->get()
        ->row();
    }

    public function getFilteredEffortsForEmployee($employeeId, $fromDate, $toDate, $projectId = null)
    {
        $this->db->select('efforts.*, projects.name as projectName, employees.name as employeeName')
            ->from('efforts')
            ->join('projects', 'efforts.projectId = projects.id', 'left')
            ->join('employees', 'efforts.employeeId = employees.id', 'left')
            ->where('efforts.employeeId', $employeeId)
            ->where('efforts.effortDate >=', $fromDate)
            ->where('efforts.effortDate <=', $toDate);
        
        // Add project filter if provided
        if (!empty($projectId)) {
            $this->db->where('efforts.projectId', $projectId);
        }
        
        return $this->db->order_by('efforts.effortDate', 'desc')
            ->get()
            ->result();
    }

    /**
     * Get all filtered efforts (for admin view)
     */
    public function getAllFilteredEfforts($fromDate, $toDate, $projectId = null)
    {
        $this->db->select('efforts.*, projects.name as projectName, employees.name as employeeName')
            ->from('efforts')
            ->join('projects', 'efforts.projectId = projects.id', 'left')
            ->join('employees', 'efforts.employeeId = employees.id', 'left')
            ->where('efforts.effortDate >=', $fromDate)
            ->where('efforts.effortDate <=', $toDate);
        
        // Add project filter if provided
        if (!empty($projectId)) {
            $this->db->where('efforts.projectId', $projectId);
        }
        
        return $this->db->order_by('efforts.effortDate', 'desc')
            ->order_by('employees.name', 'asc')
            ->get()
            ->result();
    }

    public function updateEffortDuration($publicId, $duration)
    {
        $this->db->where('publicId', $publicId);
        return $this->db->update('efforts', [
            'duration' => $duration,
            'updatedAt' => date('Y-m-d H:i:s')
        ]);
    }
}
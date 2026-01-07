<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MonthlyProjectCostAllocation extends MY_Controller
{
    public function __construct() {
        parent::__construct();
        $this->load->model('ProjectModel');
        $this->load->model('EmployeeModel');
        $this->load->model('EmployeeCtcModel');
        $this->load->model('EffortModel');
    }

    public function index()
    {
        // Only admin can access this module
        $designationId = $this->session->userdata('designationId');
        if ($designationId != 10) {
            show_error('Access denied. Admin only.', 403);
            return;
        }
        
        // Get filter values
        $month = $this->input->get('month') ?: date('m');
        $year = $this->input->get('year') ?: date('Y');
        $projectId = $this->input->get('projectId');
        
        $data = [
            'month' => $month,
            'year' => $year,
            'selectedProjectId' => $projectId,
            'allocationData' => [],
            'projectName' => '',
            'projectPublicId' => '',
            'totalProjectCost' => 0
        ];
        
        // Load all projects for dropdown
        $data['projects'] = $this->ProjectModel->getAllProjects();
        
        // If project is selected, calculate allocation
        if (!empty($projectId)) {
            // Get project details
            $project = $this->getProjectById($projectId);
            if ($project) {
                $data['projectName'] = $project->name;
                $data['projectPublicId'] = $project->publicId;
                
                // Get efforts for the selected month/year
                $startDate = date('Y-m-01', strtotime("$year-$month-01"));
                $endDate = date('Y-m-t', strtotime("$year-$month-01"));
                
                // Get all employees who worked on this project during the period
                $projectEmployees = $this->getProjectEmployees($projectId, $startDate, $endDate);
                
                if (!empty($projectEmployees)) {
                    $allocationData = [];
                    $totalProjectCost = 0;
                    
                    foreach ($projectEmployees as $employeeId) {
                        // Get employee details
                        $employee = $this->EmployeeModel->getEmployeeById($employeeId);
                        
                        if ($employee) {
                            // Get employee's latest CTC
                            $ctcRecord = $this->EmployeeCtcModel->getLatestCtcRecordOfEmployee($employeeId);
                            
                            if ($ctcRecord) {
                                $monthlyCtc = $ctcRecord->yearlyCtc / 12;
                                
                                // Get employee's effort on selected project
                                $projectEffortHours = $this->getEmployeeEffortOnProject($employeeId, $projectId, $startDate, $endDate);
                                
                                // Get employee's total effort on ALL projects
                                $allProjectsEffortHours = $this->getEmployeeTotalEffort($employeeId, $startDate, $endDate);
                                
                                if ($allProjectsEffortHours > 0 && $projectEffortHours > 0) {
                                    // Calculate cost allocation using the formula: (Project Effort / All Projects Effort) * Monthly CTC
                                    $costAllocation = ($projectEffortHours / $allProjectsEffortHours) * $monthlyCtc;
                                    
                                    $allocationData[] = [
                                        'employeeId' => $employeeId,
                                        'employeeName' => $employee->name,
                                        'employeePublicId' => $employee->publicId,
                                        'monthlyCtc' => round($monthlyCtc, 2),
                                        'projectEffortHours' => round($projectEffortHours, 2),
                                        'allProjectsEffortHours' => round($allProjectsEffortHours, 2),
                                        'costAllocation' => round($costAllocation, 2)
                                    ];
                                    
                                    $totalProjectCost += $costAllocation;
                                }
                            }
                        }
                    }
                    
                    $data['allocationData'] = $allocationData;
                    $data['totalProjectCost'] = round($totalProjectCost, 2);
                }
            }
        }
        
        $this->renderWithSideBar('MonthlyProjectCostAllocationView', $data);
    }
    
    /**
     * Get project by ID
     */
    private function getProjectById($projectId)
    {
        return $this->db->select('id, publicId, name')
                        ->from('projects')
                        ->where('id', $projectId)
                        ->where('status', 'active')
                        ->get()
                        ->row();
    }
    
    /**
     * Get unique employees who worked on a project during a period
     */
    private function getProjectEmployees($projectId, $startDate, $endDate)
    {
        $this->db->select('DISTINCT(employeeId)')
                 ->from('efforts')
                 ->where('projectId', $projectId)
                 ->where('effortDate >=', $startDate)
                 ->where('effortDate <=', $endDate)
                 ->where('status', 'active');
        
        $query = $this->db->get();
        $results = $query->result_array();
        
        return array_column($results, 'employeeId');
    }
    
    /**
     * Get employee's effort on a specific project
     */
    private function getEmployeeEffortOnProject($employeeId, $projectId, $startDate, $endDate)
    {
        $this->db->select('SUM(TIME_TO_SEC(duration)) as total_seconds')
                 ->from('efforts')
                 ->where('employeeId', $employeeId)
                 ->where('projectId', $projectId)
                 ->where('effortDate >=', $startDate)
                 ->where('effortDate <=', $endDate)
                 ->where('status', 'active');
        
        $query = $this->db->get();
        $result = $query->row();
        
        return $result && $result->total_seconds ? round($result->total_seconds / 3600, 2) : 0;
    }
    
    /**
     * Get employee's total effort on ALL projects
     */
    private function getEmployeeTotalEffort($employeeId, $startDate, $endDate)
    {
        $this->db->select('SUM(TIME_TO_SEC(duration)) as total_seconds')
                 ->from('efforts')
                 ->where('employeeId', $employeeId)
                 ->where('effortDate >=', $startDate)
                 ->where('effortDate <=', $endDate)
                 ->where('status', 'active');
        
        $query = $this->db->get();
        $result = $query->row();
        
        return $result && $result->total_seconds ? round($result->total_seconds / 3600, 2) : 0;
    }
}
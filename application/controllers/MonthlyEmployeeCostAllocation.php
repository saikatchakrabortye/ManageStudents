<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MonthlyEmployeeCostAllocation extends MY_Controller
{
    public function __construct() {
        parent::__construct();
        $this->load->model('EmployeeModel');
        $this->load->model('EmployeeCtcModel');
        $this->load->model('EffortModel');
        $this->load->model('ProjectModel');
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
        $employeeId = $this->input->get('employeeId');
        
        $data = [
            'month' => $month,
            'year' => $year,
            'selectedEmployeeId' => $employeeId,
            'allocationData' => [],
            'totalEffortHours' => 0,
            'totalCostAllocation' => 0,
            'employeeCtc' => null,
            'employeeName' => ''
        ];
        
        // Load all employees for dropdown
        $data['employees'] = $this->EmployeeModel->getAllEmployeesForDropdown();
        
        // If employee is selected, calculate allocation
        if (!empty($employeeId)) {
            // Get employee details
            $employee = $this->EmployeeModel->getEmployeeById($employeeId);
            if ($employee) {
                $data['employeeName'] = $employee->name;
                $data['employeePublicId'] = $employee->publicId;
                
                // Get employee's latest CTC
                $ctcRecord = $this->EmployeeCtcModel->getLatestCtcRecordOfEmployee($employeeId);
                if ($ctcRecord) {
                    $data['employeeCtc'] = $ctcRecord->yearlyCtc;
                    
                    // Calculate monthly salary (yearly CTC / 12)
                    $monthlySalary = $ctcRecord->yearlyCtc / 12;
                    $data['monthlySalary'] = $monthlySalary;
                    
                    // Get efforts for the selected month/year
                    $startDate = date('Y-m-01', strtotime("$year-$month-01"));
                    $endDate = date('Y-m-t', strtotime("$year-$month-01"));
                    
                    // Get efforts grouped by project
                    $efforts = $this->getEmployeeEffortsByProject($employeeId, $startDate, $endDate);
                    
                    if (!empty($efforts)) {
                        // Calculate total effort hours
                        $totalEffortHours = array_sum(array_column($efforts, 'total_hours'));
                        $data['totalEffortHours'] = $totalEffortHours;
                        
                        // Calculate allocation
                        $allocationData = [];
                        $totalCostAllocation = 0;
                        $totalEffortPercentage = 0;
                        
                        foreach ($efforts as $effort) {
                            $effortPercentage = ($totalEffortHours > 0) ? ($effort['total_hours'] / $totalEffortHours) * 100 : 0;
                            $costAllocation = ($effortPercentage / 100) * $monthlySalary;
                            
                            $allocationData[] = [
                                'projectName' => $effort['project_name'],
                                'effortHours' => $effort['total_hours'],
                                'effortPercentage' => round($effortPercentage, 2),
                                'costAllocation' => round($costAllocation, 2)
                            ];
                            
                            $totalCostAllocation += $costAllocation;
                            $totalEffortPercentage += $effortPercentage;
                        }
                        
                        $data['allocationData'] = $allocationData;
                        $data['totalCostAllocation'] = round($totalCostAllocation, 2);
                        $data['totalEffortPercentage'] = round($totalEffortPercentage, 2);
                    }
                }
            }
        }
        
        $this->renderWithSideBar('MonthlyEmployeeCostAllocationView', $data);
    }
    
    /**
     * Get employee efforts grouped by project for a date range
     */
    private function getEmployeeEffortsByProject($employeeId, $startDate, $endDate)
    {
        $this->db->select('p.name as project_name, 
                          SUM(TIME_TO_SEC(e.duration)) as total_seconds')
                 ->from('efforts e')
                 ->join('projects p', 'e.projectId = p.id')
                 ->where('e.employeeId', $employeeId)
                 ->where('e.effortDate >=', $startDate)
                 ->where('e.effortDate <=', $endDate)
                 ->where('e.status', 'active')
                 ->group_by('e.projectId')
                 ->order_by('total_seconds', 'DESC');
        
        $query = $this->db->get();
        $results = $query->result_array();
        
        // Convert seconds to hours
        foreach ($results as &$result) {
            $result['total_hours'] = round($result['total_seconds'] / 3600, 2);
            unset($result['total_seconds']);
        }
        
        return $results;
    }
    
    /**
     * AJAX endpoint to get employee CTC
     */
    public function getEmployeeCtc()
    {
        header('Content-Type: application/json');
        
        $employeeId = $this->input->post('employeeId');
        
        if (empty($employeeId)) {
            echo json_encode(['success' => false, 'message' => 'Employee ID is required']);
            return;
        }
        
        $ctcRecord = $this->EmployeeCtcModel->getLatestCtcRecordOfEmployee($employeeId);
        
        if ($ctcRecord) {
            echo json_encode([
                'success' => true,
                'yearlyCtc' => $ctcRecord->yearlyCtc,
                'monthlySalary' => round($ctcRecord->yearlyCtc / 12, 2)
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No CTC record found for this employee']);
        }
    }
}
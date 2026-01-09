<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TotalProjectCost extends MY_Controller
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
        $projectId = $this->input->get('projectId');
        $uptoDate = $this->input->get('uptoDate') ?: date('Y-m-d');
        
        $data = [
            'selectedProjectId' => $projectId,
            'uptoDate' => $uptoDate,
            'allocationData' => [],
            'projectName' => '',
            'projectPublicId' => '',
            'projectStartDate' => '',
            'totalProjectCost' => 0,
            'totalEffortMinutes' => 0,
            'periods' => [] // Add this to store monthly breakdown
        ];
        
        // Load all projects for dropdown
        $data['projects'] = $this->ProjectModel->getAllProjects();
        
        // If project is selected, calculate allocation
        if (!empty($projectId)) {
            // Get project details
            $project = $this->getProjectDetails($projectId);
            if ($project) {
                $data['projectName'] = $project->name;
                $data['projectPublicId'] = $project->publicId;
                $data['projectStartDate'] = $project->startDate;
                
                // Validate upto date
                if (strtotime($uptoDate) < strtotime($project->startDate)) {
                    $data['error'] = 'Upto date cannot be before project start date (' . date('d-m-Y', strtotime($project->startDate)) . ')';
                    $uptoDate = date('Y-m-d');
                    $data['uptoDate'] = $uptoDate;
                }
                
                // Get all unique months from project start date to upto date
                $monthlyPeriods = $this->getMonthlyPeriods($project->startDate, $uptoDate);
                $data['periods'] = $monthlyPeriods;
                
                // Get all employees who worked on this project from start date to upto date
                $projectEmployees = $this->getProjectEmployees($projectId, $project->startDate, $uptoDate);
                
                if (!empty($projectEmployees)) {
                    $allocationData = [];
                    $totalProjectCost = 0;
                    $totalEffortMinutes = 0;
                    
                    foreach ($projectEmployees as $employeeId) {
                        // Get employee details
                        $employee = $this->EmployeeModel->getEmployeeById($employeeId);
                        
                        if ($employee) {
                            // Get employee's latest CTC (assuming CTC doesn't change, or use monthly CTC if available)
                            $ctcRecord = $this->EmployeeCtcModel->getLatestCtcRecordOfEmployee($employeeId);
                            
                            if ($ctcRecord) {
                                $monthlyCtc = $ctcRecord->yearlyCtc / 12;
                                
                                // Initialize employee data
                                $employeeData = [
                                    'employeeId' => $employeeId,
                                    'employeeName' => $employee->name,
                                    'employeePublicId' => $employee->publicId,
                                    'monthlyCtc' => round($monthlyCtc, 2),
                                    'monthlyAllocations' => [],
                                    'totalCostAllocation' => 0,
                                    'totalEffortHours' => 0,
                                    'totalEffortMinutes' => 0
                                ];
                                
                                // Calculate allocation for each month
                                $employeeTotalCost = 0;
                                $employeeTotalEffortHours = 0;
                                
                                foreach ($monthlyPeriods as $period) {
                                    $startDate = $period['start'];
                                    $endDate = $period['end'];
                                    $yearMonth = $period['yearMonth'];
                                    
                                    // Get employee's effort on selected project for this month
                                    $projectEffortHours = $this->getEmployeeEffortOnProject($employeeId, $projectId, $startDate, $endDate);
                                    
                                    // Get employee's total effort on ALL projects for this month
                                    $allProjectsEffortHours = $this->getEmployeeTotalEffort($employeeId, $startDate, $endDate);
                                    
                                    $monthlyAllocation = 0;
                                    
                                    if ($allProjectsEffortHours > 0 && $projectEffortHours > 0) {
                                        // Calculate effort percentage for this month
                                        $effortPercentage = ($projectEffortHours / $allProjectsEffortHours) * 100;
                                        
                                        // Calculate cost allocation using the formula: (Effort% / 100) * Monthly CTC
                                        $monthlyAllocation = round(($effortPercentage / 100) * $monthlyCtc, 2);
                                        
                                        $employeeTotalEffortHours += $projectEffortHours;
                                    }
                                    
                                    $employeeData['monthlyAllocations'][$yearMonth] = [
                                        'effortHours' => round($projectEffortHours, 2),
                                        'allocation' => $monthlyAllocation
                                    ];
                                    
                                    $employeeTotalCost += $monthlyAllocation;
                                }
                                
                                // Convert total hours to hours:minutes format
                                $totalMinutes = round($employeeTotalEffortHours * 60);
                                $hours = floor($totalMinutes / 60);
                                $minutes = $totalMinutes % 60;
                                
                                $employeeData['totalCostAllocation'] = round($employeeTotalCost, 2);
                                $employeeData['totalEffortHours'] = round($employeeTotalEffortHours, 2);
                                $employeeData['totalEffortDisplay'] = sprintf("%d:%02d", $hours, $minutes);
                                $employeeData['totalEffortMinutes'] = $totalMinutes;
                                
                                // ADD THIS LINE FOR BACKWARD COMPATIBILITY WITH VIEW:
                                $employeeData['costAllocation'] = $employeeData['totalCostAllocation'];
                                
                                $allocationData[] = $employeeData;
                                $totalProjectCost += $employeeTotalCost;
                                $totalEffortMinutes += $totalMinutes;
                            }
                        }
                    }
                    
                    // Adjust the last item to ensure no rounding error in total
                    if (!empty($allocationData)) {
                        $calculatedTotal = array_sum(array_column($allocationData, 'totalCostAllocation'));
                        $difference = round($totalProjectCost - $calculatedTotal, 2);
                        
                        if (abs($difference) > 0) {
                            $lastIndex = count($allocationData) - 1;
                            $allocationData[$lastIndex]['totalCostAllocation'] = 
                                round($allocationData[$lastIndex]['totalCostAllocation'] + $difference, 2);
                            // Also update the costAllocation key for consistency
                            $allocationData[$lastIndex]['costAllocation'] = 
                                $allocationData[$lastIndex]['totalCostAllocation'];
                            $totalProjectCost = $calculatedTotal + $difference;
                        }
                    }
                    
                    $data['allocationData'] = $allocationData;
                    $data['totalProjectCost'] = round($totalProjectCost, 2);
                    $data['totalEffortMinutes'] = $totalEffortMinutes;
                }
            }
        }
        
        $this->renderWithSideBar('TotalProjectCostView', $data);
    }
    
    /**
     * Get project details including start date
     */
    private function getProjectDetails($projectId)
    {
        return $this->db->select('id, publicId, name, startDate')
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
     * Get employee's effort on a specific project during a period
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
     * Get employee's total effort on ALL projects during a period
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
    
    /**
     * Get all monthly periods between two dates
     */
    private function getMonthlyPeriods($startDate, $endDate)
    {
        $periods = [];
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $interval = new DateInterval('P1M');
        
        // Set start to first day of the month
        $periodStart = new DateTime($start->format('Y-m-01'));
        
        while ($periodStart <= $end) {
            $periodEnd = clone $periodStart;
            $periodEnd->modify('last day of this month');
            
            // If periodEnd is beyond our end date, use the end date
            if ($periodEnd > $end) {
                $periodEnd = $end;
            }
            
            // If periodStart is before our actual start date, use the start date
            $actualStart = $periodStart < $start ? $start : $periodStart;
            
            $periods[] = [
                'start' => $actualStart->format('Y-m-d'),
                'end' => $periodEnd->format('Y-m-d'),
                'yearMonth' => $periodStart->format('Y-m'),
                'monthName' => $periodStart->format('F Y')
            ];
            
            // Move to next month
            $periodStart->add($interval);
        }
        
        return $periods;
    }
}
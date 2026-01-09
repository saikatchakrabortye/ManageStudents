<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProjectsCostSummary extends MY_Controller
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
            'summaryData' => [],
            'totalCost' => 0,
            'totalEffortMinutes' => 0
        ];
        
        // Load all projects for dropdown
        $data['projects'] = $this->ProjectModel->getAllProjects();
        
        // Get all active projects or specific project
        $projectsToProcess = [];
        if (!empty($projectId)) {
            // Get specific project
            $project = $this->getProjectDetails($projectId);
            if ($project) {
                $projectsToProcess[] = $project;
            }
        } else {
            // Get all active projects
            $projectsToProcess = $this->getAllActiveProjects();
        }
        
        // Calculate cost for each project
        $summaryData = [];
        $totalCost = 0;
        $totalEffortMinutes = 0;
        
        foreach ($projectsToProcess as $project) {
            // Validate upto date against project start date
            if (strtotime($uptoDate) < strtotime($project->startDate)) {
                // Project started after upto date, skip calculation
                $summaryData[] = [
                    'projectId' => $project->id,
                    'projectName' => $project->name,
                    'projectPublicId' => $project->publicId,
                    'projectStartDate' => $project->startDate,
                    'totalCost' => 0,
                    'totalEffort' => '0h/00m',
                    'totalEffortMinutes' => 0,
                    'status' => 'Not started yet'
                ];
                continue;
            }
            
            // Calculate project cost
            $projectCost = $this->calculateProjectCost($project->id, $project->startDate, $uptoDate);
            
            $summaryData[] = [
                'projectId' => $project->id,
                'projectName' => $project->name,
                'projectPublicId' => $project->publicId,
                'projectStartDate' => $project->startDate,
                'totalCost' => $projectCost['totalCost'],
                'totalEffort' => $projectCost['totalEffortDisplay'],
                'totalEffortMinutes' => $projectCost['totalEffortMinutes'],
                'status' => $projectCost['totalCost'] > 0 ? 'Active' : 'No efforts'
            ];
            
            $totalCost += $projectCost['totalCost'];
            $totalEffortMinutes += $projectCost['totalEffortMinutes'];
        }
        
        // Sort by total cost descending
        usort($summaryData, function($a, $b) {
            return $b['totalCost'] <=> $a['totalCost'];
        });
        
        $data['summaryData'] = $summaryData;
        $data['totalCost'] = round($totalCost, 2);
        $data['totalEffortMinutes'] = $totalEffortMinutes;
        
        $this->renderWithSideBar('ProjectsCostSummaryView', $data);
    }
    
    /**
     * Calculate total cost for a project
     */
    private function calculateProjectCost($projectId, $projectStartDate, $uptoDate)
    {
        // Get all unique employees who worked on this project
        $projectEmployees = $this->getProjectEmployees($projectId, $projectStartDate, $uptoDate);
        
        $totalCost = 0;
        $totalEffortMinutes = 0;
        
        if (empty($projectEmployees)) {
            return [
                'totalCost' => 0,
                'totalEffortDisplay' => '00:00',
                'totalEffortMinutes' => 0
            ];
        }
        
        // Get monthly periods
        $monthlyPeriods = $this->getMonthlyPeriods($projectStartDate, $uptoDate);
        
        foreach ($projectEmployees as $employeeId) {
            // Get employee details
            $employee = $this->EmployeeModel->getEmployeeById($employeeId);
            
            if ($employee) {
                // Get employee's latest CTC
                $ctcRecord = $this->EmployeeCtcModel->getLatestCtcRecordOfEmployee($employeeId);
                
                if ($ctcRecord) {
                    $monthlyCtc = $ctcRecord->yearlyCtc / 12;
                    $employeeTotalCost = 0;
                    $employeeTotalEffortHours = 0;
                    
                    foreach ($monthlyPeriods as $period) {
                        $startDate = $period['start'];
                        $endDate = $period['end'];
                        
                        // Get employee's effort on selected project for this month
                        $projectEffortHours = $this->getEmployeeEffortOnProject($employeeId, $projectId, $startDate, $endDate);
                        
                        // Get employee's total effort on ALL projects for this month
                        $allProjectsEffortHours = $this->getEmployeeTotalEffort($employeeId, $startDate, $endDate);
                        
                        if ($allProjectsEffortHours > 0 && $projectEffortHours > 0) {
                            // Calculate effort percentage for this month
                            $effortPercentage = ($projectEffortHours / $allProjectsEffortHours) * 100;
                            
                            // Calculate cost allocation using the formula: (Effort% / 100) * Monthly CTC
                            $monthlyAllocation = round(($effortPercentage / 100) * $monthlyCtc, 2);
                            
                            $employeeTotalEffortHours += $projectEffortHours;
                            $employeeTotalCost += $monthlyAllocation;
                        }
                    }
                    
                    // Convert total hours to minutes
                    $employeeTotalMinutes = round($employeeTotalEffortHours * 60);
                    $totalEffortMinutes += $employeeTotalMinutes;
                    $totalCost += $employeeTotalCost;
                }
            }
        }
        
        // Format total effort display
        $totalHours = floor($totalEffortMinutes / 60);
        $totalMinutes = $totalEffortMinutes % 60;
        $totalEffortDisplay = sprintf("%d:%02d", $totalHours, $totalMinutes);
        
        return [
            'totalCost' => round($totalCost, 2),
            'totalEffortDisplay' => $totalEffortDisplay,
            'totalEffortMinutes' => $totalEffortMinutes
        ];
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
     * Get all active projects
     */
    private function getAllActiveProjects()
    {
        return $this->db->select('id, publicId, name, startDate')
                        ->from('projects')
                        ->where('status', 'active')
                        ->order_by('name', 'ASC')
                        ->get()
                        ->result();
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
                'end' => $periodEnd->format('Y-m-d')
            ];
            
            // Move to next month
            $periodStart->add($interval);
        }
        
        return $periods;
    }
}
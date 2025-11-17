<?php
// application/controllers/Dashboard.php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {
    public function __construct() {
        parent::__construct();

        $this->load->library('session');
        $this->load->helper('url');
        // Check auth for ALL methods in this controller
        if (!$this->session->userdata('loggedIn') || !$this->session->userdata('userId')) {
            redirect('Login');
        }
    }
    
    public function index() {
        // Load the main dashboard layout
        $this->load->view('MasterDashboard');
    }
    
    public function studentDashboard() {
        // Return pure HTML for students dashboard
        $this->load->view('ContentStudentDashboard');
    }
    
    public function userDashboard() {
        // Return pure HTML for users dashboard  
        $this->load->view('ContentUserDashboard');
    }
}
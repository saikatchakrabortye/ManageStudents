<?php
// application/controllers/Dashboard.php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {
    public function __construct() {
        parent::__construct();
    }
    
    public function index() {
        // Load the main dashboard layout
        //$this->load->view('DashboardView');
        $this->renderWithSidebar('DashboardView');
    }
}
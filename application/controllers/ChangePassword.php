<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class ChangePassword extends MY_Controller {
    public function __construct() {
        parent:: __construct();
        $this->load->model('EmployeeModel');
    }
    public function index() {
        $this->renderWithSidebar('ChangePasswordView');
    }

    public function changePassword() {
        header('Content-Type: application/json');

        // Using Centralized Validation Function
        $validation = $this->validate('changePassword');
        if (isset($validation['error'])) {
            $errorMessages = implode(', ', $validation['error']);
            echo json_encode([
                'success' => false, 
                'message' => 'Validation failed: ' . $errorMessages,
                'errors' => $validation['error']
            ]);
            return;
        }
        

        $employeeId = $validation['data']['employeeId'];
        $oldPassword = $validation['data']['oldPassword'];
        $newPassword = $validation['data']['newPassword'];
        $confirmPassword = $validation['data']['confirmPassword'];

        if ($newPassword != $confirmPassword)
        {
            //throw new Exception("Confirm Password doesn't match");
            echo json_encode([
                 'success' => false,
                 'message' => 'Confirm Password does not match'
             ]);
             return;
        }

        $employee = $this->EmployeeModel->getEmployeeById($employeeId);
        if (!password_verify($oldPassword, $employee->password)) {
             echo json_encode([
                 'success' => false,
                 'message' => 'Old password is incorrect'
             ]);
             return;
         }

         if ($oldPassword === $newPassword) {
             echo json_encode([
                 'success' => false,
                 'message' => 'Same Old Password cannot be set'
             ]);
             return;
         }
        $data = [
                'password' => password_hash($newPassword, PASSWORD_DEFAULT)
            ];
            
        $result = $this->EmployeeModel->changePassword($employeeId, $data);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Password changed successfully! Login with new password'
                ]);
                
            } else {
                throw new Exception("Failed to change password");
            }

}
}
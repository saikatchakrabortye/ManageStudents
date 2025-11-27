<?php
defined ('BASEPATH') OR exit('No direct script access allowed');
class Permissions extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('RoleModel');
        $this->load->model('PermissionModel');
    }

    public function index() {
        $permissions = $this->PermissionModel->getAllPermissions();
        
        // Group permissions by groupName
        $groupedPermissions = [];
        foreach ($permissions as $perm) {
            $groupedPermissions[$perm->groupName][] = $perm;
        }

        $data = [
            'groupedPermissions' => $groupedPermissions,
            'roles' => $this->RoleModel->getAllRolesData(),
        ];

        
        $this->load->view('PermissionsDashboard', $data);
    }

    public function getRolePermissionsById($roleId) {
        header('Content-Type: application/json');
        $activePermissions = $this->PermissionModel->getRolePermissionsById($roleId);
        echo json_encode($activePermissions);
    }

    public function activatePermission() {
        $input = json_decode(file_get_contents('php://input'), true); // Standard for REST API input, handle JSON input payload; more secure than $this->input->post()
        $roleId = $input['roleId'];
        $permissionId = $input['permissionId'];
        
        $result = $this->PermissionModel->activatePermission($roleId, $permissionId);
        echo json_encode(['success' => $result]);
    }

    public function deactivatePermission() {
        $input = json_decode(file_get_contents('php://input'), true);
        $roleId = $input['roleId'];
        $permissionId = $input['permissionId'];
        
        $result = $this->PermissionModel->deactivatePermission($roleId, $permissionId);
        echo json_encode(['success' => $result]);
    }
}
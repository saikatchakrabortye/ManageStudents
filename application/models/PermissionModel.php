<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class PermissionModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function getAllPermissions() {
        // Get all permissions grouped by groupName
        return $this->db->select('id, name, description, groupName')
                           ->where('status', 'active')
                           ->order_by('groupName, name')
                           ->get('permissions')->result();
    }

    public function getRolePermissionsById($roleId) {
    return $this->db->select('permissionId')
                   ->where('roleId', $roleId)
                   ->where('status', 'active')
                   ->get('role_permissions')
                   ->result();
}

/**Using Parameter binding. Prevents SQL injection */
// On duplicate key update not available in codeignitor query buider.
public function activatePermission($roleId, $permissionId) {
    $sql = "INSERT INTO role_permissions (roleId, permissionId, status) 
            VALUES (?, ?, 'active') 
            ON DUPLICATE KEY UPDATE status = 'active'";
    return $this->db->query($sql, [$roleId, $permissionId]);
}

public function deactivatePermission($roleId, $permissionId) {
    return $this->db->where('roleId', $roleId)
                   ->where('permissionId', $permissionId)
                   ->update('role_permissions', ['status' => 'inactive']);
}
    
}
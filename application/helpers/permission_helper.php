<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function checkPermission($permissionName) {
    $CI =& get_instance();
    $CI->load->library('session'); //added to access session
    $CI->load->database(); //added to access database
    $roleId = $CI->session->userdata('roleId');
    
    $query = $CI->db->from('role_permissions rp')
    ->join('permissions p', 'rp.permissionId=p.id')
    ->join('roles r', 'rp.roleId=r.id')
    ->where('p.name', $permissionName)
    ->where('rp.roleId', $roleId)
    ->where('rp.status', 'active')
    ->where('r.status', 'active')
    ->where('p.status', 'active')
    ->get();

    return $query->num_rows() > 0; // returns true if permission exist
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DesignationModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    } 

    public function getAllDesignations() {
        return $this->db->order_by('name', 'ASC')
        ->where('name !=', 'Admin')
        ->get('designations')->result();
    }

    public function getDesignationById($id) {
        $this->db->where('id', $id);
        return $this->db->get('designations')->row();
    }

    public function addDesignation($data) {
        // Check for duplicate designation before inserting
        $this->db->where('name', $data['name']);
        $query = $this->db->get('designations');
        
        if ($query->num_rows() > 0) {
            throw new Exception('Duplicate Designation entry');
        }
        
        // If no duplicates, proceed with insert
        $this->db->insert('designations', $data);
        return $this->db->insert_id();
    }

    public function updateDesignation($designationId, $data) {
        $this->db->where('id', $designationId);
        return $this->db->update('designations', $data);
    }

    public function deactivateDesignation($designationId, $status) {
        $this->db->where('id', $designationId);
        return $this->db->update('designations', ['status' => $status]);
    }
    
    public function designationExists($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('designations');
        return $query->num_rows() > 0;
    }
}
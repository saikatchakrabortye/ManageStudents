<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RoleModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**Function used for pagenation Function (1/2), used by controller */
    public function getPaginatedRoles($limit, $offset, $search = '') //Search parameter added for search functionality; else exclude it. Model Step (1/3)
    {

        // NEW: Add search conditions if search term exists. Model Step (2/3)
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->group_end();
        }

        return $this->db->limit($limit, $offset)->get('roles')->result(); // remains same, even when using search functionality;
    }

    /**Function used for pagenation Function (2/2), used by controller */
    public function getTotalRoles($search = '') {
        /**Code when not using pagenation */
        /*return $this->db->get('students')->result(); //result() returns an array of objects*/
        /****Code for without pagenation ends here */

        /****Code for with pagenation starts here */
        // NEW: Add same search conditions for count as used in getPagenatedStudents(). Model Step (3/3)
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->group_end();
        }
        return $this->db->count_all('roles');
        /****Code for with pagenation ends here */
    }

    public function addRole($data) {

        // Check for duplicate email or phone before inserting
    $this->db->where('name', $data['name']);
    $query = $this->db->get('roles');
    
    if ($query->num_rows() > 0) {
        throw new Exception('Duplicate entry');
    }
    
    // If no duplicates, proceed with insert
    return $this->db->insert('roles', $data);
    }

    /**For learning */
    public function getAllRolesData() {
        return $this->db->get('roles')->result();
    }    
}


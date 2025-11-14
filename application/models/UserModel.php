<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**Function used for pagenation Function (1/2), used by controller */
    public function getPaginatedUsers($limit, $offset, $search = '') //Search parameter added for search functionality; else exclude it. Model Step (1/3)
    {

        // NEW: Add search conditions if search term exists. Model Step (2/3)
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->or_like('email', $search);
            $this->db->or_like('phone', $search);
            $this->db->group_end();
        }

        return $this->db->limit($limit, $offset)->get('users')->result(); // remains same, even when using search functionality;
    }

    /**Function used for pagenation Function (2/2), used by controller */
    public function getTotalUsers($search = '') {
        /**Code when not using pagenation */
        /*return $this->db->get('students')->result(); //result() returns an array of objects*/
        /****Code for without pagenation ends here */

        /****Code for with pagenation starts here */
        // NEW: Add same search conditions for count as used in getPagenatedStudents(). Model Step (3/3)
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->or_like('email', $search);
            $this->db->or_like('phone', $search);
            $this->db->group_end();
        }
        return $this->db->count_all('users');
        /****Code for with pagenation ends here */
    }

    public function getCities() {
        $this->db->select('name');
        $this->db->from('cities');
        $this->db->order_by('name', 'ASC');
        $query = $this->db->get();
        
        return $query->result();
    }

    public function getAllRoles() {
        $this->db->select('role_id, role_name');
        $this->db->from('roles');
        $this->db->order_by('role_name', 'ASC');
        $query = $this->db->get();
        
        return $query->result();
    }

    public function addUsers($data) {
        $this->db->insert('users', $data);
    }
    
}

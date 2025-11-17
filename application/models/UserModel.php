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
        // Check for duplicate email or phone before inserting
    $this->db->where('email', $data['email']);
    $this->db->or_where('phone', $data['phone']);
    $query = $this->db->get('users');
    
    if ($query->num_rows() > 0) {
        throw new Exception('Duplicate entry');
    }
    
    // If no duplicates, proceed with insert
    return $this->db->insert('users', $data);
    }
    
    public function authenticate($email, $password) {
        	
    		$query = $this->db->get_where('users', array('email' => $email));
    
    		if($query->num_rows() == 1) {
        		$user = $query->row();
        		return $user->password === $password;
    		}
    
    		return false;
    	}

        // Get user by Email
    	public function getUserByEmail($email) {
        $query = $this->db->get_where('users', array('email' => $email));
        return $query->row();
    }
    
    public function getRoleNameFromRoleId($roleId)
    {
        $this->db->select('role_name');
        $this->db->from('roles');
        $this->db->where('role_id', $roleId);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $result = $query->row();
            return $result->role_name;
        }
        
        return false;
    }

}

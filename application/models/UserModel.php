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
        $this->db->select('id, name');
        $this->db->from('cities');
        $this->db->order_by('name', 'ASC');
        $query = $this->db->get();
        
        return $query->result();
    }

    public function getAllRoles() {
        $this->db->select('id, name');
        $this->db->from('roles');
        $this->db->order_by('name', 'ASC');
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

    public function updateUser($userId, $data) {
        $this->db->where('id', $userId);
        return $this->db->update('users', $data);
    }

    public function deactivateUser($userId, $status) {
    $this->db->where('id', $userId);
    return $this->db->update('users', [
        'status' => $status,
    ]);
    }
    
    public function authenticate($email, $password) {
        	
    		$query = $this->db->get_where('users', array('email' => $email));

            
    
    		if($query->num_rows() == 1) {
        		$user = $query->row();
        		//return $user->password === $password; // Use it for Plain text password comparison
                return password_verify($password, $user->password); // Verify hashed password
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
        $this->db->select('name');
        $this->db->from('roles');
        $this->db->where('id', $roleId);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $result = $query->row();
            return $result->name;
        }
        
        return false;
    }

    public function getAllUsersDataWithRoleName() {
        return $this->db->select('users.*, roles.name as roleName, cities.name as cityName')
                    ->from('users')
                    ->join('roles', 'users.roleId = roles.id')
                    ->join('cities', 'users.cityId = cities.id')
                    ->get()
                    ->result();
    }

    public function getTotalUsersCount() {
        $this->db->select('COUNT(*) as total');
        $this->db->from('users');

        $query = $this->db->get();
        return $query->row()->total;
    }    

    public function getUsersForPage($currentPage, $recordsToShowPerPage)
    {
        $getTotalRecords = $this->getTotalUsersCount();
        $totalPages = ceil($getTotalRecords / $recordsToShowPerPage);

        
        if ($currentPage >= 1 && $currentPage <= $totalPages) // Valid Current Page No Check
        {
            $this->db->select('users.*, roles.name as roleName, cities.name as cityName');
            $this->db->from('users')
            ->join('roles', 'users.roleId = roles.id')
                    ->join('cities', 'users.cityId = cities.id');
            
            $offset = ($currentPage - 1) * $recordsToShowPerPage; // records to skip then star displaying
            $this->db->limit($recordsToShowPerPage, $offset); 
            return $this->db->get()->result();
        }
        return []; // return empty array if current page is invalid like ?page=9999
    }
}

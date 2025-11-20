<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StudentModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**Function used for pagenation Function (1/2), used by controller */
    public function getPaginatedStudents($limit, $offset, $search = '') //Search parameter added for search functionality; else exclude it. Model Step (1/3)
    {

        // NEW: Add search conditions if search term exists. Model Step (2/3)
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->or_like('email', $search);
            $this->db->or_like('phone', $search);
            $this->db->group_end();
        }

        return $this->db->limit($limit, $offset)->get('students')->result(); // remains same, even when using search functionality;
    }

    /**Function used for pagenation Function (2/2), used by controller */
    public function getTotalStudents($search = '') {
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
        return $this->db->count_all('students');
        /****Code for with pagenation ends here */
    }

    public function getCities() {
        $this->db->select('name');
        $this->db->from('cities');
        $this->db->order_by('name', 'ASC');
        $query = $this->db->get();
        
        return $query->result();
    }

    public function addStudent($data) {

    // Check for duplicate email or phone before inserting
    $this->db->where('email', $data['email']);
    $this->db->or_where('phone', $data['phone']);
    $query = $this->db->get('students');
    
    if ($query->num_rows() > 0) {
        throw new Exception('Duplicate entry');
    }
    
    // If no duplicates, proceed with insert
    return $this->db->insert('students', $data);
    }

    public function getStudentById($id) {
    return $this->db->where('id', $id)->get('students')->row();
    }

    public function updateStudent($id, $data) {
        // Check for duplicate email or phone excluding current student
        $this->db->where('id !=', $id);
        $this->db->group_start();
        $this->db->where('email', $data['email']);
        $this->db->or_where('phone', $data['phone']);
        $this->db->group_end();
        $query = $this->db->get('students');
        
        if ($query->num_rows() > 0) {
            throw new Exception('Duplicate entry');
        }
        
        // If no duplicates, proceed with update
        return $this->db->where('id', $id)->update('students', $data);
    }

    public function deleteStudent($id) {
        return $this->db->where('id', $id)->delete('students');
    }

    }    


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
        $this->db->select('id, name');
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



    public function updateStudent($studentId, $data) {
        $this->db->where('id', $studentId);
        return $this->db->update('students', $data);
    }

    public function deactivateStudent($studentId, $status) {
    $this->db->where('id', $studentId);
    return $this->db->update('students', [
        'status' => $status,
    ]);
}

    /**For learning */
    public function getAllStudentsData() {
        // If not admin (roleId != 1), filter by createdByUserId
        if($this->session->userdata('roleId') != 1) {
            $this->db->where('createdByUserId', $this->session->userdata('userId'));
        }
        
        return $this->db->select('students.*, users.email as createdByEmail')
                    ->from('students')
                    ->join('users', 'students.createdByUserId = users.id')
                    ->get()
                    ->result();
    }

    public function getTotalStudentsCount() {
        $this->db->select('COUNT(*) as total');
        $this->db->from('students');
        

        // If not admin (roleId != 1), filter by createdByUserId
        if($this->session->userdata('roleId') != 1) {
            $this->db->where('createdByUserId', $this->session->userdata('userId'));
        }

        $query = $this->db->get();
        return $query->row()->total;
    }    

    public function getStudentsForPage($currentPage, $recordsToShowPerPage)
    {
        $getTotalRecords = $this->getTotalStudentsCount();
        $totalPages = ceil($getTotalRecords / $recordsToShowPerPage);

        
        if ($currentPage >= 1 && $currentPage <= $totalPages) // Valid Current Page No Check
        {
            $this->db->select('students.*, users.email as createdByEmail, cities.name as cityName');
            $this->db->from('students')
            ->join('users', 'students.createdByUserId = users.id')
            ->join('cities', 'students.cityId = cities.id');
            // If not admin (roleId != 1), filter by createdByUserId
            if($this->session->userdata('roleId') != 1) {
                $this->db->where('createdByUserId', $this->session->userdata('userId'));
            }
            $offset = ($currentPage - 1) * $recordsToShowPerPage; // records to skip then star displaying
            $this->db->limit($recordsToShowPerPage, $offset); 
            return $this->db->get()->result();
        }
        return []; // return empty array if current page is invalid like ?page=9999
    }
}

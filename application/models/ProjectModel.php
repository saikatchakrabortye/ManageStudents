<?php
defined('BASEPATH') OR exit('No direct access allowed');
class ProjectModel extends CI_Model {
    public function __construct() {
        parent:: __construct();
        $this->load->database();
    }

    public function getAllProjectsForListing()
    {
        return $this->db->select('projects.*, clients.name as clientName')
        ->from('projects')
        ->join('clients', 'projects.clientId = clients.id', 'left')
        ->get()
        ->result();
    }

    public function getProjectByPublicId($publicId)
    {
        return $this->db->select('projects.*, clients.name as clientName')
        ->from('projects')
        ->join('clients', 'projects.clientId = clients.id', 'left')
        ->where('projects.publicId', $publicId)
        ->where('projects.status', 'active')
        ->get()
        ->row();
    }

    public function getIdFromPublicId($publicId)
    {
        return $this->db->select('id')
        ->from('projects')
        ->where('publicId', $publicId)
        ->get()
        ->row();
    }

    public function addProject($data)
    {
        $this->db->where('name', $data['name']);
        $query =$this->db->get('projects');
        if ($query->num_rows() > 0)
        {
            throw new Exception("Project with same name already exists");
        }

        $this->db->insert('projects', $data);
        $projectId = $this->db->insert_id(); // insert_id() method returns auto-incremented ID from last insert operation
        $publicId = 'SYSPRJ' . date('Y'). str_pad($projectId, 6, '0', STR_PAD_LEFT); // Generate publicId
        $this->db->where('id', $projectId);
        $this->db->update('projects', ['publicId' => $publicId]);

        return $publicId;
    }

    public function updateProject($projectPublicId, $data)
    {
        $this->db->where('publicId', $projectPublicId);
        return $this->db->update('projects', $data);
    }

    public function toggleProjectStatus($projectPublicId, $status)
    {
        $this->db->where('publicId', $projectPublicId);
        return $this->db->update('projects', ['status' => $status]);
    }
}
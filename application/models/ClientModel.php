<?php
defined ('BASEPATH') OR exit('No direct access allowed');
class ClientModel extends CI_Model {
    public function __construct() {
        parent:: __construct();
        $this->load->database();
    }
    public function getAllClientsForListing()
    {
        return $this->db->select('clients.*')
        ->from('clients')
        ->get()
        ->result();
    }

    public function getAllClientsForDropdown()
    {
        return $this->db->select('clients.publicId, clients.name')
        ->from('clients')
        ->where('clients.status', 'active')
        ->get()
        ->result();
    }

    public function getClientById($publicId)
    {
        $result= $this->db->select('clients.*')
            ->from('clients')
            ->where('publicId', $publicId)
            ->get()
            ->row();
            return $result;
    }
    
    public function getIdFromPublicId($publicId)
    {
        return $this->db->select('id')
            ->from('clients')
            ->where('publicId', $publicId)
            ->get()
            ->row();
    }

    public function addClient($data)
    {
        $this->db->where('name', $data['name']);
        $query =$this->db->get('clients');
        if ($query->num_rows() > 0)
        {
            throw new Exception("Duplicate entry");
        }

        $this->db->insert('clients', $data);
        $clientId = $this->db->insert_id(); // insert_id() method returns auto-incremented ID from last insert operation
        $publicId = 'SYSCL' . date('Y'). str_pad($clientId, 6, '0', STR_PAD_LEFT); // Generate publicId
        $this->db->where('id', $clientId);
        $this->db->update('clients', ['publicId' => $publicId]);

        return $this->getClientById($publicId);
    }

    public function updateClient($clientPublicId, $data)
    {
        $clientId = $this->getIdFromPublicId($clientPublicId);
        $this->db->where('id', $clientId);
        return $this->db->update('clients', $data);
    }

    public function toggleClientStatus($clientPublicId, $status)
    {
        //$clientId = $this->getIdFromPublicId($clientPublicId);
        $this->db->where('publicId', $clientPublicId);
        return $this->db->update('clients', ['status' => $status]);
    }

}
<?php
DEFINED('BASEPATH') OR exit('No direct access allowed');
class SidebarModel extends CI_Model {
    public function __construct() {
        parent:: __construct();
        $this->load->database();
    }

    public function getAllSidebarItems()
    {
        $query = $this->db->select('name, routeKey, iconName, itemGroup')
        ->from('sidebarItems')
        ->where('status', 'active')
        ->order_by('itemGroup IS NULL', 'DESC', FALSE) // Items will NULL itemGroup comes first. Dashboard at top.
        ->order_by('itemGroup', 'ASC') // Then order the itemsGroup alphabetically
        ->order_by('sortOrder', 'ASC') // Finally order by sortOrder within each group
        ->get();

        $menuData = [
            'dashboard' => NULL, // Special case for Dashboard
            'groups' => []
        ];

        foreach ($query->result() as $item) {
            if ($item->itemGroup === NULL) {
                $menuData['dashboard'] = $item;
            } else {
                $menuData['groups'][$item->itemGroup][] = $item;
            
            }
        }

        return $menuData;
    }
}
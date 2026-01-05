<?php
DEFINED('BASEPATH') OR exit('No direct access allowed');
class SidebarModel extends CI_Model {
    public function __construct() {
        parent:: __construct();
        $this->load->database();
         $this->load->library('session');
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

        $designationId = $this->session->userdata('designationId');
        // Define access rules
        $allowedItems = [];
        
        if ($designationId == 10) {
            // Admin: show everything
            $allowedItems = ['show_all' => true];
        } else {
            // Non-admin: define what to show
            // Format: ['Group Name' => ['Item1', 'Item2']]
            // Empty array means show all items in that group
            $allowedItems = [
                'show_all' => false,
                'Account Settings' => [],  // Show all items
                'Project' => ['Employee Project Assignments', 'Efforts'],  // Show only specific item
            ];
        }

        $menuData = [
            'dashboard' => NULL, // Special case for Dashboard
            'groups' => []
        ];

       foreach ($query->result() as $item) {
        if ($item->itemGroup === NULL) {
            // Dashboard item
            if ($allowedItems['show_all'] || isset($allowedItems['dashboard'])) {
                $menuData['dashboard'] = $item;
            }
            continue;
        }
        
        // Check if this group is allowed
        if ($allowedItems['show_all'] || isset($allowedItems[$item->itemGroup])) {
            // Check if specific items are defined
            if ($allowedItems['show_all'] || empty($allowedItems[$item->itemGroup])) {
                // No specific items defined, show all in this group
                $menuData['groups'][$item->itemGroup][] = $item;
            } elseif (in_array($item->name, $allowedItems[$item->itemGroup])) {
                // This specific item is allowed
                $menuData['groups'][$item->itemGroup][] = $item;
            }
        }
    }

        return $menuData;
    }
}
<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Menu_model extends CI_Model
{
    public function getMenu($menu)
    {
        $this->db->like('menu', $menu);
        // return $this->db->get_where('user_menu', ['menu' => $menu])->row_array();
        return $this->db->get('user_menu')->row_array();
    }

    public function getMenuById($id)
    {
        return $this->db->get_where('user_menu', ['id' => $id])->row_array();
    }

    public function getMenus()
    {
        return $this->db->get('user_menu')->result_array();
    }

    public function addMenu($data)
    {
        $this->db->insert('user_menu', ['menu' => $data]);
    }

    public function editMenu($data)
    {
        $this->db->where('id', $data['id']);
        $this->db->update('user_menu', $data);
    }

    public function deleteMenu($id)
    {
        $this->db->delete('user_menu', ['id' => $id]);
    }

    public function getSubMenus()
    {
        $query = "SELECT `user_sub_menu`.*, `user_menu`.`menu`
                    FROM `user_sub_menu` JOIN `user_menu`
                    ON `user_sub_menu`.`menu_id` = `user_menu`.`id`";
        return $this->db->query($query)->result_array();
    }

    public function getSubMenuIsActive($url)
    {
        $subMenu = $this->db->get_where('user_sub_menu', ['url' => $url]);
        // Jika submenu tidak ada pada db (seperti roleaccess) maka anggap submenu tsb active
        if ($subMenu->num_rows() < 1) {
            return ['is_active' => 1];
        }

        return $subMenu->row_array()['is_active'];
    }

    public function getSubMenuById($id)
    {
        return $this->db->get_where('user_sub_menu', ['id' => $id])->row_array();
    }

    public function addSubMenu($data)
    {
        $this->db->insert('user_sub_menu', $data);
    }

    public function editSubMenu($data)
    {
        $this->db->where('id', $data['id']);
        $this->db->update('user_sub_menu', $data);
    }

    public function deleteSubMenu($id)
    {
        $this->db->delete('user_sub_menu', ['id' => $id]);
    }

    public function userAccess($role_id, $menu_id)
    {
        return $this->db->get_where('user_access_menu', ['role_id' => $role_id, 'menu_id' => $menu_id]);
    }
}

<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model
{
    public function addUser($data)
    {
        $this->db->insert('user', $data);
    }

    public function deleteUser($email)
    {
        $this->db->delete('user', ['email' => $email]);
    }

    public function updateUser($email)
    {
        $this->db->set('is_active', 1);
        $this->db->where('email', $email);
        $this->db->update('user');
    }

    public function getUser($email, $table = 'user')
    {
        return $this->db->get_where($table, ['email' => $email])->row_array();
    }

    public function getActiveUser($email)
    {
        return $this->db->get_where('user', ['email' => $email, 'is_active' => 1])->row_array();
    }

    // Untuk menu pada sidebar
    public function getSideMenu($role_id)
    {
        $queryMenu = "SELECT `user_menu`.`id`, `menu` 
                    FROM `user_menu` JOIN `user_access_menu` 
                    ON `user_menu`.`id` = `user_access_menu`.`menu_id` 
                    WHERE `user_access_menu`.`role_id` = $role_id 
                    ORDER BY `user_access_menu`.`menu_id` ASC";
        return $this->db->query($queryMenu)->result_array();
    }

    // Untuk submenu pada sidebar
    public function getSideSubMenu($menu)
    {
        $subMenu = [];
        foreach ($menu as $m) {
            $menuId = $m['id'];
            $querySubMenu = "SELECT * 
                            FROM `user_sub_menu`
                            WHERE `menu_id` = $menuId
                            AND `is_active` = 1";

            array_push($subMenu, $this->db->query($querySubMenu)->result_array());
        }
        return $subMenu;
    }

    public function getRoles()
    {
        return $this->db->get('user_role')->result_array();
    }

    public function getRole($id)
    {
        return $this->db->get_where('user_role', ['id' => $id])->row_array();
    }

    public function addRole($data)
    {
        $this->db->insert('user_role', $data);
    }

    public function accessMenu($role_id, $menu_id)
    {
        $this->db->where('role_id', $role_id);
        $this->db->where('menu_id', $menu_id);
        return $this->db->get('user_access_menu');
    }

    public function getAccess($data)
    {
        return $this->db->get_where('user_access_menu', $data);
    }

    public function addAccess($data)
    {
        $this->db->insert('user_access_menu', $data);
    }

    public function deleteAccess($data)
    {
        $this->db->delete('user_access_menu', $data);
    }

    public function editProfile($name, $email, $image)
    {
        $this->db->set('name', $name);
        $this->db->set('image', $image);
        $this->db->where('email', $email);
        $this->db->update('user');
    }

    public function updatePassword($pw, $email)
    {
        $this->db->set('password', $pw);
        $this->db->where('email', $email);
        $this->db->update('user');
    }

    public function insertToken($data)
    {
        $this->db->insert('user_token', $data);
    }

    public function getUserByToken($token)
    {
        return $this->db->get_where('user_token', ['token' => $token])->row_array();
    }

    public function deleteToken($email)
    {
        $this->db->delete('user_token', ['email' => $email]);
    }
}

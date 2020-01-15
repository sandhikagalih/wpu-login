<?php 
defined('BASEPATH') or exit('No direct script access allowed');

class Menu_model extends CI_Model
{
    public function getSubMenu()
    {
        $query = "SELECT `user_sub_menu`.*, `user_menu`.`menu`
                  FROM `user_sub_menu` JOIN `user_menu`
                  ON `user_sub_menu`.`menu_id` = `user_menu`.`id`
                  ORDER BY `menu` desc
                ";
        return $this->db->query($query)->result_array();
    }

    public function saveSubMenu($id)
    {
        $data = array(
            'menu_id' => $this->input->post('menu_id') ,
            'title' => $this->input->post('title'),
            'url' => $this->input->post('url'),
            'icon' => $this->input->post('icon'),
            'is_active' => $this->input->post('is_active')
        );
        $this->db->where('id', $id);
        $this->db->update('user_sub_menu', $data);

    }

    public function deleteSubMenu($id)
    {
        $this->db->delete('user_sub_menu',['id' => $id]); 
    }
}

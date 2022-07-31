<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Menu extends CI_Controller
{
    private $user, $menu, $subMenu;

    public function __construct()
    {
        parent::__construct();
        is_logged_in();

        $this->load->model('User_model');
        $this->load->model('Menu_model');
        $this->user = $this->User_model->getUser($this->session->userdata('email'));
        $this->menu = $this->User_model->getSideMenu($this->session->userdata('role_id'));
        $this->subMenu = $this->User_model->getSideSubMenu($this->menu);
    }

    public function index()
    {
        $data['user'] = $this->user;
        $data['menu'] = $this->menu;
        $data['subMenu'] = $this->subMenu;

        $data['title'] = 'Menu Management';
        $data['menuManage'] = $this->Menu_model->getMenus();

        $this->form_validation->set_rules('menu', 'Menu', 'required');

        if (!$this->form_validation->run()) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('templates/footer');
        } else {
            $this->Menu_model->addMenu($this->input->post('menu'));
            $this->session->set_flashdata('alert-success', 'New menu has been added!');
            redirect('menu');
        }
    }

    public function submenu()
    {
        $data['user'] = $this->user;
        $data['menu'] = $this->menu;
        $data['subMenu'] = $this->subMenu;

        $data['title'] = 'Sub Menu Management';
        $data['menuManage'] = $this->Menu_model->getMenus();
        $data['subMenuManage'] = $this->Menu_model->getSubMenus();

        $this->form_validation->set_rules('title', 'Title', 'required');
        $this->form_validation->set_rules('menu_id', 'Menu', 'required');
        $this->form_validation->set_rules('url', 'URL', 'required');
        $this->form_validation->set_rules('icon', 'Icon', 'required');

        if (!$this->form_validation->run()) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('menu/submenu/index', $data);
            $this->load->view('templates/footer');
        } else {
            $data = [
                'title' => $this->input->post('title'),
                'menu_id' => $this->input->post('menu_id'),
                'url' => $this->input->post('url'),
                'icon' => $this->input->post('icon'),
                'is_active' => (is_null($this->input->post('is_active'))) ? 0 : 1
            ];
            $this->Menu_model->addSubMenu($data);
            $this->session->set_flashdata('alert-success', 'New sub menu has been added!');
            redirect('menu/submenu');
        }
    }

    public function edit()
    {
        $type = $this->input->get('type');
        $id = $this->input->get('id');

        $data['user'] = $this->user;
        $data['menu'] = $this->menu;
        $data['subMenu'] = $this->subMenu;

        $this->form_validation->set_rules('title', 'Title', 'required');

        if ($type == 'submenu') {
            $this->form_validation->set_rules('menu_id', 'Menu', 'required');
            $this->form_validation->set_rules('url', 'URL', 'required');
            $this->form_validation->set_rules('icon', 'Icon', 'required');
        }

        if (!$this->form_validation->run()) {
            $data['title'] = 'Edit ' . ucfirst($type);

            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);

            if ($type == 'menu' && is_numeric($id)) {
                $data['editMenu'] = $this->Menu_model->getMenuById($id);
                $this->load->view('menu/edit', $data);
            } elseif ($type == 'submenu' && is_numeric($id)) {
                $data['editSub'] = $this->Menu_model->getSubMenuById($id); // Get 1 submenu by id
                $data['menuManage'] = $this->Menu_model->getMenus(); // Get semua menu
                $this->load->view('menu/submenu/edit', $data);
            } else {
                redirect('auth/blocked');
            }

            $this->load->view('templates/footer');
        } else {
            if ($type == 'menu') {
                $data = [
                    'id' => $this->input->post('id'),
                    'menu' => $this->input->post('title')
                ];
                $this->Menu_model->editMenu($data);
                $this->session->set_flashdata('alert-success', 'Menu changed successfully');
                redirect('menu');
            } elseif ($type == 'submenu') {
                $post = $this->input->post();
                $data = [
                    'id' => $post['id'],
                    'menu_id' => $post['menu_id'],
                    'title' => $post['title'],
                    'url' => $post['url'],
                    'icon' => $post['icon'],
                    'is_active' => ($id <= 2) ? 1 : $post['is_active']
                    // Menu Dashboard dan My Profile harus selalu active
                ];
                $this->Menu_model->editSubMenu($data);
                $this->session->set_flashdata('alert-success', 'Sub menu changed successfully');
                redirect('menu/submenu');
            }
        }
    }

    public function delete()
    {
        $type = $this->input->get('type');
        $id = $this->input->get('id');

        if ($type == 'menu' && is_numeric($id)) {
            // Menu admin, user, dan menu di database memiliki id 1,2,3 (database) dan menu tersebut tidak boleh dihapus
            if ($id <= 3) {
                $this->session->set_flashdata('alert-danger', 'Menu cannot be deleted');
                redirect('menu');
            }
            $this->Menu_model->deleteMenu($id);
            $this->session->set_flashdata('alert-success', 'Menu has been deleted');
            redirect('menu');
        } elseif ($type == 'sub' && is_numeric($id)) {
            // Submenu dashboard, my profile, edit profile, menu management, submenu management, role, dan change password memiliki id 1,2,3,4,5,11,12 (database) dan submenu tersebut tidak boleh dihapus
            if ($id <= 5 || $id == 11 || $id == 12) {
                $this->session->set_flashdata('alert-danger', 'SubMenu cannot be deleted');
                redirect('menu/submenu');
            }
            $this->Menu_model->deleteSubMenu($id);
            $this->session->set_flashdata('alert-success', 'Sub menu has been deleted');
            redirect('menu/submenu');
        } else {
            redirect('auth/blocked');
        }
    }
}

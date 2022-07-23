<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{
    private $user, $menu, $subMenu;

    public function __construct()
    {
        parent::__construct();
        is_logged_in();

        $this->load->model('User_model');
        $this->user = $this->User_model->getUser($this->session->userdata('email'));
        $this->menu = $this->User_model->getSideMenu($this->session->userdata('role_id'));
        $this->subMenu = $this->User_model->getSideSubMenu($this->menu);
    }

    public function index()
    {
        $data['title'] = 'Dashboard';
        $data['user'] = $this->user;
        $data['menu'] = $this->menu;
        $data['subMenu'] = $this->subMenu;

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('admin/index', $data);
        $this->load->view('templates/footer');
    }

    public function role()
    {
        $data['title'] = 'Role';
        $data['user'] = $this->user;
        $data['menu'] = $this->menu;
        $data['subMenu'] = $this->subMenu;

        $data['role'] = $this->User_model->getRoles();

        $this->form_validation->set_rules('role', 'Role', 'required');

        if (!$this->form_validation->run()) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('admin/role', $data);
            $this->load->view('templates/footer');
        } else {
            $data = [
                'role' => $this->input->post('role')
            ];
            $this->User_model->addRole($data);
            $this->session->set_flashdata('alert-success', 'Role has been added');
            redirect('admin/role');
        }
    }

    public function editRole($id = null)
    {
        if (is_null($id)) {
            redirect('admin/role');
        }

        $data['title'] = 'Edit Role';
        $data['user'] = $this->user;
        $data['menu'] = $this->menu;
        $data['subMenu'] = $this->subMenu;

        $data['role'] = $this->User_model->getRole($id);

        $this->form_validation->set_rules('role', 'Role Name', 'required');

        if (!$this->form_validation->run()) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('admin/edit-role', $data);
            $this->load->view('templates/footer');
        } else {
            $data = [
                'id' => $this->input->post('id'),
                'role' => $this->input->post('role')
            ];
            $this->User_model->editRole($data);
            $this->session->set_flashdata('alert-success', 'Role has been edited');
            redirect('admin/role');
        }
    }

    public function deleteRole($id = null)
    {
        // Jika id = null
        if ($id <= 2) {
            // dan jika id = 1 atau 2 (Role admin dan member tidak boleh dihapus)
            if ($id > 0) {
                $this->session->set_flashdata('alert-danger', 'This role cannot be deleted');
            }
            redirect('admin/role');
        }

        $this->User_model->deleteRole($id);
        $this->session->set_flashdata('alert-success', 'Role has been deleted');
        redirect('admin/role');
    }

    public function roleAccess($role_id = null)
    {
        if (is_null($role_id)) {
            redirect('auth/blocked');
            die();
        }

        $data['title'] = 'Role Access';
        $data['user'] = $this->user;
        $data['menu'] = $this->menu;
        $data['subMenu'] = $this->subMenu;

        $this->load->model('Menu_model');
        $data['role'] = $this->User_model->getRole($role_id);
        $data['menus'] = $this->Menu_model->getMenus();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('admin/role-access', $data);
        $this->load->view('templates/footer');
    }

    public function changeAccess()
    {
        if (empty($this->input->post())) {
            redirect('auth/blocked');
        }

        $menuId = $this->input->post('menuId');
        $roleId = $this->input->post('roleId');

        $data = [
            'role_id' => $roleId,
            'menu_id' => $menuId
        ];

        $result = $this->User_model->getAccess($data);

        if ($result->num_rows() < 1) {
            $this->User_model->addAccess($data);
        } else {
            $this->User_model->deleteAccess($data);
        }

        $this->session->set_flashdata('message', 'changed');
    }
}

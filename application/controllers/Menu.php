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
                'is_active' => $this->input->post('is_active')
            ];
            $this->Menu_model->addSubMenu($data);
            $this->session->set_flashdata('alert-success', 'New sub menu has been added!');
            redirect('menu/submenu');
        }
    }
}

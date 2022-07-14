<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
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
        $data['title'] = 'My Profile';
        $data['user'] = $this->user;
        $data['menu'] = $this->menu;
        $data['subMenu'] = $this->subMenu;

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('user/index', $data);
        $this->load->view('templates/footer');
    }

    public function edit()
    {
        $data['title'] = 'Edit Profile';
        $data['user'] = $this->user;
        $data['menu'] = $this->menu;
        $data['subMenu'] = $this->subMenu;

        $this->form_validation->set_rules('name', 'Full Name', 'required|trim');

        if (!$this->form_validation->run()) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('user/edit', $data);
            $this->load->view('templates/footer');
        } else {
            $name = $this->input->post('name');
            $email = $this->input->post('email');

            // cek jika ada gambar yang akan diupload
            $image = $_FILES['image']['name'];

            if ($image) {
                $config['allowed_types'] = 'gif|jpg|png';
                $config['max_size']      = '4096';
                $config['upload_path'] = './assets/img/profile/';

                $this->load->library('upload', $config);

                // dan jika berhasil diupload
                if ($this->upload->do_upload('image')) {
                    $old_image = $data['user']['image'];
                    if ($old_image != 'default.png') {
                        unlink(FCPATH . 'assets/img/profile/' . $old_image);
                    }
                    $new_image = $this->upload->data('file_name');
                    $this->db->set('image', $new_image);
                } else {
                    $this->session->set_flashdata('message', $this->upload->display_errors());
                    redirect('user/edit');
                    die();
                }
            }

            $this->User_model->editProfile($name, $email, $new_image);

            $this->session->set_flashdata('message', 'edited');
            redirect('user');
        }
    }

    public function changePassword()
    {
        $data['title'] = 'Change Password';
        $data['user'] = $this->user;
        $data['menu'] = $this->menu;
        $data['subMenu'] = $this->subMenu;

        $this->form_validation->set_rules('current_password', 'Current Password', 'required|trim');
        $this->form_validation->set_rules('new_password1', 'New Password', 'required|trim|min_length[8]|matches[new_password2]');
        $this->form_validation->set_rules('new_password2', 'Confirm New Password', 'required|trim|min_length[8]|matches[new_password1]');

        if (!$this->form_validation->run()) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('user/changepassword', $data);
            $this->load->view('templates/footer');
        } else {
            $currPw = $this->input->post('current_password');
            $newPw = $this->input->post('new_password1');

            if (!password_verify($currPw, $this->user['password'])) {
                $this->session->set_flashdata('alert-danger', 'Wrong Current Password!');
                redirect('user/changePassword');
            } else {
                if ($currPw == $newPw) {
                    $this->session->set_flashdata('alert-danger', 'The new password cannot be the same as the old password!');
                    redirect('user/changePassword');
                } else {
                    // Password ok
                    $hashPw = password_hash($newPw, PASSWORD_DEFAULT);
                    $this->User_model->updatePassword($hashPw, $this->session->userdata('email'));

                    $this->session->set_flashdata('alert-success', 'Password changed!');
                    redirect('user/changePassword');
                }
            }
        }
    }
}

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
            $deletePic = $this->input->post('deletePic');
            $old_image = $this->user['image'];

            // Jika delete current picture di check
            if (!is_null($deletePic)) {
                unlink(FCPATH . 'assets/img/profile/' . $old_image);
                $old_image = 'default.png';
            }

            // cek jika ada gambar yang akan diupload
            $image = $_FILES['image']['name'];

            if ($image) {
                // Generate random image name
                $this->load->helper('string');
                $config['file_name'] = random_string('alnum', 16);

                $config['allowed_types'] = 'gif|jpg|png';
                $config['max_size']      = '4096';
                // Kita upload di folder resize untuk di resize
                $config['upload_path'] = './assets/img/profile/resize/';

                $this->load->library('upload', $config);

                // dan jika berhasil diupload
                if ($this->upload->do_upload('image')) {
                    $old_image = $data['user']['image'];
                    if ($old_image != 'default.png') {
                        unlink(FCPATH . 'assets/img/profile/' . $old_image);
                    }

                    // Nama gambar sudah random
                    $new_image = $this->upload->data('file_name');

                    // Config untuk resize image
                    $configImg['source_image'] = $config['upload_path'] . $new_image; // path image yang diupload (sesuai $config[upload path])
                    $configImg['new_image'] = './assets/img/profile/' . $new_image; // path baru untuk menyimpan gambar yang sudah diresize
                    $configImg['quality'] = 100;
                    $configImg['maintain_ratio'] = TRUE;
                    $configImg['width'] = 250;
                    $configImg['height'] = 250;

                    $this->load->library('image_lib', $configImg);
                    $this->image_lib->resize(); // Resize gambar

                    // Jika sudah resize
                    if ($this->image_lib->resize()) {
                        // maka hapus foto original
                        unlink(FCPATH . 'assets/img/profile/resize/' . $new_image);
                    } else {
                        // tapi jika error dalam resizing
                        echo $this->image_lib->display_errors();
                        die();
                    }
                } else {
                    $this->session->set_flashdata('message', $this->upload->display_errors());
                    redirect('user/edit');
                    die();
                }
            }

            if (isset($new_image)) {
                // Jika user input gambar baru
                $this->User_model->editProfile($name, $email, $new_image);
            } else {
                // Jika user tidak input gambar baru (pakai gambar lama)
                $this->User_model->editProfile($name, $email, $old_image);
            }

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

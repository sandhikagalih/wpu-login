<?php

// str_contains hanya berfungsi di php8, untuk php7 bisa diganti menjadi str_pos arau preg_match. Detail ada pada README

function is_logged_in()
{
    $ci = get_instance();
    $menu = $ci->uri->segment(1);

    // Jika belum login
    if (!$ci->session->userdata('email')) {
        // dan jika user mengakses halaman selain login dan registration
        if (!str_contains($menu, 'auth')) {
            redirect('auth');
            die();
        }
        // Jika sudah login
    } else {
        $role_id = $ci->session->userdata('role_id');

        // dan jika user malah mengakses halaman login atau registration maka diredirect sesuai role id
        if (str_contains($menu, 'auth') || is_null($menu)) {
            switch ($role_id) {
                case 1:
                    redirect('admin');
                    die();
                    break;

                case 2:
                    redirect('user');
                    die();
                    break;
            }
        }

        $ci->load->model('Menu_model');
        $menu_id = $ci->Menu_model->getMenu($menu)['id'];
        $userAccess = $ci->Menu_model->userAccess($role_id, $menu_id);

        // Jika user mengakses menu yang seharusnya tidak boleh
        if ($userAccess->num_rows() < 1) {
            redirect('auth/blocked');
            die();
        }
    }
}

function check_access($role_id, $menu_id)
{
    $ci = get_instance();

    $ci->load->model('User_model');
    $result = $ci->User_model->accessMenu($role_id, $menu_id);

    if ($result->num_rows() > 0) {
        return 'checked';
    }
}

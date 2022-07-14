<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        is_logged_in();
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');

        if (!$this->form_validation->run()) {
            $data['title'] = 'Login Page';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/login');
            $this->load->view('templates/auth_footer');
        } else {
            // validasinya success
            $this->_login();
        }
    }

    private function _login()
    {
        $email = $this->input->post('email');
        $password = $this->input->post('password');

        $user = $this->User_model->getUser($email);

        // jika usernya aada
        if ($user) {
            // jika usernya aktif
            if ($user['is_active'] == 1) {
                // cek password
                if (password_verify($password, $user['password'])) {
                    $data = [
                        'email' => $user['email'],
                        'role_id' => $user['role_id']
                    ];
                    $this->session->set_userdata($data);

                    if ($user['role_id'] == 1) {
                        redirect('admin');
                    } else {
                        redirect('user');
                    }
                } else {
                    $this->session->set_flashdata('alert-danger', 'Wrong Password!');
                    redirect('auth');
                }
            } else {
                $this->session->set_flashdata('alert-danger', 'This email has not been activated!');
                redirect('auth');
            }
        } else {
            $this->session->set_flashdata('alert-danger', 'Email is not registered!');
            redirect('auth');
        }
    }

    public function registration()
    {
        is_logged_in();

        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[user.email]', [
            'is_unique' => 'Email has already registered!'
        ]);
        $this->form_validation->set_rules('password1', 'Password', 'required|trim|min_length[8]|matches[password2]', [
            'matches' => 'Password dont match!',
            'min_length' => 'Password too short!'
        ]);
        $this->form_validation->set_rules('password2', 'Password', 'required|trim|matches[password1]');

        if (!$this->form_validation->run()) {
            $data['title'] = 'WPU User Registration';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/registration');
            $this->load->view('templates/auth_footer');
        } else {
            $email = $this->input->post('email', true);
            $data = [
                'name' => htmlspecialchars($this->input->post('name', true)),
                'email' => htmlspecialchars($email),
                'image' => 'default.jpg',
                'password' => password_hash($this->input->post('password1'), PASSWORD_DEFAULT),
                'role_id' => 2,
                'is_active' => 0,
                'date_created' => time()
            ];

            // Siapkan token
            $token = base64_encode(random_bytes(32));
            $user_token = [
                'email' => $email,
                'token' => $token,
                'date_created' => time()
            ];

            $this->User_model->addUser($data);
            $this->User_model->insertToken($user_token);

            $this->_sendEmail($token, 'verify');

            $this->session->set_flashdata('alert-success', 'Your account has been created. Please Activate Your Account!');
            redirect('auth');
        }
    }

    private function _sendEmail($token, $type)
    {
        $config = [
            'protocol'  => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_user' => 'your-email@mail.com',
            'smtp_pass' => 'your-app-pass',
            'smtp_port' => 465,
            'mailtype'  => 'html',
            'charset'   => 'utf-8',
            'newline'   => "\r\n"
        ];

        $this->email->initialize($config);

        $this->email->from('your-email@gmail.com', 'WPU Admin Pro');
        $this->email->to($this->input->post('email'));

        if ($type == 'verify') {
            $this->email->subject('Account Verification');
            $this->email->message('<h1 style="text-align: center;">Click this button to verify your account</h1>
            <br /> 
            <div style="width:100%;display:flex;align-items:center;justify-content:center">
                <a style="width:200px;height:100px;background-color:#4E73DF;padding:10px 25px;margin:auto;text-decoration:none;color:white;border: 3px solid black;border-radius:10px;font-size:24px;font-weight:bold;text-align:center;line-height:100px;" href="' . base_url() . 'auth/verify?email=' . $this->input->post('email') . '&token=' . urlencode($token) . '">
                    Verify
                </a>
            </div>');
        } else if ($type == 'forgot') {
            $this->email->subject('Reset Password');
            $this->email->message('<h1 style="text-align: center;">Click this button to reset your password</h1>
            <br /> 
            <div style="width:100%;display:flex;align-items:center;justify-content:center">
                <a style="width:200px;height:100px;background-color:#4E73DF;padding:10px 25px;margin:auto;text-decoration:none;color:white;border: 3px solid black;border-radius:10px;font-size:24px;font-weight:bold;text-align:center;line-height:100px;" href="' . base_url() . 'auth/resetpassword?email=' . $this->input->post('email') . '&token=' . urlencode($token) . '">
                    Reset
                </a>
            </div>');
        }

        if ($this->email->send()) {
            return true;
        } else {
            echo $this->email->print_debugger();
            die;
        }
    }

    public function verify()
    {
        $email = $this->input->get('email');
        $token = $this->input->get('token');

        $user = $this->User_model->getUser($email);
        // Jika usernya ada
        if ($user) {
            $user_token = $this->User_model->getUserByToken($token);

            // Jika tokennya ada
            if ($user_token) {
                // dan jika token masih valid
                if (time() - $user_token['date_created'] < (60 * 60 * 24)) {
                    $this->User_model->updateUser($email);
                    $this->User_model->deleteToken($email);

                    $this->session->set_flashdata('alert-success', "${email} has been activated! Please login.");
                    redirect('auth');
                } else {
                    $this->User_model->deleteUser($email);
                    $this->User_model->deleteToken($email);

                    $this->session->set_flashdata('alert-danger', 'Account activation failed! Token expired, please fill registration form again.');
                    redirect('auth/registration');
                }
            } else {
                $this->session->set_flashdata('alert-danger', 'Account activation failed! Invalid token');
                redirect('auth');
            }
        } else {
            $this->session->set_flashdata('alert-danger', 'Account activation failed! Wrong email');
            redirect('auth');
        }
    }

    public function logout()
    {
        if (!$this->session->userdata('email')) {
            redirect('auth');
        }

        $this->session->unset_userdata('email');
        $this->session->unset_userdata('role_id');

        $this->session->set_flashdata('alert-success', 'You have been logged out!');
        redirect('auth');
        $this->session->sess_destroy();
    }

    public function blocked()
    {
        $data['title'] = 'Access Blocked';
        $this->load->view('templates/header', $data);
        $this->load->view('auth/blocked');
        $this->load->view('templates/footer', $data);
    }

    public function forgotPassword()
    {
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');

        if (!$this->form_validation->run()) {
            $data['title'] = 'Forgot Password';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/forgot-password');
            $this->load->view('templates/auth_footer');
        } else {
            $email = $this->input->post('email');
            $user = $this->User_model->getActiveUser($email);

            if ($user) {
                $token = base64_encode(random_bytes(32));
                $user_token = [
                    'email' => $email,
                    'token' => $token,
                    'date_created' => time()
                ];

                $this->User_model->insertToken($user_token);
                $this->_sendEmail($token, 'forgot');

                $this->session->set_flashdata('alert-success', 'Please check your email to reset your password!');
                redirect('auth/forgotPassword');
            } else {
                $this->session->set_flashdata('alert-danger', 'Email is not registered or activated!');
                redirect('auth/forgotPassword');
            }
        }
    }

    public function resetPassword()
    {
        $email = $this->input->get('email');
        $token = $this->input->get('token');
        $user = $this->User_model->getUser($email, 'user_token');

        // Jika email user ada di tabel user_token
        if ($user) {
            $user_token = $this->User_model->getUserByToken($token);

            // dan jika tokennya benar
            if ($user_token) {
                if (time() - $user_token['date_created'] < 60 * 60 * 24) {
                    $this->session->set_userdata('reset_email', $email);
                    $this->changePassword();
                } else {
                    $this->session->set_flashdata('alert-danger', 'Token expired! Please re-enter your email for new token');
                    redirect('auth/forgotPassword');
                }
            } else {
                $this->session->set_flashdata('alert-danger', 'Reset password failed! Invalid Token');
                redirect('auth/forgotPassword');
            }
        } else {
            $this->session->set_flashdata('alert-danger', 'Reset password failed! Invalid Email');
            redirect('auth/forgotPassword');
        }
    }

    public function changePassword()
    {
        if (!$this->session->userdata('reset_email')) {
            redirect('auth');
            die();
        }

        $this->form_validation->set_rules('password1', 'Password', 'trim|required|min_length[8]|matches[password2]');
        $this->form_validation->set_rules('password2', 'Repeat Password', 'trim|required|min_length[8]|matches[password1]');

        if (!$this->form_validation->run()) {
            $data['title'] = 'Change Password';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/change-password');
            $this->load->view('templates/auth_footer');
        } else {
            $password = password_hash($this->input->post('password1'), PASSWORD_DEFAULT);
            $email = $this->session->userdata('reset_email');

            $this->User_model->updatePassword($password, $email);
            $this->User_model->deleteToken($email);
            $this->session->unset_userdata('reset_email');

            $this->session->set_flashdata('alert-success', 'Password has been changed! Please Login');
            redirect('auth');
        }
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Login
 * @property UserModel $user
 * @property UserTokenModel $userToken
 */
class Login extends App_Controller
{
    protected $layout = 'layouts/auth';

    /**
     * Login constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('UserModel', 'user');
        $this->load->model('UserTokenModel', 'userToken');
    }

    /**
     * Show default login page.
     */
    public function index()
    {
        if (_is_method('post')) {
            if($this->validate()) {
                $username = $this->input->post('username');
                $password = $this->input->post('password');
                $remember = $this->input->post('remember');

                $authenticated = $this->user->authenticate($username, $password);

                if ($authenticated === UserModel::STATUS_PENDING || $authenticated === UserModel::STATUS_SUSPENDED) {
                    flash('danger', 'Your account has status ' . $authenticated );
                } else {
                    if ($authenticated) {
                        if ($remember || $remember == 'on') {
                            $loggedEmail = UserModel::loginData('email');
                            $token = $this->userToken->create($loggedEmail, UserTokenModel::TOKEN_REMEMBER);

                            if ($token) {
                                set_cookie('remember_token', $token, 3600 * 24 * 30);
                                $this->session->set_userdata([
                                    'auth.remember_me' => true,
                                    'auth.remember_token' => $token
                                ]);
                            } else {
                                flash('danger', 'Failed create remember token.');
                            }
                        }

                        $intended = $this->input->get('redirect');
                        if(empty($intended)) {
                            redirect("dashboard");
                        }
                        redirect(urldecode($intended));
                    } else {
                        flash('danger', 'Username and password mismatch.');
                    }
                }
            }
        }

        $this->render('auth/login');
    }

    /**
     * Return validation rules.
     *
     * @return array
     */
    protected function _validation_rules()
    {
        return [
            'username' => 'trim|required',
            'password' => 'trim|required'
        ];
    }

}

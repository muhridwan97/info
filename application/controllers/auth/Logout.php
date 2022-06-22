<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Logout
 * @property UserModel $user
 * @property UserTokenModel $userToken
 */
class Logout extends App_Controller
{
    /**
     * Logout constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('UserModel', 'user');
        $this->load->model('UserTokenModel', 'userToken');

        $this->setFilterMethods([
            'index' => 'POST|PUT'
        ]);
    }

    /**
     * Signing out logged user.
     */
    public function index()
    {
        if ($this->user->logout()) {
            $rememberToken = get_cookie('remember_token');
            if (!empty($rememberToken)) {
                delete_cookie('remember_token');
                $this->userToken->delete($rememberToken);
            }
        }
        redirect('auth/login');
    }

}

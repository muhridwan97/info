<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Account
 * @property UserModel $user
 * @property Uploader $uploader
 */
class Account extends App_Controller
{
    /**
     * Account constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('UserModel', 'user');
        $this->load->model('modules/Uploader', 'uploader');
    }

    /**
     * Set validation rules.
     *
     * @return array
     */
    protected function _validation_rules()
    {
        $userId = UserModel::loginData('id');
        return [
            'name' => 'trim|required|max_length[50]',
            'username' => [
                'trim', 'required', 'max_length[50]', ['username_exists', function ($username) use ($userId) {
                    $this->form_validation->set_message('username_exists', 'The %s has been registered before, try another');
                    return $this->user->isUniqueUsername($username, $userId);
                }]
            ],
            'email' => [
                'trim', 'required', 'valid_email', 'max_length[50]', ['email_exists', function ($username) use ($userId) {
                    $this->form_validation->set_message('email_exists', 'The %s has been registered before, try another');
                    return $this->user->isUniqueEmail($username, $userId);
                }]
            ],
            'password' => [
                'trim', 'required', ['match_password', function ($password) use ($userId) {
                    $user = $this->user->getById($userId);
                    if (password_verify($password, $user['password'])) {
                        return true;
                    }
                    $this->form_validation->set_message('match_password', 'The %s mismatch with your password');
                    return false;
                }]
            ],
            'new_password' => 'min_length[6]|max_length[50]',
            'confirm_password' => 'matches[new_password]'
        ];
    }

    /**
     * Show account preferences.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_ACCOUNT_EDIT);

        $user = $this->user->getById(UserModel::loginData('id'));

        if (_is_method('put')) {
            if ($this->validate()) {
                $name = $this->input->post('name');
                $username = $this->input->post('username');
                $email = $this->input->post('email');
                $newPassword = $this->input->post('new_password');

                $dataAccount = [
                    'name' => $name,
                    'username' => $username,
                    'email' => $email
                ];

                if (!empty($_FILES['avatar']['name'])) {
                    $options = ['destination' => 'avatars/' . date('Y/m')];
                    if ($this->config->item('sso_enable')) {
                        $options['base_path'] = env('SSO_STORAGE_PATH');
                    }
                    if ($this->uploader->uploadTo('avatar', $options)) {
                        $uploadedData = $this->uploader->getUploadedData();
                        $dataAccount['avatar'] = $uploadedData['uploaded_path'];
                        if (!empty($user['avatar'])) {
                            $this->uploader->delete($user['avatar']);
                        }
                    } else {
                        flash('danger', $this->uploader->getDisplayErrors(), 'account');
                    }
                }

                if (!empty($newPassword)) {
                    $dataAccount['password'] = password_hash($newPassword, CRYPT_BLOWFISH);
                }

                $userId = UserModel::loginData('id');
                $update = $this->user->update($dataAccount, $userId);

                if ($update) {
                    flash('success', 'Your account was successfully updated', 'account');
                } else {
                    flash('danger', 'Update account failed, try again or contact administrator');
                }
            }
        }

        $this->render('account/index', compact('user'));
    }

}
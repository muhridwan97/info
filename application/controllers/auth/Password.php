<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Password
 * @property UserModel $user
 * @property UserTokenModel $userToken
 * @property Mailer $mailer
 */
class Password extends App_Controller
{
    protected $layout = 'layouts/auth';

    /**
     * Password constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('UserModel', 'user');
        $this->load->model('UserTokenModel', 'userToken');
        $this->load->model('modules/Mailer', 'mailer');

        $this->setFilterMethods([
            'forgot_password' => 'GET|POST',
            'reset' => 'GET|POST'
        ]);
    }

    /**
     * Show forgot password form.
     */
    public function forgot_password()
    {
        if (_is_method('post')) {
            $isValid = $this->validate([
                'email' => [
                    'trim', 'required', 'valid_email', 'max_length[50]', [
                        'email_exist', function ($email) {
                            $this->form_validation->set_message('email_exist', 'The email is not registered in our system');
                            return !$this->user->isUniqueEmail($email);
                        }
                    ]
                ]
            ]);

            if ($isValid) {
                $email = $this->input->post('email');

                $token = $this->userToken->create($email, UserTokenModel::TOKEN_PASSWORD);
                $user = $this->user->getBy(['prv_users.email' => $email], true);

                if (!$token || empty($user)) {
                    flash('danger', 'Failed to create reset password token.');
                } else {
                    $emailTemplate = 'emails/reset_password';
                    $emailData = [
                        'user' => $user,
                        'token' => $token
                    ];
                    $sendEmail = $this->mailer->send($email, 'Reset Password', $emailTemplate, $emailData);

                    if ($sendEmail) {
                        flash('success', "We have sent email {$email} token to reset your password", 'auth/login');
                    }

                    flash('warning', 'Send email token to reset password failed.');
                }
            }
        }

        $this->render('auth/forgot', ['title' => 'Forgot Password']);
    }

    /**
     * Recover password.
     * @param $token
     */
    public function reset($token)
    {
        if (_is_method('post')) {
            $isValid = $this->validate([
                'email' => 'trim|required|valid_email|max_length[50]',
                'new_password' => 'trim|required|min_length[5]|max_length[50]',
                'confirm_password' => 'trim|required|matches[new_password]',
            ]);

            if ($isValid) {
                $email = $this->input->post('email');
                $newPassword = $this->input->post('new_password');

                $emailToken = $this->userToken->verifyToken($token, UserTokenModel::TOKEN_PASSWORD);
                if ($email != $emailToken) {
                    flash('danger', 'Token is mismatch with email');
                } else {
                    $this->db->trans_start();

                    $this->user->update([
                        'password' => password_hash($newPassword, CRYPT_BLOWFISH)
                    ], ['email' => $email]);

                    $this->userToken->delete($token);

                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE) {
                        flash('danger', 'Transaction failed, try again or contact our administrator.');
                    } else {
                        $user = $this->user->getBy(['prv_users.email' => $email], true);

                        $this->mailer->send($email, 'Password Recovered', 'emails/password_recovered', ['user' => $user]);

                        flash('success', 'Your password is recovered.', 'auth/login');
                    }
                }
            }
        }

        $email = $this->userToken->verifyToken($token, UserTokenModel::TOKEN_PASSWORD);

        if (!$email) {
            flash('danger', 'Invalid or expired reset token key.', 'auth/login');
        }

        $this->render('auth/reset', compact('token', 'email'), 'Reset Password');
    }

}

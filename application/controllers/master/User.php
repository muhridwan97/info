<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class User
 * @property UserModel $user
 * @property RoleModel $role
 * @property UserRoleModel $userRole
 * @property Uploader $uploader
 * @property Exporter $exporter
 * @property Mailer $mailer
 */
class User extends App_Controller
{
    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('UserModel', 'user');
        $this->load->model('RoleModel', 'role');
        $this->load->model('UserRoleModel', 'userRole');
        $this->load->model('modules/Uploader', 'uploader');
        $this->load->model('modules/Exporter', 'exporter');
        $this->load->model('modules/Mailer', 'mailer');
    }

    /**
     * Show all user page.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_USER_VIEW);

        $filters = array_merge($_GET, ['page' => get_url_param('page', 1)]);

        $export = $this->input->get('export');
        if ($export) unset($filters['page']);

        $users = $this->user->getAll($filters);

        if ($export) {
            $this->exporter->exportFromArray('User', $users);
        }

        $roles = $this->role->getAll();

        $this->render('user/index', compact('users', 'roles'));
    }

    /**
     * Show user data.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_USER_VIEW);

        $user = $this->user->getById($id);
        $roles = $this->role->getByUser($id);

        $this->render('user/view', compact('user', 'roles'));
    }

    /**
     * Show create user.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_USER_CREATE);

        $roles = $this->role->getAll();

        $this->render('user/create', compact('roles'));
    }

    /**
     * Save new user data.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_USER_CREATE);

        if ($this->validate()) {
            $name = $this->input->post('name');
            $username = $this->input->post('username');
            $email = $this->input->post('email');
            $password = $this->input->post('password');
            $status = $this->input->post('status');
            $userType = $this->input->post('user_type');
            $roles = $this->input->post('roles');

            $avatar = null;
            if (!empty($_FILES['avatar']['name'])) {
                $options = ['destination' => 'avatars/' . date('Y/m')];
                if ($this->config->item('sso_enable')) {
                    $options['base_path'] = env('SSO_STORAGE_PATH');
                }
                if ($this->uploader->uploadTo('avatar', $options)) {
                    $uploadedData = $this->uploader->getUploadedData();
                    $avatar = $uploadedData['uploaded_path'];
                } else {
                    flash('danger', $this->uploader->getDisplayErrors(), '_back', 'master/user');
                }
            }

            $this->db->trans_start();

            $this->user->create([
                'name' => $name,
                'username' => $username,
                'email' => $email,
                'password' => password_hash($password, CRYPT_BLOWFISH),
                'user_type' => $userType,
                'status' => $status,
                'avatar' => $avatar,
            ]);
            $userId = $this->db->insert_id();

            if ($this->config->item('sso_enable')) {
                $this->user->addAccessToApplication($userId);
            }

            foreach ($roles as $roleId) {
                $this->userRole->create([
                    'id_role' => $roleId,
                    'id_user' => $userId
                ]);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                if ($status == UserModel::STATUS_ACTIVATED) {
                    $emailTitle = "You have been registered at " . format_date('now', 'd F Y H:i');
                    $emailTemplate = 'emails/basic';
                    $content = "
                        We have decided that you allowed and granted to access our system. Below is your user account information:<br><br>
                        <b>Username:</b> {$username}<br>
                        <b>Email:</b> {$email}<br>
                        <b>Password:</b> {$password}<br><br>
                        <b>We highly recommend you to change the password in our system immediately.</b>
                    ";
                    $emailData = [
                        'name' => $name,
                        'email' => $email,
                        'content' => $content
                    ];
                    $this->mailer->send($email, $emailTitle, $emailTemplate, $emailData);
                }
                flash('success', "User {$name} successfully created", 'master/user');
            } else {
                flash('danger', 'Create user failed, try again or contact administrator');
            }
        }
        $this->create();
    }

    /**
     * Show edit user form.
     *
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_USER_EDIT);

        $user = $this->user->getById($id);
        $roles = $this->role->getAll();
        $parentUsers = $this->user->getBy(['prv_users.id!=' => $id]);
        $userRoles = $this->userRole->getBy(['id_user' => $id]);

        if ($user['username'] == 'admin') {
            flash('warning', 'Edit reserved user is limited a few fields that necessary only.');
        }

        $this->render('user/edit', compact('user', 'roles', 'userRoles', 'parentUsers'));
    }

    /**
     * Update data user by id.
     *
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_USER_EDIT);

        if ($this->validate($this->_validation_rules($id))) {
            $name = $this->input->post('name');
            $username = $this->input->post('username');
            $email = $this->input->post('email');
            $password = $this->input->post('password');
            $status = $this->input->post('status');
            $userType = $this->input->post('user_type');
            $roles = $this->input->post('roles');

            $user = $this->user->getById($id);

            if ($user['username'] == 'admin') {
                $name = 'Administrator';
                $username = 'admin';
                $status = UserModel::STATUS_ACTIVATED;
            }

            $avatar = $user['avatar'];
            if (!empty($_FILES['avatar']['name'])) {
                $options = ['destination' => 'avatars/' . date('Y/m')];
                if ($this->config->item('sso_enable')) {
                    $options['base_path'] = env('SSO_STORAGE_PATH');
                }
                if ($this->uploader->uploadTo('avatar', $options)) {
                    $uploadedData = $this->uploader->getUploadedData();
                    $avatar = $uploadedData['uploaded_path'];
                    if (!empty($user['avatar'])) {
                        if ($this->config->item('sso_enable')) {
                            $this->uploader->delete($user['avatar'], env('SSO_STORAGE_PATH'));
                        } else {
                            $this->uploader->delete($user['avatar']);
                        }
                    }
                } else {
                    flash('danger', $this->uploader->getDisplayErrors(), '_back', 'master/user');
                }
            }

            $this->db->trans_start();

            $this->user->update([
                'name' => $name,
                'username' => $username,
                'email' => $email,
                'password' => empty($password) ? $user['password'] : password_hash($password, CRYPT_BLOWFISH),
                'status' => $status,
                'user_type' => $userType,
                'avatar' => $avatar,
            ], $id);

            if ($user['username'] != 'admin') {
                $this->userRole->delete(['id_user' => $id]);
                foreach ($roles as $roleId) {
                    $this->userRole->create([
                        'id_role' => $roleId,
                        'id_user' => $id
                    ]);
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                $roles = $this->role->getByUser($id);
                $emailTitle = "Your account has been updated at " . format_date('now', 'd F Y H:i');
                $emailTemplate = 'emails/basic';
                $passwordInfo = empty($password) ? 'Not changed' : $password;
                $content = "
                        Administrator has been updated the account bellow:<br><br>
                        <b>Username:</b> {$username}<br>
                        <b>Email:</b> {$email}<br>
                        <b>Password:</b> {$passwordInfo}<br>
                        <b>Status:</b> {$status}<br>
                        <b>App:</b> " . $this->config->item('app_name') . "<br>
                        <b>Role:</b> " . implode(', ', array_column($roles, 'role')) . "<br><br>
                        <b>Ask your administrator for further information.</b>
                    ";
                $emailData = [
                    'name' => $name,
                    'email' => $email,
                    'content' => $content
                ];
                $this->mailer->send($email, $emailTitle, $emailTemplate, $emailData);
                flash('success', "User {$name} successfully updated", 'master/user');
            } else {
                flash('danger', 'Update user failed, try again or contact administrator');
            }
        }
        $this->edit($id);
    }

    /**
     * Perform deleting user data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_USER_DELETE);

        $user = $this->user->getById($id);

        if ($user['username'] == 'admin') {
            flash('danger', 'Administrator is reserved role.', 'master/user');
        }

        if ($this->user->delete($id, true)) {
            flash('success', "User {$user['name']} is successfully deleted");
        } else {
            flash('danger', 'Delete user failed, try again or contact administrator');
        }
        redirect('master/user');
    }


    /**
     * Return general validation rules.
     *
     * @param array $params
     * @return array
     */
    protected function _validation_rules(...$params)
    {
        $id = isset($params[0]) ? $params[0] : 0;

        $user = $this->user->getById($id);

        $baseRule = [
            'name' => 'trim|required|max_length[50]',
            'username' => [
                'trim', 'required', 'max_length[25]', 'regex_match[/^[a-zA-Z0-9.\-]+$/]', ['username_exists', function ($username) use ($id) {
                    $this->form_validation->set_message('username_exists', 'The username %s has been registered before, try another');
                    return $this->user->isUniqueUsername($username, $id);
                }]
            ],
            'email' => [
                'trim', 'required', 'valid_email', 'max_length[50]', ['email_exists', function ($email) use ($id) {
                    $this->form_validation->set_message('email_exists', 'The email %s has been registered before, try another');
                    return $this->user->isUniqueEmail($email, $id);
                }]
            ],
            'password' => 'min_length[6]' . ($id > 0 ? '' : '|required'),
            'confirm_password' => 'matches[password]',
            'user_type' => 'trim|required|in_list[INTERNAL,EXTERNAL]',
        ];

        if (isset($user['username']) && $user['username'] != 'admin') {
            $baseRule = array_merge($baseRule, [
                'status' => 'trim|required',
                'roles[]' => [
                    'trim', 'required', ['not_empty', function ($input) {
                        $this->form_validation->set_message('not_empty', 'The {field} field must be selected at least one.');
                        return !empty($input);
                    }]
                ]
            ]);
        }

        return $baseRule;
    }

}

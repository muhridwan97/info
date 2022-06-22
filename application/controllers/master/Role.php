<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Role
 * @property RoleModel $role
 * @property PermissionModel $permission
 * @property RolePermissionModel $rolePermission
 * @property Exporter $exporter
 */
class Role extends App_Controller
{
    /**
     * Role constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('RoleModel', 'role');
        $this->load->model('PermissionModel', 'permission');
        $this->load->model('RolePermissionModel', 'rolePermission');
        $this->load->model('modules/Exporter', 'exporter');
    }

    /**
     * Show role index page.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_ROLE_VIEW);

        $filters = array_merge($_GET, ['page' => get_url_param('page', 1)]);

        $export = $this->input->get('export');
        if ($export) unset($filters['page']);

        $roles = $this->role->getAll($filters);

        if ($export) {
            $this->exporter->exportFromArray('Role', $roles);
        }

        $this->render('role/index', compact('roles'));
    }

    /**
     * Show role data.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_ROLE_VIEW);

        $role = $this->role->getById($id);

        $permissions = $this->permission->getByRole($id);

        $this->render('role/view', compact('role', 'permissions'));
    }

    /**
     * Show create role.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_ROLE_CREATE);

        $permissions = $this->permission->getAll();

        $this->render('role/create', compact('permissions'));
    }

    /**
     * Save new role data.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_ROLE_CREATE);

        if ($this->validate()) {
            $role = $this->input->post('role');
            $description = $this->input->post('description');
            $permissions = $this->input->post('permissions');

            $this->db->trans_start();

            $this->role->create([
                'role' => $role,
                'description' => $description
            ]);

            $roleId = $this->db->insert_id();

            foreach ($permissions as $permissionId) {
                $this->rolePermission->create([
                    'id_role' => $roleId,
                    'id_permission' => $permissionId
                ]);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Role {$role} successfully created", 'master/role');
            } else {
                flash('danger', 'Create role failed, try again or contact administrator');
            }
        }
        $this->create();
    }

    /**
     * Show edit role form.
     *
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_ROLE_EDIT);

        $role = $this->role->getById($id);
        $permissions = $this->permission->getAll();
        $rolePermissions = $this->rolePermission->getBy(['id_role' => $id]);

        if ($role['role'] == RoleModel::ROLE_ADMINISTRATOR) {
            flash('danger', "Reserved role {$role['role']} cannot be edited.", 'master/role');
        }

        $this->render('role/edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update data role by id.
     *
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_ROLE_EDIT);

        if ($this->validate()) {
            $role = $this->input->post('role');
            $description = $this->input->post('description');
            $permissions = $this->input->post('permissions');

            if (in_array($role, RoleModel::RESERVED_ROLES)) {
                flash('danger', "{$role} is reserved role.", 'master/role');
            }

            $this->db->trans_start();

            $this->role->update([
                'role' => $role,
                'description' => $description
            ], $id);

            $this->rolePermission->delete(['id_role' => $id]);

            foreach ($permissions as $permissionId) {
                $this->rolePermission->create([
                    'id_role' => $id,
                    'id_permission' => $permissionId
                ]);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Role {$role} successfully updated", 'master/role');
            } else {
                flash('danger', "Update role failed, try again or contact administrator");
            }
        }
        $this->edit($id);
    }

    /**
     * Perform deleting role data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_ROLE_DELETE);

        $role = $this->role->getById($id);

        if (in_array($role['role'], RoleModel::RESERVED_ROLES)) {
            flash('danger', "Reserved role {$role['role']} cannot be deleted.", 'master/role');
        }

        if ($this->role->delete($id, true)) {
            flash('warning', "Role {$role['role']} successfully deleted");
        } else {
            flash('danger', 'Delete role failed, try again or contact administrator');
        }
        redirect('master/role');
    }

    /**
     * Return general validation rules.
     *
     * @return array
     */
    protected function _validation_rules()
    {
        return [
            'role' => 'trim|required|max_length[50]',
            'description' => 'max_length[500]',
            'permissions[]' => [
                'trim', 'required', ['not_empty', function ($input) {
                    $this->form_validation->set_message('not_empty', 'The {field} field must be selected at least one.');
                    return !empty($input);
                }]
            ]
        ];
    }
}
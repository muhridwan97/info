<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Migration_Seed_admin_user
 * @property CI_DB_query_builder $db
 * @property CI_DB_forge $dbforge
 */
class Migration_Seed_admin_user extends CI_Migration
{
    public function up()
    {
        $this->load->model('RoleModel');

        $this->db->insert('prv_users', [
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@warehouse.app',
            'password' => password_hash('admin', PASSWORD_BCRYPT),
            'user_type' => 'INTERNAL',
            'status' => 'ACTIVATED',
        ]);
        $userId = $this->db->insert_id();

        $this->db->insert('prv_roles', [
            'role' => RoleModel::ROLE_ADMINISTRATOR,
            'description' => 'Top level user',
            'created_by' => $userId,
        ]);
        $roleId = $this->db->insert_id();

        $this->db->insert('prv_user_roles', [
            'id_user' => $userId,
            'id_role' => $roleId
        ]);

        $this->db->insert('prv_permissions', [
            'module' => 'admin',
            'submodule' => 'admin',
            'permission' => PERMISSION_ALL_ACCESS,
            'description' => 'Access all feature without specific rule'
        ]);
        $permissionId = $this->db->insert_id();

        $this->db->insert('prv_role_permissions', [
            'id_role' => $roleId,
            'id_permission' => $permissionId
        ]);

        echo '--Seeding Migration_Seed_admin_user' . PHP_EOL;
    }

    public function down()
    {
        $this->db->delete('prv_users', ['username' => 'admin']);
        $this->db->delete('prv_roles', ['role' => 'Administrator']);
        echo 'Rollback Migration_Seed_admin_user' . PHP_EOL;
    }
}

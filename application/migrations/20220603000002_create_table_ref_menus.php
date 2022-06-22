<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Migration_Create_table_ref_menus
 * @property CI_DB_forge $dbforge
 */
class Migration_Create_table_ref_menus extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'unsigned' => TRUE, 'constraint' => 11, 'auto_increment' => TRUE],
            'id_parent' => ['type' => 'INT', 'unsigned' => TRUE, 'constraint' => 11, 'null' => TRUE],
            'url' => ['type' => 'VARCHAR', 'constraint' => '500'],
            'menu_name' => ['type' => 'VARCHAR', 'constraint' => '100'],
            'link_type' => ['type' => 'VARCHAR', 'constraint' => '100', 'null' => TRUE],
            'is_deleted' => ['type' => 'INT', 'constraint' => 1, 'default' => 0],
            'created_at' => ['type' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'],
            'created_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE],
            'updated_at' => ['type' => 'TIMESTAMP ON UPDATE CURRENT_TIMESTAMP', 'null' => TRUE],
            'updated_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE],
            'deleted_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'deleted_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE]
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('ref_menus');
        $this->db->insert_batch('prv_permissions', [

            [
                'module' => 'master', 'submodule' => 'menu', 'permission' => PERMISSION_MENU_VIEW,
                'description' => 'View menu data'
            ],
            [
                'module' => 'master', 'submodule' => 'menu', 'permission' => PERMISSION_MENU_CREATE,
                'description' => 'Create new menu'
            ],
            [
                'module' => 'master', 'submodule' => 'menu', 'permission' => PERMISSION_MENU_EDIT,
                'description' => 'Edit menu'
            ],
            [
                'module' => 'master', 'submodule' => 'menu', 'permission' => PERMISSION_MENU_DELETE,
                'description' => 'Delete menu'
            ],
        ]);
        echo 'Migrate Migration_Create_table_ref_menus' . PHP_EOL;
    }

    public function down()
    {
        $this->dbforge->drop_table('ref_menus');
        $this->db->delete('prv_permissions', ['module' => 'master', 'submodule' => 'menu']);
        echo 'Rollback Migration_Create_table_ref_menus' . PHP_EOL;
    }
}

<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Migration_Create_table_banners
 * @property CI_DB_forge $dbforge
 */
class Migration_Create_table_banners extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'unsigned' => TRUE, 'constraint' => 11, 'auto_increment' => TRUE],
            'title' => ['type' => 'VARCHAR', 'constraint' => '100'],
            'caption' => ['type' => 'VARCHAR', 'constraint' => '100'],
            'photo' => ['type' => 'VARCHAR', 'constraint' => '200', 'null' => TRUE],
            'is_show' => ['type' => 'INT', 'constraint' => 1, 'default' => 1],
            'is_deleted' => ['type' => 'INT', 'constraint' => 1, 'default' => 0],
            'created_at' => ['type' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'],
            'created_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE],
            'updated_at' => ['type' => 'TIMESTAMP ON UPDATE CURRENT_TIMESTAMP', 'null' => TRUE],
            'updated_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE],
            'deleted_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'deleted_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE]
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('banners');
        $this->db->insert_batch('prv_permissions', [

            [
                'module' => 'banner', 'submodule' => 'banner', 'permission' => PERMISSION_BANNER_VIEW,
                'description' => 'View banner data'
            ],
            [
                'module' => 'banner', 'submodule' => 'banner', 'permission' => PERMISSION_BANNER_CREATE,
                'description' => 'Create new banner'
            ],
            [
                'module' => 'banner', 'submodule' => 'banner', 'permission' => PERMISSION_BANNER_EDIT,
                'description' => 'Edit banner'
            ],
            [
                'module' => 'banner', 'submodule' => 'banner', 'permission' => PERMISSION_BANNER_DELETE,
                'description' => 'Delete banner'
            ],
        ]);
        echo 'Migrate Migration_Create_table_banners' . PHP_EOL;
    }

    public function down()
    {
        $this->dbforge->drop_table('banners');
        $this->db->delete('prv_permissions', ['module' => 'banner', 'submodule' => 'banner']);
        echo 'Rollback Migration_Create_table_banners' . PHP_EOL;
    }
}

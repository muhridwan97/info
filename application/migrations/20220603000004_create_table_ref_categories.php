<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Migration_Create_table_ref_categories
 * @property CI_DB_forge $dbforge
 */
class Migration_Create_table_ref_categories extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'unsigned' => TRUE, 'constraint' => 11, 'auto_increment' => TRUE],
            'category' => ['type' => 'VARCHAR', 'constraint' => '500'],
            'slug' => ['type' => 'VARCHAR', 'constraint' => '500'],
            'description' => ['type' => 'TEXT', 'null' => TRUE],
            'is_deleted' => ['type' => 'INT', 'constraint' => 1, 'default' => 0],
            'created_at' => ['type' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'],
            'created_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE],
            'updated_at' => ['type' => 'TIMESTAMP ON UPDATE CURRENT_TIMESTAMP', 'null' => TRUE],
            'updated_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE],
            'deleted_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'deleted_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE]
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('ref_categories');
        $this->db->insert_batch('prv_permissions', [

            [
                'module' => 'master', 'submodule' => 'category', 'permission' => PERMISSION_CATEGORY_VIEW,
                'description' => 'View category data'
            ],
            [
                'module' => 'master', 'submodule' => 'category', 'permission' => PERMISSION_CATEGORY_CREATE,
                'description' => 'Create new category'
            ],
            [
                'module' => 'master', 'submodule' => 'category', 'permission' => PERMISSION_CATEGORY_EDIT,
                'description' => 'Edit category'
            ],
            [
                'module' => 'master', 'submodule' => 'category', 'permission' => PERMISSION_CATEGORY_DELETE,
                'description' => 'Delete category'
            ],
        ]);
        echo 'Migrate Migration_Create_table_ref_categories' . PHP_EOL;
    }

    public function down()
    {
        $this->dbforge->drop_table('ref_categories');
        $this->db->delete('prv_permissions', ['module' => 'master', 'submodule' => 'category']);
        echo 'Rollback Migration_Create_table_ref_categories' . PHP_EOL;
    }
}

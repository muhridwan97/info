<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Migration_Create_table_pages
 * @property CI_DB_forge $dbforge
 */
class Migration_Create_table_pages extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'unsigned' => TRUE, 'constraint' => 11, 'auto_increment' => TRUE],
            'url' => ['type' => 'VARCHAR', 'constraint' => '500'],
            'page_name' => ['type' => 'VARCHAR', 'constraint' => '100'],
            'photo' => ['type' => 'VARCHAR', 'constraint' => '200', 'null' => TRUE],
            'content' => ['type' => 'TEXT', 'null' => TRUE],
            'is_deleted' => ['type' => 'INT', 'constraint' => 1, 'default' => 0],
            'created_at' => ['type' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'],
            'created_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE],
            'updated_at' => ['type' => 'TIMESTAMP ON UPDATE CURRENT_TIMESTAMP', 'null' => TRUE],
            'updated_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE],
            'deleted_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'deleted_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE]
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('pages');
        $this->db->insert_batch('prv_permissions', [

            [
                'module' => 'page', 'submodule' => 'page', 'permission' => PERMISSION_PAGE_VIEW,
                'description' => 'View page data'
            ],
            [
                'module' => 'page', 'submodule' => 'page', 'permission' => PERMISSION_PAGE_CREATE,
                'description' => 'Create new page'
            ],
            [
                'module' => 'page', 'submodule' => 'page', 'permission' => PERMISSION_PAGE_EDIT,
                'description' => 'Edit page'
            ],
            [
                'module' => 'page', 'submodule' => 'page', 'permission' => PERMISSION_PAGE_DELETE,
                'description' => 'Delete page'
            ],
        ]);
        echo 'Migrate Migration_Create_table_pages' . PHP_EOL;
    }

    public function down()
    {
        $this->dbforge->drop_table('pages');
        $this->db->delete('prv_permissions', ['module' => 'page', 'submodule' => 'page']);
        echo 'Rollback Migration_Create_table_pages' . PHP_EOL;
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Migration_Create_table_prv_permissions
 * @property CI_DB_forge $dbforge
 */
class Migration_Create_table_prv_permissions extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'unsigned' => TRUE, 'constraint' => 11, 'auto_increment' => TRUE],
            'module' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => TRUE],
            'submodule' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => TRUE],
            'permission' => ['type' => 'VARCHAR', 'constraint' => '50', 'unique' => TRUE],
            'description' => ['type' => 'TEXT', 'null' => TRUE],
            'created_at' => ['type' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('prv_permissions');
        echo 'Migrate Migration_Create_table_prv_permissions' . PHP_EOL;
    }

    public function down()
    {
        $this->dbforge->drop_table('prv_permissions',TRUE);
        echo 'Rollback Migration_Create_table_prv_permissions' . PHP_EOL;
    }
}
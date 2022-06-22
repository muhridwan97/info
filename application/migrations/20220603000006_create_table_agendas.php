<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Migration_Create_table_agendas
 * @property CI_DB_forge $dbforge
 */
class Migration_Create_table_agendas extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'unsigned' => TRUE, 'constraint' => 11, 'auto_increment' => TRUE],
            'title' => ['type' => 'VARCHAR', 'constraint' => '100'],
            'photo' => ['type' => 'VARCHAR', 'constraint' => '200', 'null' => TRUE],
            'date' => ['type' => 'DATE'],
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
        $this->dbforge->create_table('agendas');
        $this->db->insert_batch('prv_permissions', [

            [
                'module' => 'agenda', 'submodule' => 'agenda', 'permission' => PERMISSION_AGENDA_VIEW,
                'description' => 'View agenda data'
            ],
            [
                'module' => 'agenda', 'submodule' => 'agenda', 'permission' => PERMISSION_AGENDA_CREATE,
                'description' => 'Create new agenda'
            ],
            [
                'module' => 'agenda', 'submodule' => 'agenda', 'permission' => PERMISSION_AGENDA_EDIT,
                'description' => 'Edit agenda'
            ],
            [
                'module' => 'agenda', 'submodule' => 'agenda', 'permission' => PERMISSION_AGENDA_DELETE,
                'description' => 'Delete agenda'
            ],
        ]);
        echo 'Migrate Migration_Create_table_agendas' . PHP_EOL;
    }

    public function down()
    {
        $this->dbforge->drop_table('agendas');
        $this->db->delete('prv_permissions', ['module' => 'agenda', 'submodule' => 'agenda']);
        echo 'Rollback Migration_Create_table_agendas' . PHP_EOL;
    }
}

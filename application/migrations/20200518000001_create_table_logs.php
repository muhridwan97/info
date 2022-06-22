<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Migration_Create_table_logs
 * @property CI_DB_forge $dbforge
 */
class Migration_Create_table_logs extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field(array(
            'id' => ['type' => 'INT', 'unsigned' => TRUE, 'constraint' => 11, 'auto_increment' => TRUE],
            'event_access' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => TRUE],
            'event_type' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => TRUE],
            'data' => ['type' => 'TEXT', 'null' => TRUE],
            'created_at' => ['type' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'],
            'created_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE],
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('logs');
        echo 'Migrating Migration_Create_table_logs' . PHP_EOL;
    }

    public function down()
    {
        $this->dbforge->drop_table('logs', TRUE);
        echo 'Rollback Migration_Create_table_logs' . PHP_EOL;
    }
}
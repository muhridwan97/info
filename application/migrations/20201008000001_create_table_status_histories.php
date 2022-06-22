<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Migration_Create_table_status_histories
 * @property CI_DB_forge $dbforge
 */
class Migration_Create_table_status_histories extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field(array(
            'id' => ['type' => 'INT', 'unsigned' => TRUE, 'constraint' => 11, 'auto_increment' => TRUE],
            'type' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => TRUE],
            'id_reference' => ['type' => 'INT', 'unsigned' => TRUE, 'constraint' => 11],
            'status' => ['type' => 'VARCHAR', 'constraint' => 300, 'null' => TRUE],
            'description' => ['type' => 'TEXT', 'null' => TRUE],
            'data' => ['type' => 'TEXT', 'null' => TRUE],
            'created_at' => ['type' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'],
            'created_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE],
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('status_histories');
        echo 'Migrating Migration_Create_table_status_histories' . PHP_EOL;
    }

    public function down()
    {
        $this->dbforge->drop_table('status_histories');
        echo 'Rollback Migration_Create_table_status_histories' . PHP_EOL;
    }
}
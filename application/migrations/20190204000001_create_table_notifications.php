<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Migration_Create_table_notifications
 * @property CI_DB_forge $dbforge
 */
class Migration_Create_table_notifications extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'unsigned' => TRUE, 'constraint' => 11, 'auto_increment' => TRUE],
            'id_user' => ['type' => 'INT', 'unsigned' => TRUE, 'constraint' => 11],
            'id_related' => ['type' => 'INT', 'unsigned' => TRUE, 'constraint' => 11],
            'channel' => ['type' => 'VARCHAR', 'constraint' => '100'],
            'event' => ['type' => 'VARCHAR', 'constraint' => '200'],
            'data' => ['type' => 'TEXT', 'null' => TRUE],
            'is_read' => ['type' => 'INT', 'constraint' => 1, 'default' => 0],
            'created_at' => ['type' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('notifications');
        echo 'Migrate Migration_Create_table_notifications' . PHP_EOL;
    }

    public function down()
    {
        $this->dbforge->drop_table('notifications',TRUE);
        echo 'Rollback Migration_Create_table_notifications' . PHP_EOL;
    }
}
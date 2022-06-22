<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Migration_Create_table_prv_users
 * @property CI_DB_forge $dbforge
 */
class Migration_Create_table_prv_users extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'unsigned' => TRUE, 'constraint' => 11, 'auto_increment' => TRUE],
            'name' => ['type' => 'VARCHAR', 'constraint' => '50'],
            'username' => ['type' => 'VARCHAR', 'constraint' => '50', 'unique' => TRUE],
            'email' => ['type' => 'VARCHAR', 'constraint' => '50', 'unique' => TRUE],
            'password' => ['type' => 'VARCHAR', 'constraint' => '200'],
            'avatar' => ['type' => 'VARCHAR', 'constraint' => '300', 'null' => TRUE],
            'user_type' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => TRUE],
            'status' => ['type' => 'ENUM("ACTIVATED", "PENDING", "SUSPENDED")', 'default' => 'PENDING'],
            'is_deleted' => ['type' => 'INT', 'constraint' => 1, 'default' => 0],
            'created_at' => ['type' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'],
            'created_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE],
            'updated_at' => ['type' => 'TIMESTAMP ON UPDATE CURRENT_TIMESTAMP', 'null' => TRUE],
            'updated_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE],
            'deleted_at' => ['type' => 'TIMESTAMP', 'null' => TRUE],
            'deleted_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE]
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('prv_users');
        echo 'Migrating Migration_Create_table_prv_users' . PHP_EOL;
    }

    public function down()
    {
        $this->dbforge->drop_table('prv_users',TRUE);
        echo 'Rollback Migration_Create_table_prv_users' . PHP_EOL;
    }
}
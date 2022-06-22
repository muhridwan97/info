<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Migration_Create_table_prv_user_tokens
 * @property CI_DB_forge $dbforge
 */
class Migration_Create_table_prv_user_tokens extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'unsigned' => TRUE, 'constraint' => 11, 'auto_increment' => TRUE],
            'email' => ['type' => 'VARCHAR', 'constraint' => '50'],
            'token' => ['type' => 'VARCHAR', 'constraint' => '200'],
            'type' => ['type' => 'ENUM("REGISTRATION", "PASSWORD", "REMEMBER", "OTP", "AUTHORIZATION")', 'default' => 'REGISTRATION'],
            'max_activation' => ['type' => 'INT', 'constraint' => 11, 'null' => TRUE],
            'expired_at' => ['type' => 'DATETIME', 'null' => TRUE],
            'created_at' => ['type' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'],
        ]); //->add_field('CONSTRAINT fk_user_token_user FOREIGN KEY (email) REFERENCES prv_users(email) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('prv_user_tokens');
        echo 'Migrating Migration_Create_table_prv_user_tokens' . PHP_EOL;
    }

    public function down()
    {
        $this->dbforge->drop_table('prv_user_tokens',TRUE);
        echo 'Rollback Migration_Create_table_prv_user_tokens' . PHP_EOL;
    }
}
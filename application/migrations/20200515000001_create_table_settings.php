<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Migration_Create_table_settings
 * @property CI_DB_forge $dbforge
 */
class Migration_Create_table_settings extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field(array(
            'key' => ['type' => 'VARCHAR', 'constraint' => '50'],
            'value' => ['type' => 'TEXT'],
            'description' => ['type' => 'VARCHAR', 'constraint' => '500', 'null' => true],
            'created_at' => ['type' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'],
            'created_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE],
            'updated_at' => ['type' => 'TIMESTAMP ON UPDATE CURRENT_TIMESTAMP', 'null' => true],
            'updated_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => true],
        ));
        $this->dbforge->add_key('key', TRUE);
        $this->dbforge->create_table('settings');
        echo 'Migrate Migration_Create_table_settings' . PHP_EOL;

        $this->db->insert_batch('settings', [
            [
                'key' => 'app_name',
                'value' => 'Training',
            ],
            [
                'key' => 'meta_url',
                'value' => 'https://training.app',
            ],
            [
                'key' => 'meta_keywords',
                'value' => 'training,employee,syllabus,course,lesson',
            ],
            [
                'key' => 'meta_description',
                'value' => 'Training management system',
            ],
            [
                'key' => 'meta_author',
                'value' => 'Transcon Indonesia',
            ],
            [
                'key' => 'email_bug_report',
                'value' => 'bug@transcon-indonesia.com',
            ],
            [
                'key' => 'email_support',
                'value' => 'support@transcon-indonesia.com',
            ],
            [
                'key' => 'company_name',
                'value' => 'Transcon Indonesia',
            ],
            [
                'key' => 'company_address',
                'value' => 'Jl. Denpasar Blok II No.1 dan 16 Kbn Marunda. Clincing Jakarta Utara',
            ],
            [
                'key' => 'company_contact',
                'value' => 'Telp: 021-44850578 Fax: 021-44850403',
            ],
        ]);
        echo '--Seeding Migration_Create_table_settings' . PHP_EOL;
    }

    public function down()
    {
        $this->dbforge->drop_table('settings');
        echo 'Rollback Migration_Create_table_settings' . PHP_EOL;
    }
}

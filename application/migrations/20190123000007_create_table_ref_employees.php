<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Migration_Create_table_ref_employees
 * @property CI_DB_forge $dbforge
 */
class Migration_Create_table_ref_employees extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'unsigned' => TRUE, 'constraint' => 11, 'auto_increment' => TRUE],
            'id_department' => ['type' => 'INT', 'unsigned' => TRUE, 'constraint' => 11, 'null' => TRUE],
            'id_employee' => ['type' => 'INT', 'unsigned' => TRUE, 'constraint' => 11, 'null' => TRUE],
            'id_user' => ['type' => 'INT', 'unsigned' => TRUE, 'constraint' => 11, 'null' => TRUE],
            'no_employee' => ['type' => 'VARCHAR', 'constraint' => '50', 'unique' => TRUE],
            'name' => ['type' => 'VARCHAR', 'constraint' => '50'],
            'enter_date' => ['type' => 'DATE', 'null' => TRUE],
            'quit_date' => ['type' => 'DATE', 'null' => TRUE],
            'gender' => ['type' => 'ENUM("female", "male")', 'null' => TRUE],
            'company' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => TRUE],
            'position_level' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => TRUE],
            'position' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => TRUE],
            'work_location' => ['type' => 'VARCHAR', 'constraint' => '100', 'null' => TRUE],
            'tax_no' => ['type' => 'VARCHAR', 'constraint' => '100', 'null' => TRUE],
            'tax_address' => ['type' => 'VARCHAR', 'constraint' => '100', 'null' => TRUE],
            'id_card_no' => ['type' => 'VARCHAR', 'constraint' => '100', 'null' => TRUE],
            'id_card_address' => ['type' => 'VARCHAR', 'constraint' => '100', 'null' => TRUE],
            'social_security_no' => ['type' => 'VARCHAR', 'constraint' => '100', 'null' => TRUE],
            'health_insurance_no' => ['type' => 'VARCHAR', 'constraint' => '100', 'null' => TRUE],
            'place_of_birth' => ['type' => 'VARCHAR', 'constraint' => '100', 'null' => TRUE],
            'date_of_birth' => ['type' => 'DATE', 'null' => TRUE],
            'contact_phone' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => TRUE],
            'contact_mobile' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => TRUE],
            'photo' => ['type' => 'VARCHAR', 'constraint' => '300', 'null' => TRUE],
            'status' => ['type' => 'ENUM("ACTIVE", "INACTIVE")', 'default' => 'ACTIVE'],
            'description' => ['type' => 'TEXT', 'null' => TRUE],
            'is_deleted' => ['type' => 'INT', 'constraint' => 1, 'default' => 0],
            'created_at' => ['type' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'],
            'created_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE],
            'updated_at' => ['type' => 'TIMESTAMP ON UPDATE CURRENT_TIMESTAMP', 'null' => TRUE],
            'updated_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE],
            'deleted_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'deleted_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE]
        ])
			->add_field('CONSTRAINT fk_employee_department FOREIGN KEY (id_department) REFERENCES ref_departments(id) ON DELETE CASCADE ON UPDATE CASCADE')
			->add_field('CONSTRAINT fk_employee_user FOREIGN KEY (id_user) REFERENCES prv_users(id) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('ref_employees');
        echo 'Migrate Migration_Create_table_ref_employees' . PHP_EOL;
    }

    public function down()
    {
        $this->dbforge->drop_table('ref_employees');
        echo 'Rollback Migration_Create_table_ref_employees' . PHP_EOL;
    }
}

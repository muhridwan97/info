<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Migration_Create_table_blogs
 * @property CI_DB_forge $dbforge
 */
class Migration_Create_table_blogs extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'unsigned' => TRUE, 'constraint' => 11, 'auto_increment' => TRUE],
            'id_category' => ['type' => 'INT', 'unsigned' => TRUE, 'constraint' => 11],
            'title' => ['type' => 'VARCHAR', 'constraint' => '500'],
            'body' => ['type' => 'TEXT', 'null' => TRUE],
            'writed_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE],
            'date' => ['type' => 'DATE', 'null' => TRUE],
            'photo' => ['type' => 'VARCHAR', 'constraint' => '500'],
            'attachment' => ['type' => 'VARCHAR', 'constraint' => '500'],
            'count_view' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'status' => ['type' => 'VARCHAR', 'constraint' => '100', 'default' => 'PENDING'],
            'description' => ['type' => 'TEXT', 'null' => TRUE],
            'is_deleted' => ['type' => 'INT', 'constraint' => 1, 'default' => 0],
            'created_at' => ['type' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'],
            'created_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE],
            'updated_at' => ['type' => 'TIMESTAMP ON UPDATE CURRENT_TIMESTAMP', 'null' => TRUE],
            'updated_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE],
            'deleted_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'deleted_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE]
        ])
            ->add_field('CONSTRAINT fk_blogs_ref_categories FOREIGN KEY (id_category) REFERENCES ref_categories(id) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('blogs');
        $this->db->insert_batch('prv_permissions', [

            [
                'module' => 'blog', 'submodule' => 'blog', 'permission' => PERMISSION_BLOG_VIEW,
                'description' => 'View blog data'
            ],
            [
                'module' => 'blog', 'submodule' => 'blog', 'permission' => PERMISSION_BLOG_CREATE,
                'description' => 'Create new blog'
            ],
            [
                'module' => 'blog', 'submodule' => 'blog', 'permission' => PERMISSION_BLOG_EDIT,
                'description' => 'Edit blog'
            ],
            [
                'module' => 'blog', 'submodule' => 'blog', 'permission' => PERMISSION_BLOG_DELETE,
                'description' => 'Delete blog'
            ],
            [
                'module' => 'blog', 'submodule' => 'blog', 'permission' => PERMISSION_BLOG_VALIDATE,
                'description' => 'Validate blog'
            ],
        ]);
        echo 'Migrate Migration_Create_table_blogs' . PHP_EOL;
    }

    public function down()
    {
        $this->dbforge->drop_table('blogs');
        $this->db->delete('prv_permissions', ['module' => 'blog', 'submodule' => 'blog']);
        echo 'Rollback Migration_Create_table_blogs' . PHP_EOL;
    }
}

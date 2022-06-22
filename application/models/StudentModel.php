<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StudentModel extends App_Model
{
    protected $table = 'ref_students';
    public static $tableStudent = 'ref_students';
    
    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_INACTIVE = 'INACTIVE';

    public function __construct()
    {
        if ($this->config->item('sso_enable')) {
            $this->table = env('DB_LETTER_DATABASE') . '.ref_students';
            self::$tableStudent = env('DB_LETTER_DATABASE') . '.ref_students';
        }
    }
    /**
     * Get base query of table.
     *
     * @return CI_DB_query_builder
     */
    public function getBaseQuery()
    {
        return parent::getBaseQuery()
                ->select([
                    'ref_lecturers.name AS nama_pembimbing',
                    'ref_lecturers.no_lecturer',
                    'prv_users.email',
                    ])
                ->join('ref_lecturers','ref_lecturers.id = ref_students.id_pembimbing','left')
                ->join('prv_users','prv_users.id = ref_students.id_user','left');
    }
}
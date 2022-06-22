<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DepartmentModel extends App_Model
{
    protected $table = 'ref_departments';

    public static $tableDepartment = 'ref_departments';

    public function __construct()
    {
        if ($this->config->item('sso_enable')) {
            $this->table = env('DB_HR_DATABASE') . '.ref_departments';
            self::$tableDepartment = env('DB_HR_DATABASE') . '.ref_departments';
        }
    }

    /**
     * Get base query of table.
     *
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery()
    {
        return parent::getBaseQuery()
            ->select([
                'COUNT(DISTINCT ref_employees.id) AS total_employee',
                'COUNT(DISTINCT curriculums.id) AS total_curriculums',
            ])
            ->join('(SELECT * FROM ' . EmployeeModel::$tableEmployee . ' WHERE is_deleted = FALSE) AS ref_employees', 'ref_employees.id_department = ref_departments.id', 'left')
            ->join('(SELECT * FROM curriculums WHERE is_deleted = FALSE) AS curriculums', 'curriculums.id_department = ref_departments.id', 'left')
            ->group_by('ref_departments.id');
    }
}

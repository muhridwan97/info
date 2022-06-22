<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EmployeeModel extends App_Model
{
    protected $table = 'ref_employees';

    public static $tableEmployee = 'ref_employees';

    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_INACTIVE = 'INACTIVE';

    public function __construct()
    {
        if ($this->config->item('sso_enable')) {
            $this->table = env('DB_HR_DATABASE') . '.ref_employees';
            self::$tableEmployee = env('DB_HR_DATABASE') . '.ref_employees';
        }

		$this->addFilteredMap('status', function (CI_DB_query_builder &$baseQuery, &$filters) {
			if (key_exists('status', $filters) && !empty($filters['status'])) {
				$baseQuery->where($this->table . '.status', $filters['status']);
			}
		});
    }

    /**
     * Get base query.
     *
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery()
    {
        return parent::getBaseQuery()
            ->select([
                'ref_departments.department',
                'supervisors.name AS supervisor_name',
                UserModel::$tableUser . '.username',
                UserModel::$tableUser . '.email',
            ])
            ->join(EmployeeModel::$tableEmployee . ' AS supervisors', 'supervisors.id = ref_employees.id_employee', 'left')
            ->join(DepartmentModel::$tableDepartment, 'ref_departments.id = ref_employees.id_department', 'left')
            ->join(UserModel::$tableUser, UserModel::$tableUser . '.id = ref_employees.id_user', 'left');
    }

	/**
	 * Generate employee number.
	 *
	 * @return string
	 */
	public function getAutoNumberEmployee()
	{
		$orderData = $this->db->query("
            SELECT IFNULL(CAST(RIGHT(no_employee, 5) AS UNSIGNED), 0) + 1 AS order_number 
            FROM " . EmployeeModel::$tableEmployee . "
            WHERE no_employee LIKE 'EMP%'
            ORDER BY no_employee DESC LIMIT 1
        ");
		$orderPad = '00001';
		if ($orderData->num_rows()) {
			$lastOrder = $orderData->row_array();
			$orderPad = str_pad($lastOrder['order_number'], 5, '0', STR_PAD_LEFT);
		}
		return 'EMP' . $orderPad;
	}
}

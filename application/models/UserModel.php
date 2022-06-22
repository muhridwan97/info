<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserModel extends App_Model
{
	protected $table = 'prv_users';
	protected $tableUserApplication = 'prv_user_applications';
	protected $tableApplication = 'prv_applications';

	public static $tableUser = 'prv_users';

	protected $filteredFields = [
		'*',
		'ref_employees.no_employee',
		'prv_roles.role'
	];

	const STATUS_PENDING = 'PENDING';
	const STATUS_ACTIVATED = 'ACTIVATED';
	const STATUS_SUSPENDED = 'SUSPENDED';

	public function __construct()
	{
		if ($this->config->item('sso_enable')) {
			$this->table = env('DB_SSO_DATABASE') . '.prv_users';
			$this->tableUserApplication = env('DB_SSO_DATABASE') . '.prv_user_applications';
			$this->tableApplication = env('DB_SSO_DATABASE') . '.prv_applications';
			self::$tableUser = env('DB_SSO_DATABASE') . '.prv_users';
		}
	}

	/**
	 * Get base query of table.
	 *
	 * @return CI_DB_query_builder
	 */
	public function getBaseQuery()
	{
		$this->addFilteredField([
			'ref_employees.no_employee',
			'ref_employees.name'
		]);

		$baseQuery = $this->db->select([
			$this->table . '.*',
			'GROUP_CONCAT(DISTINCT prv_roles.role) AS roles',
			'ref_employees.id AS id_employee',
			'ref_employees.no_employee',
			'ref_employees.name AS employee_name',
		])
			->distinct()
			->from($this->table)
			->join('prv_user_roles', 'prv_user_roles.id_user = ' . $this->table . '.id', 'left')
			->join('prv_roles', 'prv_roles.id = prv_user_roles.id_role', 'left')
			->join(EmployeeModel::$tableEmployee, 'ref_employees.id_user = prv_users.id', 'left')
			->group_by([$this->table . '.id', 'ref_employees.id', 'ref_employees.no_employee'])
			->order_by($this->id, 'desc');

		if ($this->config->item('sso_enable')) {
			$baseQuery
				->join($this->tableUserApplication, 'prv_user_applications.id_user = prv_users.id')
				->join($this->tableApplication, 'prv_applications.id = prv_user_applications.id_application')
				->where("TRIM(TRAILING '/' FROM prv_applications.url)=", rtrim(site_url(), '/'));
		}

		return $baseQuery;
	}

	/**
	 * Get all data model.
	 *
	 * @param array $filters
	 * @param bool $withTrashed
	 * @return array|mixed
	 */
	public function getAll($filters = [], $withTrashed = false)
	{
		$currentPage = 1;
		$perPage = 15;

		$this->db->start_cache();

		$baseQuery = $this->getBaseQuery();

		if (!$withTrashed && $this->db->field_exists('is_deleted', $this->table)) {
			$baseQuery->where($this->table . '.is_deleted', false);
		}

		if (!empty($filters)) {
			if (key_exists('query', $filters) && $filters['query']) {
				return $baseQuery;
			}

			if (key_exists('search', $filters) && !empty($filters['search'])) {
				$baseQuery->group_start();
				foreach ($this->filteredFields as $filteredField) {
					if ($filteredField == '*') {
						$fields = $this->db->list_fields($this->table);
						foreach ($fields as $field) {
							$baseQuery->or_like($this->table . '.' . $field, trim($filters['search']));
						}
					} else {
						$baseQuery->or_like($filteredField, trim($filters['search']));
					}
				}
				$baseQuery->group_end();
			}

			if (key_exists('status', $filters) && !empty($filters['status'])) {
				if ($this->db->field_exists('status', $this->table)) {
					$baseQuery->where($this->table . '.status', $filters['status']);
				}
			}

			if (key_exists('users', $filters) && !empty($filters['users'])) {
				if ($this->db->field_exists('id_user', $this->table)) {
					$baseQuery->where_in($this->table . '.id_user', $filters['users']);
				}
			}

			if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
				if ($this->db->field_exists('created_at', $this->table)) {
					$baseQuery->where($this->table . '.created_at>=', format_date($filters['date_from']));
				}
			}

			if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
				if ($this->db->field_exists('created_at', $this->table)) {
					$baseQuery->where($this->table . '.created_at<=', format_date($filters['date_to']));
				}
			}

			if (key_exists('role', $filters) && !empty($filters['role'])) {
				$baseQuery->where('prv_roles.id', $filters['role']);
			}

			if (key_exists('page', $filters) && !empty($filters['page'])) {
				$currentPage = $filters['page'];
			}

			if (key_exists('per_page', $filters) && !empty($filters['per_page'])) {
				$perPage = $filters['per_page'];
			}
		}
		$this->db->stop_cache();

		if (key_exists('page', $filters) && !empty($filters['page'])) {
			$totalData = $this->db->count_all_results();

			if (key_exists('sort_by', $filters) && !empty($filters['sort_by'])) {
				if (key_exists('order_method', $filters) && !empty($filters['order_method'])) {
					$baseQuery->order_by($filters['sort_by'], $filters['order_method']);
				} else {
					$baseQuery->order_by($filters['sort_by'], 'asc');
				}
			} else {
				$baseQuery->order_by($this->table . '.' . $this->id, 'desc');
			}
			$pageData = $baseQuery->limit($perPage, ($currentPage - 1) * $perPage)->get()->result_array();

			$this->db->flush_cache();

			return [
				'_paging' => true,
				'total_data' => $totalData,
				'total_page_data' => count($pageData),
				'total_page' => ceil($totalData / $perPage),
				'per_page' => $perPage,
				'current_page' => $currentPage,
				'data' => $pageData
			];
		} else {
			$baseQuery->order_by($this->table . '.' . $this->id, 'desc');
		}

		$data = $baseQuery->get()->result_array();

		$this->db->flush_cache();

		return $data;
	}

	/**
	 * Get users that owned certain role or group.
	 *
	 * @param $roles
	 * @param bool $strict
	 * @return array
	 */
	public function getByRole($roles, $strict = true)
	{
		$this->db->select($this->table . '.*')
			->distinct()
			->from($this->table)
			->join('prv_user_roles', 'prv_users.id = prv_user_roles.id_user')
			->join('prv_roles', 'prv_user_roles.id_role = prv_roles.id');

		if (is_array($roles)) {
			$this->db->where_in('prv_roles.role', $roles);
		} else {
			$this->db->where('prv_roles.role', $roles);
		}

		if ($strict) {
			$this->db->where('prv_roles.is_deleted', false);
			$this->db->where($this->table . '.is_deleted', false);
			$this->db->where($this->table . '.status', self::STATUS_ACTIVATED);
		}

		return $this->db->get()->result_array();
	}

	/**
	 * Get users that owned certain permission.
	 *
	 * @param $permissions
	 * @param bool $strict
	 * @return array
	 */
	public function getByPermission($permissions, $strict = true)
	{
		$this->db->select($this->table . '.*')
			->distinct()
			->from($this->table)
			->join('prv_user_roles', 'prv_users.id = prv_user_roles.id_user')
			->join('prv_roles', 'prv_user_roles.id_role = prv_roles.id')
			->join('prv_role_permissions', 'prv_roles.id = prv_role_permissions.id_role')
			->join('prv_permissions', 'prv_role_permissions.id_permission = prv_permissions.id');

		if (is_array($permissions)) {
			$this->db->where_in('prv_permissions.permission', $permissions);
		} else {
			$this->db->where('prv_permissions.permission', $permissions);
		}

		if ($strict) {
			$this->db->where('prv_roles.is_deleted', false);
			$this->db->where($this->table . '.is_deleted', false);
			$this->db->where($this->table . '.status', self::STATUS_ACTIVATED);
		}

		return $this->db->get()->result_array();
	}

	/**
	 * Get unattached user from employee.
	 *
	 * @param null $exceptId
	 * @return array
	 */
	public function getUnattachedUsers($exceptId = null)
	{
		$users = $this->getBaseQuery()
			->where('prv_users.username!=', 'admin')
			->where('ref_employees.id_user', null);

		if (!empty($exceptId)) {
			$users->or_where($this->table . '.id', $exceptId);
		}

		return $users->get()->result_array();
	}

	/**
	 * Get total active users.
	 *
	 * @return int
	 */
	public function getTotalActiveUsers()
	{
		return $this->db->from($this->table)
			->where('status', self::STATUS_ACTIVATED)
			->count_all_results();
	}

	/**
	 * Check user authentication and remembering login.
	 *
	 * @param string $username
	 * @param string $password
	 * @return bool
	 */
	public function authenticate($username, $password)
	{
		$usernameField = 'username';
		$isEmail = filter_var($username, FILTER_VALIDATE_EMAIL);
		if ($isEmail) {
			$usernameField = 'email';
		}

		$user = $this->db->get_where($this->table, [
			$usernameField => $username
		]);

		if ($user->num_rows() > 0) {
			$result = $user->row_array();
			if ($result['status'] != UserModel::STATUS_ACTIVATED) {
				return $result['status'];
			}
			$hashedPassword = $result['password'];
			if (password_verify($password, $hashedPassword)) {
				if (password_needs_rehash($hashedPassword, PASSWORD_BCRYPT)) {
					$newHash = password_hash($password, PASSWORD_BCRYPT);
					$this->db->update($this->table, ['password' => $newHash], ['id' => $result['id']]);
				}
				$this->session->set_userdata([
					'auth.id' => $result['id'],
					'auth.is_logged_in' => true
				]);
				return true;
			}
		}
		return false;
	}

	/**
	 * Destroy user's session
	 */
	public function logout()
	{
		if ($this->session->has_userdata('auth.id')) {
			$this->session->unset_userdata([
				'auth.id', 'auth.is_logged_in', 'auth.remember_me', 'auth.remember_token'
			]);
			$this->session->sess_destroy();
			return true;
		}
		return false;
	}

	/**
	 * Check if user has logged in from everywhere.
	 * @return bool
	 */
	public static function isLoggedIn()
	{
		$CI = get_instance();
		$sessionUserId = $CI->session->userdata('auth.id');
		if (is_null($sessionUserId) || $sessionUserId == '') {
			return false;
		}
		return true;
	}

	/**
	 * Get authenticate user data.
	 * @param string $key
	 * @param string $default
	 * @return mixed
	 */
	public static function loginData($key = '', $default = '')
	{
		$CI = get_instance();
		$id = 0;
		if ($CI->session->has_userdata('auth.id')) {
			$id = $CI->session->userdata('auth.id');
		}
		$result = $CI->db->select([
			'prv_users.*',
			'IFNULL(ref_lecturers.id,ref_students.id) AS id_civitas',
			'IFNULL(ref_students.id_pembimbing,null) AS id_pembimbing',
			'IF(ref_lecturers.id IS NULL, IF(ref_students.id IS NULL,"ADMIN","MAHASISWA"),"DOSEN") AS civitas_type',
		])
			->from(UserModel::$tableUser)
			->join(LecturerModel::$tableLecturer, 'ref_lecturers.id_user = prv_users.id', 'left')
			->join(StudentModel::$tableStudent, 'ref_students.id_user = prv_users.id', 'left')
			->where('prv_users.id', $id)
			->get();
		$userData = $result->row_array();

		if ($userData == null || count($userData) <= 0) {
			return $default;
		}

		if (!is_null($key) && $key != '') {
			if (key_exists($key, $userData)) {
				return is_null($userData[$key]) || $userData[$key] == '' ? $default : $userData[$key];
			}
			return $default;
		}
		return $userData;
	}

	/**
	 * Check if given email is unique.
	 *
	 * @param $email
	 * @param int $exceptId
	 * @return bool
	 */
	public function isUniqueEmail($email, $exceptId = 0)
	{
		$user = $this->db->get_where($this->table, [
			'email' => $email,
			'id != ' => $exceptId
		]);

		if ($user->num_rows() > 0) {
			return false;
		}
		return true;
	}

	/**
	 * Check if given username is unique.
	 *
	 * @param $username
	 * @param int $exceptId
	 * @return bool
	 */
	public function isUniqueUsername($username, $exceptId = 0)
	{
		$user = $this->db->get_where($this->table, [
			'username' => $username,
			'id != ' => $exceptId
		]);

		if ($user->num_rows() > 0) {
			return false;
		}
		return true;
	}

	/**
	 * Create new users.
	 *
	 * @param $data
	 * @return bool
	 */
	public function create($data)
	{
		if (!$this->db->field_exists('user_type', $this->table)) {
			unset($data['user_type']);
		}
		return parent::create($data);
	}

	/**
	 * Attach application
	 * @param $userId
	 * @return bool
	 */
	public function addAccessToApplication($userId)
	{
		$application = $this->db->from($this->tableApplication)
			->like("TRIM(TRAILING '/' FROM prv_applications.url)", rtrim(site_url(), '/'))
			->limit(1)
			->get()
			->row_array();

		if (!empty($application)) {
			return $this->db->insert($this->tableUserApplication, [
				'id_user' => $userId,
				'id_application' => $application['id'],
				'is_default' => true
			]);
		}
		return true;
	}
}

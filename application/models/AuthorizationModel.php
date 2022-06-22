<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AuthorizationModel extends App_Model
{
    /**
     * AuthorizationModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Check if user is logged in.
     *
     * @param string $defaultRedirect
     */
    public static function mustLoggedIn($defaultRedirect = 'auth/login')
    {
        if (!UserModel::isLoggedIn()) {
            redirect($defaultRedirect, false);
        }
    }

    /**
     * Quick check user permission.
     *
     * @param $permission
     * @param null $module
     */
    public static function mustAuthorized($permission, $module = null)
    {
        if (self::isUnauthorized($permission)) {
            self::redirectUnauthorized($module);
        }
    }

	/**
	 * Redirecting unauthorized route.
	 *
	 * @param null $message
	 * @param null $redirectModule
	 * @return string
	 */
	public static function redirectUnauthorized($message = null, $redirectModule = null)
	{
		$CI = get_instance();
		$message = empty($message) ? 'You are <strong>UNAUTHORIZED</strong> to perform this action.' : $message;

		if ($CI->input->is_ajax_request()) {
			return $message;
		} else {
			$CI->session->set_flashdata([
				'status' => 'danger',
				'message' => $message,
			]);
		}

		$redirectModule = if_empty($redirectModule, 'dashboard');

		redirect(if_empty(get_instance()->agent->referrer(), $redirectModule));

		return true;
	}

    /**
     * Check if given or logged user has a permission.
     *
     * @param $permission
     * @param int $userId
     * @return bool
     */
    public static function isAuthorized($permission, $userId = null)
    {
        return self::checkAuthorization($permission, $userId, true);
    }

    /**
     * Check if given or logged user is unauthorized of a permission.
     *
     * @param string $permission
     * @param null|integer $userId
     * @return bool
     */
    public static function isUnauthorized($permission, $userId = null)
    {
        return AuthorizationModel::checkAuthorization($permission, $userId, false);
    }

    /**
     * Check authorization by granted or denied point of view.
     *
     * @param $permission
     * @param $userId
     * @param bool $grantedCheck
     * @return bool
     */
    private static function checkAuthorization($permission, $userId, $grantedCheck = true)
    {
        $CI = get_instance();
        if ($userId == null) {
            $userId = UserModel::loginData('id', 0);
        }
        $permissionQuery = $CI->db->select('prv_permissions.id, prv_permissions.permission')
            ->from(UserModel::$tableUser)
            ->join('prv_user_roles', 'prv_users.id = prv_user_roles.id_user')
            ->join('prv_roles', 'prv_user_roles.id_role = prv_roles.id')
            ->join('prv_role_permissions', 'prv_roles.id = prv_role_permissions.id_role')
            ->join('prv_permissions', 'prv_role_permissions.id_permission = prv_permissions.id')
            ->where('prv_users.id', $userId)
            ->group_start()
            ->where_in('prv_permissions.permission', $permission)
            ->or_where('prv_permissions.permission', PERMISSION_ALL_ACCESS)
            ->or_where('prv_users.username', 'admin')
            ->group_end()
            ->get();

        if ($permissionQuery->num_rows() > 0) {
            if ($grantedCheck) {
                return true;
            } else {
                log_message('info', 'User ID ' . $userId . ' has no permission : ' . json_encode($permission));
                return false;
            }
        }

        if ($grantedCheck) {
            log_message('info', 'User ID ' . $userId . ' has no permission : ' . json_encode($permission));
            return false;
        } else {
            return true;
        }
    }

    /**
     * Check if a user has a role.
     *
     * @param $role
     * @param null|integer $userId
     * @return bool
     */
    public static function hasRole($role, $userId = null)
    {
        $CI = get_instance();
        if ($userId == null) {
            $userId = UserModel::loginData('id', 0);
        }
        $permission = $CI->db->select('prv_roles.id, prv_roles.role')
            ->from(UserModel::$tableUser)
            ->join('prv_user_roles', 'prv_users.id = prv_user_roles.id_user')
            ->join('prv_roles', 'prv_user_roles.id_role = prv_roles.id')
            ->where('prv_users.id', $userId)
            ->where('prv_roles.role', $role)
            ->get();
        if ($permission->num_rows() > 0) {
            return true;
        } else {
            log_message('info', 'User ID ' . $userId . ' has no role : ' . $role);
        }
        return false;
    }

    /**
     * Check if a user has a role.
     *
     * @param $permissions
     * @param bool $mustHaveAll
     * @param null|integer $userId
     * @return bool
     */
    public static function hasPermission($permissions, $mustHaveAll = false, $userId = null)
    {
        $CI = get_instance();
        if ($userId == null) {
            $userId = UserModel::loginData('id', 0);
        }
        $baseQuery = $CI->db->select('prv_permissions.id, prv_permissions.permission')
            ->from(UserModel::$tableUser)
            ->join('prv_user_roles', 'prv_users.id = prv_user_roles.id_user')
            ->join('prv_roles', 'prv_user_roles.id_role = prv_roles.id')
            ->join('prv_role_permissions', 'prv_roles.id = prv_role_permissions.id_role')
            ->join('prv_permissions', 'prv_role_permissions.id_permission = prv_permissions.id')
            ->where('prv_users.id', $userId);

        $baseQuery->group_start();
        if (is_array($permissions)) {
            if ($mustHaveAll) {
                $baseQuery->where_in('prv_permissions.permission', $permissions);
            } else {
                foreach ($permissions as $permission) {
                    $baseQuery->or_where('prv_permissions.permission', $permission);
                }
            }
        } else {
            $baseQuery->where('prv_permissions.permission', $permissions);
        }
        $baseQuery->or_where('prv_permissions.permission', PERMISSION_ALL_ACCESS);
        $baseQuery->or_where('prv_users.username', 'admin');
        $baseQuery->group_end();

        $total = $baseQuery->get()->num_rows();
        if ($total > 0) {
            return true;
        } else {
            log_message('info', 'User ID ' . $userId . ' has no permission : ' . json_encode($permissions));
        }
        return false;
    }

    /**
     * Check application access.
     *
     * @param $userId
     * @return bool
     */
    public static function hasApplicationAccess($userId = null)
    {
        $CI = get_instance();
        if ($userId == null) {
            $userId = UserModel::loginData('id', 0);
        }
        $CI->db = $CI->load->database('sso', true);

        $applications = $CI->db->select()->from('prv_users')
            ->distinct()
            ->join('prv_user_applications', 'prv_user_applications.id_user = prv_users.id')
            ->join('prv_applications', 'prv_applications.id = prv_user_applications.id_application')
            ->where('prv_user_applications.id_user', $userId)
            ->where("TRIM(TRAILING '/' FROM " . ('prv_applications.url') . ")=", rtrim(site_url(), '/'));

        $total = $applications->get()->num_rows();

        $CI->db = $CI->load->database('default', true);

        if ($total > 0) {
            return true;
        } else {
            $message = 'You are <strong>UNAUTHORIZED</strong> to access this application.';

            if ($CI->input->is_ajax_request()) {
                return $message;
            } else {
                $CI->session->set_flashdata([
                    'status' => 'danger',
                    'message' => $message,
                ]);
            }

            log_message('info', 'User ID ' . $userId . ' has no access to application ' . site_url('/'));
        }
        return false;
    }
}

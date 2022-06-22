<?php
/**
 * Created by PhpStorm.
 * User: angga
 * Date: 24/06/18
 * Time: 1:39
 */

class Logging
{
    /**
     * Capture request and put into log access.
     */
    public function logAccess()
    {
        if (UserModel::isLoggedIn()) {
            $CI = get_instance();
            $CI->load->library('user_agent');

            $CI->db->insert('logs', [
                'event_type' => 'access',
                'event_access' => str_replace(['-', '_'], ' ', strtoupper(get_class($CI))),
                'data' => json_encode([
                    'host' => site_url('/', false),
                    'path' => uri_string(),
                    'query' => $_SERVER['QUERY_STRING'],
                    'ip' => $CI->input->ip_address(),
                    'platform' => $CI->agent->platform(),
                    'browser' => $CI->agent->browser(),
                    'is_mobile' => $CI->agent->is_mobile(),
                ]),
                'created_by' => UserModel::loginData('id', 0),
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
}
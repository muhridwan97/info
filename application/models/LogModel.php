<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LogModel extends App_Model
{
    protected $table = 'logs';

    /**
     * Get active record query builder for all related position data selection.
     *
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery()
    {
        $baseQuery = $this->db->select([
            'logs.*',
            'prv_users.name AS creator_name',
        ])
            ->from($this->table)
            ->join(UserModel::$tableUser, 'prv_users.id = logs.created_by', 'left');

        return $baseQuery;
    }

    /**
     * Create log data.
     *
     * @param $data
     * @return bool
     */
    public function create($data)
    {
        $userId = UserModel::loginData('id');
        $log = $this->db->from($this->table)
            ->where('created_by', $userId)
            ->order_by('id', 'desc')
            ->get()
            ->row_array();

        $createLog = false;
        if (!empty($log)) {
            $timeDiff = strtotime('now') - strtotime($log['created_at']);
            if (strtolower($log['event_access']) != strtolower($data['event_access']) || $timeDiff > 300) {
                $createLog = true;
            }
        } else {
            $createLog = true;
        }

        if ($createLog) {
            $data['data'] = json_encode($data['data']);
            return $this->db->insert($this->table, $data);
        }
        return true;
    }

}
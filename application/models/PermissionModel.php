<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PermissionModel extends App_Model
{
    protected $table = 'prv_permissions';

    /**
     * Get base query of table.
     *
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery()
    {
        return $this->db->select([$this->table . '.*'])
            ->from($this->table)
            ->order_by('module,submodule');
    }

    /**
     * Get permissions of a role.
     *
     * @param int $id
     * @return array
     */
    public function getByRole($id)
    {
        $permissions = $this->getBaseQuery()
            ->join('prv_role_permissions', 'prv_role_permissions.id_permission = prv_permissions.id')
            ->join('prv_roles', 'prv_role_permissions.id_role = prv_roles.id')
            ->where('prv_roles.id', $id);

        return $permissions->get()->result_array();
    }
}

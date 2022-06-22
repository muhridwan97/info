<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MenuModel extends App_Model
{
    protected $table = 'ref_menus';

    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_INACTIVE = 'INACTIVE';

    public function __construct()
    {
        if ($this->config->item('sso_enable')) {
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
        ]);

        $this->addFilteredMap('parent','ref_menus.id_parent');


        $baseQuery = $this->db->select([
            $this->table . '.*',
        ])
            ->from($this->table);

        return $baseQuery;
    }

    /**
     * Get all data model.
     *
     * @param array $filters
     * @param bool $withTrashed
     * @return mixed
     */
    public function getAll($filters = [], $withTrashed = false)
    {
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
                foreach ($this->filteredFields as $filteredField) {
                    if ($filteredField == '*') {
                        $fields = $this->db->list_fields($this->table);
                        foreach ($fields as $field) {
                            $baseQuery->or_having($this->table . '.' . $field . ' LIKE', '%' . trim($filters['search']) . '%');
                        }
                    } else {
                        $baseQuery->or_having($filteredField . ' LIKE', '%' . trim($filters['search']) . '%');
                    }
                }
            }

            if (key_exists('status', $filters) && !empty($filters['status'])) {
                if ($this->db->field_exists('status', $this->table)) {
                    $baseQuery->where_in($this->table . '.status', explode(',', $filters['status']));
                }
            }

            if (key_exists('users', $filters) && !empty($filters['users'])) {
                if ($this->db->field_exists('id_user', $this->table)) {
                    $baseQuery->where_in($this->table . '.id_user', $filters['users']);
                }
            }

            if (key_exists('employees', $filters) && !empty($filters['employees'])) {
                if ($this->db->field_exists('id_employee', $this->table)) {
                    $baseQuery->where_in($this->table . '.id_employee', $filters['employees']);
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

            if (key_exists('parentNull', $filters) && !empty($filters['parentNull'])) {
                if ($this->db->field_exists('id_parent', $this->table)) {
                    $baseQuery->where($this->table . '.id_parent is null');
                }
            }

			if (!empty($this->filteredMaps)) {
				foreach ($this->filteredMaps as $filterKey => $filterField) {
					if (is_callable($filterField)) {
						$filterField($baseQuery, $filters);
					} elseif (key_exists($filterKey, $filters) && !empty($filters[$filterKey])) {
						if (is_array($filters[$filterKey])) {
							$baseQuery->where_in($filterField, $filters[$filterKey]);
						} else {
							$baseQuery->where($filterField, $filters[$filterKey]);
						}
					}
				}
			}
        }
        $this->db->stop_cache();

        if (key_exists('per_page', $filters) && !empty($filters['per_page'])) {
            $perPage = $filters['per_page'];
        } else {
            $perPage = 25;
        }

        if (key_exists('page', $filters) && !empty($filters['page'])) {
            $currentPage = $filters['page'];

            //$totalData = $this->db->count_all_results();

            $queryTax = $this->db->get_compiled_select();
            $totalQuery = $this->db->query("SELECT COUNT(*) AS total_record FROM ({$queryTax}) AS report");
            $totalRows = $totalQuery->row_array();
            if (!empty($totalRows)) {
                $totalData = $totalRows['total_record'];
            } else {
                $totalData = 0;
            }

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
        }

        if (key_exists('sort_by', $filters) && !empty($filters['sort_by'])) {
            if (key_exists('order_method', $filters) && !empty($filters['order_method'])) {
                $baseQuery->order_by($filters['sort_by'], $filters['order_method']);
            } else {
                $baseQuery->order_by($filters['sort_by'], 'asc');
            }
        } else {
            $baseQuery->order_by($this->table . '.' . $this->id, 'desc');
        }

        $data = $baseQuery->get()->result_array();

        $this->db->flush_cache();

        return $data;
    }

}

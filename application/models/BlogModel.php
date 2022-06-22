<?php
defined('BASEPATH') or exit('No direct script access allowed');

class BlogModel extends App_Model
{
    protected $table = 'blogs';
    protected $tableUser = 'prv_users';
    protected $tableStudent = 'ref_students';

    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_INACTIVE = 'INACTIVE';
    const STATUS_REJECTED = 'REJECTED';
    const STATUS_PENDING = 'PENDING';

    public function __construct()
    {
        if ($this->config->item('sso_enable')) {
            $this->tableUser = env('DB_SSO_DATABASE') . '.prv_users';
            $this->tableStudent = env('DB_LETTER_DATABASE') . '.ref_students';
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
            'user.name',
        ]);
        $this->addFilteredMap('writed_by', 'writed_by');

        $baseQuery = $this->db->select([
            $this->table . '.*',
            'user.name AS writer_name',
            'ref_categories.category',
            'ref_students.no_student',
            'ref_students.name AS student_name',
        ])
            ->from($this->table)
            ->join($this->tableUser . ' as user', 'user.id = ' . $this->table . '.writed_by', 'left')
            ->join('ref_categories','ref_categories.id = '. $this->table . '.id_category', 'left')
            ->join($this->tableStudent. ' AS ref_students','ref_students.id_user = '. $this->table . '.writed_by', 'left');

        return $baseQuery;
    }

    //ambil data mahasiswa dari database
    function get_blog_list($filters)
    {
        $limit = $filters['limit'];
        $start = $filters['start'];   
		// print_debug($filters);
        $baseQuery = $this->getBaseQuery();
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
        if (key_exists('category', $filters) && !empty($filters['category'])) {
            $baseQuery->where('ref_categories.category', $filters['category']);
        }
        if (key_exists('slug', $filters) && !empty($filters['slug'])) {
            $baseQuery->where('ref_categories.slug', $filters['slug']);
        }
        if (key_exists('writer', $filters) && !empty($filters['writer'])) {
            $baseQuery->where($this->table.'.writed_by', $filters['writer']);
        }
        if (key_exists('status', $filters) && !empty($filters['status'])) {
            $baseQuery->where($this->table.'.status', $filters['status']);
        }
        $baseQuery->where($this->table . '.is_deleted', false);
        $data = $baseQuery->limit($limit, $start)->get()->result_array();
        return $data;
    }

    function getBestWriter()
    {
        $baseQuery = $this->getBaseQuery()->select([
            'COUNT('.$this->table . '.id) AS count_blog'
        ]);
        $baseQuery->where($this->table . '.is_deleted', false);
        $baseQuery->where($this->table . '.status', BlogModel::STATUS_ACTIVE);
        $baseQuery->order_by('COUNT('.$this->table . '.id)', 'desc');
        $baseQuery->group_by($this->table . '.writed_by');
        $data = $baseQuery->limit(5)->get()->result_array();
        return $data;
    }

    function getBestBlog()
    {
        $baseQuery = $this->getBaseQuery()
                ->select([
                    'COUNT(likes.id) AS count_like'
                ])
                ->join('likes','likes.id_reference = '.$this->table . '.id', 'left');
        $baseQuery->where($this->table . '.is_deleted', false);
        $baseQuery->where($this->table . '.status', BlogModel::STATUS_ACTIVE);
        $baseQuery->order_by('COUNT(likes.id)', 'desc');
        $baseQuery->group_by($this->table . '.id');
        $data = $baseQuery->limit(5)->get()->result_array();
        return $data;
    }

    /**
     * Update model.
     *
     * @param $data
     * @param $id
     * @return bool
     */
    public function updating($data, $id)
    {
        $condition = is_null($id) ? null : [$this->id => $id];
        if (is_array($id)) {
            $condition = $id;
        }

        return $this->db->update($this->table, $data, $condition);
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

            if (key_exists('category', $filters) && !empty($filters['category'])) {
                if ($this->db->field_exists('id_category    ', $this->table)) {
                    $baseQuery->where($this->table . '.id_category  ', $filters['category']);
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

        if (key_exists('limit', $filters) && !empty($filters['limit'])) {
            $data = $baseQuery->limit($filters['limit'])->get()->result_array();
            $this->db->flush_cache();

            return $data;
        }

        $data = $baseQuery->get()->result_array();

        $this->db->flush_cache();

        return $data;
    }
}

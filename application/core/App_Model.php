<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App_Model extends CI_Model
{
    protected $table = '';
    protected $id = 'id';
    protected $filteredFields = ['*'];
    protected $filteredMaps = [];

    protected $morphColumnType = 'type';
    protected $morphColumnRef = 'id_reference';

    /**
     * Set field to filtered list.
     *
     * @param $fields
     */
    protected function addFilteredField($fields)
    {
        if (is_array($fields)) {
            foreach ($fields as $field) {
                if (!in_array($field, $this->filteredFields)) {
                    $this->filteredFields[] = $field;
                }
            }
        } else {
            if (!in_array($fields, $this->filteredFields)) {
                $this->filteredFields[] = $fields;
            }
        }
    }

    /**
     * Set field to map as filter list.
     *
     * @param $key
     * @param $field
     */
    protected function addFilteredMap($key, $field)
    {
        $this->filteredMaps[$key] = $field;
    }

    /**
     * Get base query of table.
     *
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery()
    {
        return $this->db->select([$this->table . '.*'])->from($this->table);
    }

	/**
	 * Get all data with pagination params.
	 *
	 * @param array $filters
	 * @param false $withTrashed
	 * @return array|CI_DB_query_builder|mixed
	 */
    public function getAllWithPagination($filters = [], $withTrashed = false)
	{
		$filters['page'] = get_if_exist($filters, 'page', get_url_param('page', 1));

		return $this->getAll($filters);
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

    /**
     * Get single model data by id with or without deleted record.
     *
     * @param $modelId
     * @param bool $withTrashed
     * @return mixed
     */
    public function getById($modelId, $withTrashed = false)
    {
        $baseQuery = $this->getBaseQuery();

        $baseQuery->where($this->table . '.' . $this->id, $modelId);

        if (!$withTrashed && $this->db->field_exists('is_deleted', $this->table)) {
            $baseQuery->where($this->table . '.is_deleted', false);
        }

        return $baseQuery->get()->row_array();
    }

    /**
     * Get data by custom condition.
     *
     * @param $conditions
     * @param bool $resultRow
     * @param bool $withTrashed
     * @return array|int
     */
    public function getBy($conditions, $resultRow = false, $withTrashed = false)
    {
        $baseQuery = $this->getBaseQuery()->order_by($this->table . '.id', 'asc');

        foreach ($conditions as $key => $condition) {
            if(is_array($condition)) {
                if(!empty($condition)) {
                    $baseQuery->where_in($key, $condition);
                }
            } else {
                $baseQuery->where($key, $condition);
            }
        }

        if (!$withTrashed && $this->db->field_exists('is_deleted', $this->table)) {
            $baseQuery->where($this->table . '.is_deleted', false);
        }

        if($resultRow === 'COUNT') {
            return $baseQuery->count_all_results();
        } else if ($resultRow) {
            return $baseQuery->get()->row_array();
        }

        return $baseQuery->get()->result_array();
    }

	/**
	 * Get by morph owner.
	 *
	 * @param $type
	 * @param $referenceId
	 * @param bool $resultRow
	 * @param bool $withTrashed
	 * @return array|int
	 */
    public function getByMorph($type, $referenceId, $resultRow = false, $withTrashed = false)
	{
		return $this->getBy([
			$this->table . '.' . $this->morphColumnType => $type,
			$this->table . '.' . $this->morphColumnRef => $referenceId
		], $resultRow, $withTrashed);
	}

    /**
     * Search data from entity.
	 *
     * @param $key
     * @param int $limit
     * @param bool $withTrashed
     * @return array
     */
    public function search($key, $limit = 10, $withTrashed = false)
    {
        $baseQuery = $this->getBaseQuery();

		$singleSpaceKeywords = trim(preg_replace('/\s+/', ' ', $key));
		$isEnclosedWithQuotes = preg_match("/^\".*\"$/", $singleSpaceKeywords) || preg_match("/^'.*'$/", $singleSpaceKeywords);
		if ($isEnclosedWithQuotes) {
			$keywords = [trim($key, '"\'')];
		} else {
			$keywords = power_set(explode(' ', $singleSpaceKeywords));
			foreach ($keywords as &$keyword) {
				$keyword = implode(' ', $keyword);
			}
		}

		$baseQuery->group_start();
		foreach ($keywords as $keywordQuery) {
			foreach ($this->filteredFields as $filteredField) {
				if ($filteredField == '*') {
					$fields = $this->db->list_fields($this->table);
					foreach ($fields as $field) {
						$baseQuery->or_like($this->table . '.' . $field, $keywordQuery);
					}
				} else {
					$baseQuery->or_like($filteredField, $keywordQuery);
				}
			}
		}
		$baseQuery->group_end();

        if (!$withTrashed && $this->db->field_exists('is_deleted', $this->table)) {
            $baseQuery->where($this->table . '.is_deleted', false);
        }

        if (!empty($limit)) {
            $baseQuery->limit($limit);
        }

        return $baseQuery->get()->result_array();
    }

    /**
     * Get total model data.
     *
     * @param bool $withTrashed
     * @return int
     */
    public function getTotal($withTrashed = false)
    {
        $query = $this->db->from($this->table);

        if (!$withTrashed && $this->db->field_exists('is_deleted', $this->table)) {
            $query->where($this->table . '.is_deleted', false);
        }

        return $query->count_all_results();
    }

    /**
     * Create new model.
     *
     * @param $data
     * @return bool
     */
    public function create($data)
    {
        if (key_exists(0, $data) && is_array($data[0])) {
            $hasCreatedBy = $this->db->field_exists('created_by', $this->table);
            $hasCreatedAt = $this->db->field_exists('created_at', $this->table);
            foreach ($data as &$datum) {
                if ($hasCreatedBy) {
                    $datum['created_by'] = UserModel::loginData('id', 0);
                }
                if ($hasCreatedAt) {
                    $datum['created_at'] = date('Y-m-d H:i:s');
                }
            }
            return $this->db->insert_batch($this->table, $data);
        }

        if ($this->db->field_exists('created_by', $this->table) && !key_exists('created_by', $data)) {
            $data['created_by'] = UserModel::loginData('id', 0);
        }
        if ($this->db->field_exists('created_at', $this->table) && !key_exists('created_at', $data)) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update model.
     *
     * @param $data
     * @param $id
     * @return bool
     */
    public function update($data, $id)
    {
        $condition = is_null($id) ? null : [$this->id => $id];
        if (is_array($id)) {
            $condition = $id;
        }
        if ($this->db->field_exists('updated_by', $this->table) && !key_exists('updated_by', $data)) {
            $data['updated_by'] = UserModel::loginData('id', 0);
        }
        if ($this->db->field_exists('updated_at', $this->table) && !key_exists('updated_at', $data)) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        return $this->db->update($this->table, $data, $condition);
    }

    /**
     * Delete model data.
     *
     * @param int|array $id
     * @param bool $softDelete
     * @return bool
     */
    public function delete($id, $softDelete = false)
    {
        if ($softDelete && $this->db->field_exists('is_deleted', $this->table)) {
            return $this->db->update($this->table, [
                'is_deleted' => true,
                'deleted_at' => date('Y-m-d H:i:s'),
                'deleted_by' => UserModel::loginData('id')
            ], (is_array($id) ? $id : [$this->id => $id]));
        }
        return $this->db->delete($this->table, (is_array($id) ? $id : [$this->id => $id]));
    }

}

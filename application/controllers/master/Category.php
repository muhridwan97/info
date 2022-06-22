<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Category
 * @property CategoryModel $category
 * @property DepartmentModel $department
 * @property UserModel $user
 * @property Exporter $exporter
 * @property Mailer $mailer
 * @property Uploader $uploader
 */
class Category extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('CategoryModel', 'category');
        $this->load->model('DepartmentModel', 'department');
        $this->load->model('UserModel', 'user');
        $this->load->model('modules/Mailer', 'mailer');
        $this->load->model('modules/Exporter', 'exporter');
        $this->load->model('modules/Uploader', 'uploader');
    }

    /**
     * Show Category index page.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CATEGORY_VIEW);

        $filters = array_merge($_GET, ['page' => get_url_param('page', 1)]);

        $export = $this->input->get('export');
        if ($export) unset($filters['page']);

        $categories = $this->category->getAll($filters);

        if ($export) {
            $this->exporter->exportFromArray('category', $categories);
        }

        $this->render('category/index', compact('categories'));
    }

    /**
     * Show Category data.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CATEGORY_VIEW);

        $category = $this->category->getById($id);

        $this->render('category/view', compact('category'));
    }

    /**
     * Show create Category.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CATEGORY_CREATE);

        $this->render('category/create');
    }

    /**
     * Save new Category data.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CATEGORY_CREATE);

        if ($this->validate()) {
            $category = $this->input->post('category');
            $description = $this->input->post('description');
            $slug = slugify($category);

            $save = $this->category->create([
                'category' => $category,
                'slug' => $slug,
                'description' => $description,
            ]);

            if ($save) {
                flash('success', "Category {$category} successfully created", 'master/category');
            } else {
                flash('danger', "Create Category failed, try again of contact administrator");
            }
        }
        $this->create();
    }

    /**
     * Show edit Category form.
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CATEGORY_EDIT);

        $category = $this->category->getById($id);

        $this->render('category/edit', compact('category'));
    }

    /**
     * Save new Category data.
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CATEGORY_EDIT);

        if ($this->validate($this->_validation_rules($id))) {
            $category = $this->input->post('category');
            $description = $this->input->post('description');
            $slug = slugify($category);

            $category = $this->category->getById($id);

            $save = $this->category->update([
                'category' => $category,
                'slug' => $slug,
                'description' => $description,
            ], $id);

            if ($save) {
                flash('success', "User {$category} successfully updated", 'master/category');
            } else {
                flash('danger', "Update Category failed, try again of contact administrator");
            }
        }
        $this->edit($id);
    }

    /**
     * Perform deleting Category data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CATEGORY_DELETE);

        $category = $this->category->getById($id);

        if ($this->category->delete($id, true)) {
            flash('warning', "Category {$category['name']} successfully deleted");
        } else {
            flash('danger', "Delete Category failed, try again or contact administrator");
        }
        redirect('master/category');
    }

    /**
     * Return general validation rules.
     *
     * @param array $params
     * @return array
     */
    protected function _validation_rules(...$params)
    {
        $id = isset($params[0]) ? $params[0] : 0;
        return [
            'category' => [
                'trim', 'required', 'max_length[100]', ['category_name_exists', function ($name) use ($id) {
                    $this->form_validation->set_message('category_name_exists', 'The %s has been created before, try another');
                    $slug = slugify($name);
                    return empty($this->category->getBy([
                    	'ref_categories.slug' => $slug,
						'ref_categories.id!=' => $id
					]));
                }]
            ],
        ];
    }

}

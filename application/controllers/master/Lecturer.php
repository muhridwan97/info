<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Lecturer
 * @property LecturerModel $lecturer
 * @property DepartmentModel $department
 * @property UserModel $user
 * @property Exporter $exporter
 * @property Mailer $mailer
 * @property Uploader $uploader
 */
class Lecturer extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('LecturerModel', 'lecturer');
        $this->load->model('DepartmentModel', 'department');
        $this->load->model('UserModel', 'user');
        $this->load->model('modules/Mailer', 'mailer');
        $this->load->model('modules/Exporter', 'exporter');
        $this->load->model('modules/Uploader', 'uploader');
    }

    /**
     * Show Lecturer index page.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_LECTURER_VIEW);

        $filters = array_merge($_GET, ['page' => get_url_param('page', 1)]);

        $export = $this->input->get('export');
        if ($export) unset($filters['page']);

        $lecturers = $this->lecturer->getAll($filters);

        if ($export) {
            $this->exporter->exportFromArray('lecturer', $lecturers);
        }

        $this->render('lecturer/index', compact('lecturers'));
    }

    /**
     * Show Lecturer data.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_LECTURER_VIEW);

        $lecturer = $this->lecturer->getById($id);

        $this->render('lecturer/view', compact('lecturer'));
    }

    /**
     * Show create Lecturer.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_LECTURER_CREATE);

        $users = $this->user->getUnattachedUsers();

        $this->render('lecturer/create', compact('users'));
    }

    /**
     * Save new Lecturer data.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_LECTURER_CREATE);

        if ($this->validate()) {
            $lecturerNo = $this->input->post('no_lecturer');
            $name = $this->input->post('name');
            $status = $this->input->post('status');
            $position = $this->input->post('position');
            $user = $this->input->post('user');
            $description = $this->input->post('description');

            $save = $this->lecturer->create([
                'no_lecturer' => $lecturerNo,
                'id_user' => if_empty($user, null),
                'name' => $name,
                'position' => $position,
                'description' => $description,
                'status' => $status,
            ]);

            if ($save) {
                flash('success', "Lecturer {$name} successfully created", 'master/lecturer');
            } else {
                flash('danger', "Create Lecturer failed, try again of contact administrator");
            }
        }
        $this->create();
    }

    /**
     * Show edit Lecturer form.
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_LECTURER_EDIT);

        $lecturer = $this->lecturer->getById($id);
        $users = $this->user->getUnattachedUsers($lecturer['id_user']);

        $this->render('lecturer/edit', compact('users', 'lecturer'));
    }

    /**
     * Save new Lecturer data.
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_LECTURER_EDIT);

        if ($this->validate($this->_validation_rules($id))) {
            $lecturerNo = $this->input->post('no_lecturer');
            $name = $this->input->post('name');
            $status = $this->input->post('status');
            $position = $this->input->post('position');
            $user = $this->input->post('user');
            $description = $this->input->post('description');

            $lecturer = $this->lecturer->getById($id);

            $save = $this->lecturer->update([
                'id_user' => if_empty($user, null),
                'name' => $name,
                'no_lecturer' => $lecturerNo,
                'position' => $position,
                'description' => $description,
                'status' => $status,
            ], $id);

            if ($save) {
                flash('success', "User {$name} successfully updated", 'master/lecturer');
            } else {
                flash('danger', "Update Lecturer failed, try again of contact administrator");
            }
        }
        $this->edit($id);
    }

    /**
     * Perform deleting Lecturer data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_LECTURER_DELETE);

        $lecturer = $this->lecturer->getById($id);

        if ($this->lecturer->delete($id, true)) {
            flash('warning', "Lecturer {$lecturer['name']} successfully deleted");
        } else {
            flash('danger', "Delete Lecturer failed, try again or contact administrator");
        }
        redirect('master/lecturer');
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
            'no_lecturer' => [
                'trim', 'required', 'max_length[100]', ['no_lecturer_exists', function ($no) use ($id) {
                    $this->form_validation->set_message('no_lecturer_exists', 'The %s has been registered before, try another');
                    return empty($this->lecturer->getBy([
                    	'ref_lecturers.no_lecturer' => $no,
						'ref_lecturers.id!=' => $id
					]));
                }]
            ],
            'name' => 'trim|required|max_length[50]',
            'position' => 'trim|required|max_length[50]',
            'status' => 'trim|required|max_length[50]',
        ];
    }

}

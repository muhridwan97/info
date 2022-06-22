<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Student
 * @property StudentModel $student
 * @property LecturerModel $lecturer
 * @property DepartmentModel $department
 * @property UserModel $user
 * @property Exporter $exporter
 * @property Mailer $mailer
 * @property Uploader $uploader
 */
class Student extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('StudentModel', 'student');
        $this->load->model('LecturerModel', 'lecturer');
        $this->load->model('DepartmentModel', 'department');
        $this->load->model('UserModel', 'user');
        $this->load->model('modules/Mailer', 'mailer');
        $this->load->model('modules/Exporter', 'exporter');
        $this->load->model('modules/Uploader', 'uploader');

        
		$this->setFilterMethods([
			'ajax_get_pembimbing' => 'GET',
		]);
    }

    /**
     * Show Student index page.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_STUDENT_VIEW);

        $filters = array_merge($_GET, ['page' => get_url_param('page', 1)]);

        $export = $this->input->get('export');
        if ($export) unset($filters['page']);

        $students = $this->student->getAll($filters);

        if ($export) {
            $this->exporter->exportFromArray('student', $students);
        }

        $this->render('student/index', compact('students'));
    }

    /**
     * Show Student data.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_STUDENT_VIEW);

        $student = $this->student->getById($id);

        $this->render('student/view', compact('student'));
    }

    /**
     * Show create Student.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_STUDENT_CREATE);

        $users = $this->user->getUnattachedUsers();
        $pembimbings = $this->lecturer->getAll(['status' => LecturerModel::STATUS_ACTIVE]);

        $this->render('student/create', compact('users', 'pembimbings'));
    }

    /**
     * Save new Student data.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_STUDENT_CREATE);

        if ($this->validate()) {
            $studentNo = $this->input->post('no_student');
            $name = $this->input->post('name');
            $status = $this->input->post('status');
            $pembimbing = $this->input->post('pembimbing');
            $user = $this->input->post('user');
            $description = $this->input->post('description');

            $save = $this->student->create([
                'no_student' => $studentNo,
                'id_user' => if_empty($user, null),
                'id_pembimbing' => if_empty($pembimbing, null),
                'name' => $name,
                'description' => $description,
                'status' => $status,
            ]);

            if ($save) {
                flash('success', "Student {$name} successfully created", 'master/student');
            } else {
                flash('danger', "Create Student failed, try again of contact administrator");
            }
        }
        $this->create();
    }

    /**
     * Show edit Student form.
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_STUDENT_EDIT);

        $student = $this->student->getById($id);
        $users = $this->user->getUnattachedUsers($student['id_user']);
        $pembimbings = $this->lecturer->getAll(['status' => LecturerModel::STATUS_ACTIVE]);

        $this->render('student/edit', compact('users', 'student', 'pembimbings'));
    }

    /**
     * Save new Student data.
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_STUDENT_EDIT);

        if ($this->validate($this->_validation_rules($id))) {
            $studentNo = $this->input->post('no_student');
            $name = $this->input->post('name');
            $status = $this->input->post('status');
            $pembimbing = $this->input->post('pembimbing');
            $user = $this->input->post('user');
            $description = $this->input->post('description');

            $student = $this->student->getById($id);

            $save = $this->student->update([
                'id_user' => if_empty($user, null),
                'id_pembimbing' => if_empty($pembimbing, null),
                'name' => $name,
                'no_student' => $studentNo,
                'description' => $description,
                'status' => $status,
            ], $id);

            if ($save) {
                flash('success', "User {$name} successfully updated", 'master/student');
            } else {
                flash('danger', "Update Student failed, try again of contact administrator");
            }
        }
        $this->edit($id);
    }

    /**
     * Perform deleting Student data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_STUDENT_DELETE);

        $student = $this->student->getById($id);

        if ($this->student->delete($id, true)) {
            flash('warning', "Student {$student['name']} successfully deleted");
        } else {
            flash('danger', "Delete Student failed, try again or contact administrator");
        }
        redirect('master/student');
    }

    /**
	 * Get paid leave data.
	 */
	public function ajax_get_pembimbing()
	{
		$studentId = get_url_param('id_student');

		$student = $this->student->getById($studentId);

		$this->renderJson($student);
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
            'no_student' => [
                'trim', 'required', 'max_length[100]', ['no_student_exists', function ($no) use ($id) {
                    $this->form_validation->set_message('no_student_exists', 'The %s has been registered before, try another');
                    return empty($this->student->getBy([
                    	'ref_students.no_student' => $no,
						'ref_students.id!=' => $id
					]));
                }]
            ],
            'name' => 'trim|required|max_length[50]',
            'status' => 'trim|required|max_length[50]',
        ];
    }

}

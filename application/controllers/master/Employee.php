<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Employee
 * @property EmployeeModel $employee
 * @property DepartmentModel $department
 * @property UserModel $user
 * @property Exporter $exporter
 * @property Mailer $mailer
 * @property Uploader $uploader
 */
class Employee extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('EmployeeModel', 'employee');
        $this->load->model('DepartmentModel', 'department');
        $this->load->model('UserModel', 'user');
        $this->load->model('modules/Mailer', 'mailer');
        $this->load->model('modules/Exporter', 'exporter');
        $this->load->model('modules/Uploader', 'uploader');
    }

    /**
     * Show employee index page.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_EMPLOYEE_VIEW);

        $filters = array_merge($_GET, ['page' => get_url_param('page', 1)]);

        $export = $this->input->get('export');
        if ($export) unset($filters['page']);

        $employees = $this->employee->getAll($filters);

        if ($export) {
            $this->exporter->exportFromArray('Employee', $employees);
        }

        $this->render('employee/index', compact('employees'));
    }

    /**
     * Show employee data.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_EMPLOYEE_VIEW);

        $employee = $this->employee->getById($id);
        $subordinates = $this->employee->getBy(['ref_employees.id_employee' => $id]);

        $this->render('employee/view', compact('employee', 'subordinates'));
    }

    /**
     * Show create employee.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_EMPLOYEE_CREATE);

        $departments = $this->department->getAll();
        $parentEmployees = $this->employee->getAll();
        $employeeNo = $this->employee->getAutoNumberEmployee();
        $users = $this->user->getUnattachedUsers();

        $this->render('employee/create', compact('users', 'departments', 'parentEmployees', 'employeeNo'));
    }

    /**
     * Save new employee data.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_EMPLOYEE_CREATE);

        if ($this->validate()) {
            $parentEmployee = $this->input->post('parent_employee');
            $employeeNo = $this->input->post('no_employee');
            $department = $this->input->post('department');
            $name = $this->input->post('name');
            $hireDate = $this->input->post('enter_date');
            $quitDate = $this->input->post('quit_date');
            $gender = $this->input->post('gender');
            $status = $this->input->post('status');
            $positionLevel = $this->input->post('position_level');
            $position = $this->input->post('position');
            $workLocation = $this->input->post('work_location');
            $company = $this->input->post('company');
            $taxNo = $this->input->post('tax_no');
            $taxAddress = $this->input->post('tax_address');
            $idCardNo = $this->input->post('id_card_no');
            $idCardAddress = $this->input->post('id_card_address');
            $socialSecurityNo = $this->input->post('social_security_no');
            $healthInsuranceNo = $this->input->post('health_insurance_no');
            $placeOfBirth = $this->input->post('place_of_birth');
            $dateOfBirth = $this->input->post('date_of_birth');
            $contactPhone = $this->input->post('contact_phone');
            $contactMobile = $this->input->post('contact_mobile');
            $user = $this->input->post('user');
            $description = $this->input->post('description');

            $uploadedPhoto = '';
            if (!empty($_FILES['photo']['name'])) {
                $uploadFile = $this->uploader->uploadTo('photo', [
                    'destination' => 'employee/' . date('Y/m')
                ]);
                if ($uploadFile) {
                    $uploadedData = $this->uploader->getUploadedData();
                    $uploadedPhoto = $uploadedData['uploaded_path'];
                } else {
                    flash('warning', $this->uploader->getDisplayErrors());
                }
            } else {
                $uploadFile = true;
            }

            if ($uploadFile) {
                $save = $this->employee->create([
                    'no_employee' => if_empty($employeeNo, $this->employee->getAutoNumberEmployee()),
                    'id_user' => if_empty($user, null),
                    'id_employee' => if_empty($parentEmployee, null),
                    'id_department' => $department,
                    'name' => $name,
                    'enter_date' => if_empty(format_date($hireDate, 'Y-m-d'), null),
                    'quit_date' => if_empty(format_date($quitDate, 'Y-m-d'), null),
                    'gender' => $gender,
                    'position_level' => $positionLevel,
                    'position' => $position,
                    'work_location' => $workLocation,
                    'company' => $company,
                    'tax_no' => $taxNo,
                    'tax_address' => $taxAddress,
                    'id_card_no' => $idCardNo,
                    'id_card_address' => $idCardAddress,
                    'social_security_no' => $socialSecurityNo,
                    'health_insurance_no' => $healthInsuranceNo,
                    'place_of_birth' => $placeOfBirth,
                    'date_of_birth' => if_empty(format_date($dateOfBirth, 'Y-m-d'), null),
                    'contact_phone' => $contactPhone,
                    'contact_mobile' => $contactMobile,
                    'photo' => $uploadedPhoto,
                    'description' => $description,
                    'status' => $status,
                ]);

                if ($save) {
                    flash('success', "Employee {$name} successfully created", 'master/employee');
                } else {
                    flash('danger', "Create employee failed, try again of contact administrator");
                }
            }
        }
        $this->create();
    }

    /**
     * Show edit employee form.
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_EMPLOYEE_EDIT);

        $employee = $this->employee->getById($id);
        $departments = $this->department->getAll();
        $parentEmployees = $this->employee->getAll();
        $users = $this->user->getUnattachedUsers($employee['id_user']);

        $this->render('employee/edit', compact('users', 'employee', 'departments', 'parentEmployees'));
    }

    /**
     * Save new employee data.
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_EMPLOYEE_EDIT);

        if ($this->validate($this->_validation_rules($id))) {
            $parentEmployee = $this->input->post('parent_employee');
            $employeeNo = $this->input->post('no_employee');
            $department = $this->input->post('department');
            $name = $this->input->post('name');
            $hireDate = $this->input->post('enter_date');
            $quitDate = $this->input->post('quit_date');
            $gender = $this->input->post('gender');
            $status = $this->input->post('status');
            $positionLevel = $this->input->post('position_level');
            $position = $this->input->post('position');
            $workLocation = $this->input->post('work_location');
            $company = $this->input->post('company');
            $taxNo = $this->input->post('tax_no');
            $taxAddress = $this->input->post('tax_address');
            $idCardNo = $this->input->post('id_card_no');
            $idCardAddress = $this->input->post('id_card_address');
            $socialSecurityNo = $this->input->post('social_security_no');
            $healthInsuranceNo = $this->input->post('health_insurance_no');
            $placeOfBirth = $this->input->post('place_of_birth');
            $dateOfBirth = $this->input->post('date_of_birth');
            $contactPhone = $this->input->post('contact_phone');
            $contactMobile = $this->input->post('contact_mobile');
            $user = $this->input->post('user');
            $description = $this->input->post('description');

            $employee = $this->employee->getById($id);

            $uploadedPhoto = $employee['photo'];
            if (!empty($_FILES['photo']['name'])) {
                if ($this->uploader->uploadTo('photo', ['destination' => 'employee/' . date('Y/m')])) {
                    $uploadedData = $this->uploader->getUploadedData();
                    $uploadedPhoto = $uploadedData['uploaded_path'];
                }
            }

            $save = $this->employee->update([
                'id_user' => if_empty($user, null),
                'id_employee' => if_empty($parentEmployee, null),
                'id_department' => $department,
                'name' => $name,
                'no_employee' => $employeeNo,
                'enter_date' => if_empty(format_date($hireDate, 'Y-m-d'), null),
                'quit_date' => if_empty(format_date($quitDate, 'Y-m-d'), null),
                'gender' => $gender,
                'position_level' => $positionLevel,
                'position' => $position,
                'work_location' => $workLocation,
                'company' => $company,
                'tax_no' => $taxNo,
                'tax_address' => $taxAddress,
                'id_card_no' => $idCardNo,
                'id_card_address' => $idCardAddress,
                'social_security_no' => $socialSecurityNo,
                'health_insurance_no' => $healthInsuranceNo,
                'place_of_birth' => $placeOfBirth,
                'date_of_birth' => if_empty(format_date($dateOfBirth, 'Y-m-d'), null),
                'contact_phone' => $contactPhone,
                'contact_mobile' => $contactMobile,
                'photo' => $uploadedPhoto,
                'description' => $description,
                'status' => $status,
            ], $id);

            if ($save) {
                flash('success', "User {$name} successfully updated", 'master/employee');
            } else {
                flash('danger', "Update employee failed, try again of contact administrator");
            }
        }
        $this->edit($id);
    }

    /**
     * Perform deleting employee data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_EMPLOYEE_DELETE);

        $employee = $this->employee->getById($id);

        if ($this->employee->delete($id, true)) {
            if (!empty($employee['photo'])) {
                $this->uploader->delete($employee['photo']);
            }
            flash('warning', "Employee {$employee['name']} successfully deleted");
        } else {
            flash('danger', "Delete employee failed, try again or contact administrator");
        }
        redirect('master/employee');
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
            'parent_employee' => 'trim|max_length[50]',
            'department' => 'trim|required|max_length[50]',
            'no_employee' => [
                'trim', 'required', 'max_length[20]', ['no_employee_exists', function ($no) use ($id) {
                    $this->form_validation->set_message('no_employee_exists', 'The %s has been registered before, try another');
                    return empty($this->employee->getBy([
                    	'ref_employees.no_employee' => $no,
						'ref_employees.id!=' => $id
					]));
                }]
            ],
            'name' => 'trim|required|max_length[50]',
            'enter_date' => 'trim|required|max_length[500]',
            'quit_date' => 'trim|max_length[50]',
            'gender' => 'trim|required|max_length[50]',
            'position_level' => 'trim|required|max_length[50]',
            'position' => 'trim|required|max_length[50]',
            'work_location' => 'trim|required|max_length[50]',
            'company' => 'trim|required|max_length[50]',
            'status' => 'trim|required|max_length[50]',
            'tax_no' => 'trim|max_length[50]',
            'tax_address' => 'trim|max_length[100]',
            'id_card_no' => 'trim|max_length[50]',
            'id_card_address' => 'trim|max_length[100]',
            'social_security_no' => 'trim|max_length[50]',
            'health_insurance_no' => 'trim|max_length[50]',
            'place_of_birth' => 'trim|max_length[50]',
            'date_of_birth' => 'trim|max_length[50]',
            'contact_phone' => 'trim|max_length[50]',
            'contact_mobile' => 'trim|max_length[50]',
            'photo' => 'trim|max_length[300]',
            'description' => 'trim|max_length[500]',
        ];
    }

}

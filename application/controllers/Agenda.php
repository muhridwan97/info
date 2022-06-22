<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Curriculum
 * @property LecturerModel $lecturer
 * @property AgendaModel $agenda
 * @property StudentModel $student
 * @property StatusHistoryModel $statusHistory
 * @property CategoryModel $category
 * @property DepartmentModel $department
 * @property UserModel $user
 * @property Exporter $exporter
 * @property Mailer $mailer
 * @property Uploader $uploader
 */
class Agenda extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('AgendaModel', 'agenda');
        $this->load->model('StudentModel', 'student');
        $this->load->model('LecturerModel', 'lecturer');
        $this->load->model('StatusHistoryModel', 'statusHistory');
        $this->load->model('CategoryModel', 'category');

        $this->load->model('DepartmentModel', 'department');
        $this->load->model('UserModel', 'user');
        $this->load->model('modules/Mailer', 'mailer');
        $this->load->model('modules/Exporter', 'exporter');
        $this->load->model('modules/Uploader', 'uploader');

        $this->setFilterMethods([
		]);
    }

    /**
     * Show Curriculum index page.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_AGENDA_VIEW);

        $filters = array_merge($_GET, ['page' => get_url_param('page', 1)]);

        $export = $this->input->get('export');
        if ($export) unset($filters['page']);

        $civitasLoggedIn = UserModel::loginData('id_civitas', '-1');
        $civitasType = UserModel::loginData('civitas_type', 'Admin');
		if($civitasType == "DOSEN"){
            $filters['dosen'] = $civitasLoggedIn;
        }else if($civitasType == "MAHASISWA"){
            $filters['mahasiswa'] = $civitasLoggedIn;
        }
        $agendas = $this->agenda->getAll($filters);

        if ($export) {
            $this->exporter->exportFromArray('agenda', $agendas);
        }

        $this->render('agenda/index', compact('agendas'));
    }

    /**
     * Show Skripsi data.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_AGENDA_VIEW);

        $agenda = $this->agenda->getById($id);

        $this->render('agenda/view', compact('agenda'));
    }

    /**
     * Show create Research.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_AGENDA_CREATE);

        $this->render('agenda/create');
    }

    /**
     * Save new Research data.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_AGENDA_CREATE);

        if ($this->validate()) {
            $title = $this->input->post('title');
            $content = $this->input->post('content');
            $date = $this->input->post('date');

            $photo = "";
            if (!empty($_FILES['photo']['name'])) {
                $options = ['destination' => 'agenda/' . date('Y/m')];
                if ($this->uploader->uploadTo('photo', $options)) {
                    $uploadedData = $this->uploader->getUploadedData();
                    $photo = $uploadedData['uploaded_path'];
                } else {
                    flash('danger', $this->uploader->getDisplayErrors(), '_back', 'agenda/create');
                }
            }
            $this->db->trans_start();
            $this->agenda->create([
                'title' => $title,
                'content' => $content,
                'date' => format_date($date),
                'photo' => $photo,
            ]);


            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Agenda {$title} successfully created", 'agenda');
            } else {
                flash('danger', "Create Agenda failed, try again of contact administrator");
            }
        }
        $this->create();
    }
    /**
     * Show edit Curriculum form.
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_AGENDA_EDIT);

        $agenda = $this->agenda->getById($id);

        $this->render('agenda/edit', compact('agenda'));
    }

    /**
     * Save new Curriculum data.
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_AGENDA_EDIT);

        if ($this->validate($this->_validation_rules($id))) {
            $title = $this->input->post('title');
            $content = $this->input->post('content');
            $date = $this->input->post('date');

            $agenda = $this->agenda->getById($id);
            $photo = $agenda['photo'];
            if (!empty($_FILES['photo']['name'])) {
                $options = ['destination' => 'agenda/' . date('Y/m')];
                if ($this->uploader->uploadTo('photo', $options)) {
                    $uploadedData = $this->uploader->getUploadedData();
                    $photo = $uploadedData['uploaded_path'];
                } else {
                    flash('danger', $this->uploader->getDisplayErrors(), '_back', 'agenda/edit/'.$id);
                }
            }
            $this->db->trans_start();
            
            $this->agenda->update([
                'title' => $title,
                'content' => $content,
                'date' => format_date($date),
                'photo' => $photo,
            ], $id);


            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Agenda {$title} successfully updated", 'agenda');
            } else {
                flash('danger', "Create Agenda failed, try again of contact administrator");
            }
        }
        $this->edit($id);
    }

    /**
     * Perform deleting Research data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_AGENDA_DELETE);

        $agenda = $this->agenda->getById($id);
        // push any status absent to history
        $this->statusHistory->create([
            'type' => StatusHistoryModel::TYPE_AGENDA,
            'id_reference' => $id,
            'description' => "Agenda is deleted",
            'data' => json_encode($agenda)
        ]);

        if ($this->agenda->delete($id, true)) {
            flash('warning', "Agenda {$agenda['title']} successfully deleted");
        } else {
            flash('danger', "Delete Agenda failed, try again or contact administrator");
        }
        redirect('agenda');
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
            'title' => 'trim|required',
            'content' => 'trim|required',
            'date' => 'trim|required',
        ];
    }

}

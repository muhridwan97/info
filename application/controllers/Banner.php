<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Banner
 * @property LecturerModel $lecturer
 * @property BannerModel $banner
 * @property StudentModel $student
 * @property StatusHistoryModel $statusHistory
 * @property DepartmentModel $department
 * @property UserModel $user
 * @property Exporter $exporter
 * @property Mailer $mailer
 * @property Uploader $uploader
 */
class Banner extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('BannerModel', 'banner');
        $this->load->model('StudentModel', 'student');
        $this->load->model('LecturerModel', 'lecturer');
        $this->load->model('StatusHistoryModel', 'statusHistory');

        $this->load->model('DepartmentModel', 'department');
        $this->load->model('UserModel', 'user');
        $this->load->model('modules/Mailer', 'mailer');
        $this->load->model('modules/Exporter', 'exporter');
        $this->load->model('modules/Uploader', 'uploader');

        $this->setFilterMethods([
			'validate_skripsi' => 'POST|PUT',
			'print_logbook' => 'POST|GET',
		]);
    }

    /**
     * Show Banner index banner.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BANNER_VIEW);

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
        $banners = $this->banner->getAll($filters);

        if ($export) {
            $this->exporter->exportFromArray('banner', $banners);
        }

        $this->render('banner/index', compact('banners'));
    }

    /**
     * Show Skripsi data.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BANNER_VIEW);

        $banner = $this->banner->getById($id);

        $this->render('banner/view', compact('banner'));
    }

    /**
     * Show create Banner.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BANNER_CREATE);

        $this->render('banner/create');
    }

    /**
     * Save new Banner data.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BANNER_CREATE);

        if ($this->validate()) {
            $title = $this->input->post('title');
            $caption = $this->input->post('caption');
            
            $photo = "";
            if (!empty($_FILES['photo']['name'])) {
                $options = ['destination' => 'banner/' . date('Y/m')];
                if ($this->uploader->uploadTo('photo', $options)) {
                    $uploadedData = $this->uploader->getUploadedData();
                    $photo = $uploadedData['uploaded_path'];
                } else {
                    flash('danger', $this->uploader->getDisplayErrors(), '_back', 'banner/create');
                }
            }
            $this->db->trans_start();
            $this->banner->create([
                'caption' => $caption,
                'title' => $title,
                'photo' => $photo,
            ]);

            $this->db->trans_complete();
            if ($this->db->trans_status()) {
                flash('success', "Banner {$title} successfully created", 'banner');
            } else {
                flash('danger', "Create banner failed, try again of contact administrator");
            }
        }
        $this->create();
    }

    /**
     * Show edit Banner form.
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BANNER_EDIT);

        $banner = $this->banner->getById($id);

        $this->render('banner/edit', compact('banner'));
    }

    /**
     * Save new Banner data.
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BANNER_EDIT);

        if ($this->validate($this->_validation_rules($id))) {
            $title = $this->input->post('title');
            $caption = $this->input->post('caption');

            $dataBanner = $this->banner->getById($id);
            
            $photo = $dataBanner['photo'];
            if (!empty($_FILES['photo']['name'])) {
                $options = ['destination' => 'banner/' . date('Y/m')];
                if ($this->uploader->uploadTo('photo', $options)) {
                    $uploadedData = $this->uploader->getUploadedData();
                    $photo = $uploadedData['uploaded_path'];
                    //delete
                    $this->uploader->delete($dataBanner['photo']);
                } else {
                    flash('danger', $this->uploader->getDisplayErrors(), '_back', 'banner/edit/'.$id);
                }
            }
            $this->db->trans_start();
            $this->banner->update([
                'caption' => $caption,
                'title' => $title,
                'photo' => $photo,
            ],$id);

            $this->db->trans_complete();
            if ($this->db->trans_status()) {
                flash('success', "Banner {$dataBanner['title']} successfully updated", 'banner');
            } else {
                flash('danger', "Update Banner failed, try again of contact administrator");
            }
        }
        $this->edit($id);
    }

    /**
     * Perform deleting Banner data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BANNER_DELETE);

        $banner = $this->banner->getById($id);
        // push any status absent to history
        $this->statusHistory->create([
            'type' => StatusHistoryModel::TYPE_BANNER,
            'id_reference' => $id,
            'description' => "Banner is deleted",
            'data' => json_encode($banner)
        ]);

        if ($this->banner->delete($id, true)) {
            flash('warning', "Banner {$banner['banner_name']} successfully deleted");
        } else {
            flash('danger', "Delete Banner failed, try again or contact administrator");
        }
        redirect('banner');
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
            'caption' => 'trim|required',
        ];
    }

}

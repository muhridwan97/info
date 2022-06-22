<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Carbon\Carbon;
use Milon\Barcode\DNS2D;
/**
 * Class Page
 * @property LecturerModel $lecturer
 * @property PageModel $page
 * @property StudentModel $student
 * @property StatusHistoryModel $statusHistory
 * @property DepartmentModel $department
 * @property UserModel $user
 * @property Exporter $exporter
 * @property Mailer $mailer
 * @property Uploader $uploader
 */
class Page extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('PageModel', 'page');
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
     * Show Page index page.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PAGE_VIEW);

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
        $pages = $this->page->getAll($filters);

        if ($export) {
            $this->exporter->exportFromArray('page', $pages);
        }

        $this->render('page/index', compact('pages'));
    }

    /**
     * Show Skripsi data.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PAGE_VIEW);

        $page = $this->page->getById($id);

        $this->render('page/view', compact('page'));
    }

    /**
     * Show create Page.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PAGE_CREATE);

        $this->render('page/create');
    }

    /**
     * Save new Page data.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PAGE_CREATE);

        if ($this->validate()) {
            $page_name = $this->input->post('page_name');
            $content = $this->input->post('content');
            
            $photo = "";
            if (!empty($_FILES['photo']['name'])) {
                $options = ['destination' => 'page/' . date('Y/m')];
                if ($this->uploader->uploadTo('photo', $options)) {
                    $uploadedData = $this->uploader->getUploadedData();
                    $photo = $uploadedData['uploaded_path'];
                } else {
                    flash('danger', $this->uploader->getDisplayErrors(), '_back', 'page/create');
                }
            }
            $this->db->trans_start();
            $this->page->create([
                'content' => $content,
                'page_name' => $page_name,
                'photo' => $photo,
            ]);
			$pageId = $this->db->insert_id();
            $this->page->update([
                'url' => "landing/page/".$pageId
            ],$pageId);

            $this->db->trans_complete();
            if ($this->db->trans_status()) {
                flash('success', "Page {$page_name} successfully created", 'page');
            } else {
                flash('danger', "Create page failed, try again of contact administrator");
            }
        }
        $this->create();
    }

    /**
     * Show edit Page form.
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PAGE_EDIT);

        $page = $this->page->getById($id);

        $this->render('page/edit', compact('page'));
    }

    /**
     * Save new Page data.
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PAGE_EDIT);

        if ($this->validate($this->_validation_rules($id))) {
            $page_name = $this->input->post('page_name');
            $content = $this->input->post('content');

            $dataPage = $this->page->getById($id);
            
            $photo = $dataPage['photo'];
            if (!empty($_FILES['photo']['name'])) {
                $options = ['destination' => 'page/' . date('Y/m')];
                if ($this->uploader->uploadTo('photo', $options)) {
                    $uploadedData = $this->uploader->getUploadedData();
                    $photo = $uploadedData['uploaded_path'];
                    //delete
                    $this->uploader->delete($dataPage['photo']);
                } else {
                    flash('danger', $this->uploader->getDisplayErrors(), '_back', 'page/edit/'.$id);
                }
            }
            $this->db->trans_start();
            $this->page->update([
                'content' => $content,
                'page_name' => $page_name,
                'photo' => $photo,
            ],$id);

            $this->db->trans_complete();
            if ($this->db->trans_status()) {
                flash('success', "Page {$dataPage['page_name']} successfully updated", 'page');
            } else {
                flash('danger', "Update Page failed, try again of contact administrator");
            }
        }
        $this->edit($id);
    }

    /**
     * Perform deleting Page data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PAGE_DELETE);

        $page = $this->page->getById($id);
        // push any status absent to history
        $this->statusHistory->create([
            'type' => StatusHistoryModel::TYPE_PAGE,
            'id_reference' => $id,
            'description' => "Page is deleted",
            'data' => json_encode($page)
        ]);

        if ($this->page->delete($id, true)) {
            flash('warning', "Page {$page['page_name']} successfully deleted");
        } else {
            flash('danger', "Delete Page failed, try again or contact administrator");
        }
        redirect('page');
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
            'page_name' => 'trim|required',
            'content' => 'trim|required',
        ];
    }

}

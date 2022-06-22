<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Repository
 * @property RepositoryModel $repository
 * @property UserModel $user
 * @property Exporter $exporter
 * @property Mailer $mailer
 * @property Uploader $uploader
 */
class Repository extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('RepositoryModel', 'repository');
        $this->load->model('UserModel', 'user');
        $this->load->model('modules/Mailer', 'mailer');
        $this->load->model('modules/Exporter', 'exporter');
        $this->load->model('modules/Uploader', 'uploader');

        
		$this->setFilterMethods([
		]);
    }

    /**
     * Show Repository index page.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPOSITORY_VIEW);

        $filters = array_merge($_GET, ['page' => get_url_param('page', 1)]);

        $export = $this->input->get('export');
        if ($export) unset($filters['page']);

        $repositories = $this->repository->getAll($filters);

        if ($export) {
            $this->exporter->exportFromArray('repository', $repositories);
        }

        $this->render('repository/index', compact('repositories'));
    }

    /**
     * Show Repository data.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPOSITORY_VIEW);

        $repository = $this->repository->getById($id);

        $this->render('repository/view', compact('repository'));
    }

    /**
     * Show create Repository.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPOSITORY_CREATE);

        $this->render('repository/create');
    }

    /**
     * Save new Repository data.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPOSITORY_CREATE);

        if ($this->validate()) {
            $name = $this->input->post('name');
            $description = $this->input->post('description');

            
            if (!empty($_FILES['file_surat']['name'])) {
                $options = ['destination' => 'repository/' . date('Y/m')];
                if ($this->uploader->uploadTo('file_surat', $options)) {
                    $uploadedData = $this->uploader->getUploadedData();
                    $avatar = $uploadedData['uploaded_path'];
                } else {
                    flash('danger', $this->uploader->getDisplayErrors(), '_back', 'repository/create');
                }
            }else{
                flash('warning', 'You must upload file', 'repository/create');
            }
            $this->db->trans_start();
            $this->repository->create([
                'name' => $name,
                'file' => $uploadedData['file_name'],
                'src' => $uploadedData['uploaded_path'],
                'url' => base_url().'uploads/'.$uploadedData['uploaded_path'],
                'description' => $description,
            ]);


            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Repository {$name} successfully created", 'repository');
            } else {
                flash('danger', "Create Repository failed, try again of contact administrator");
            }
        }
        $this->create();
    }

    /**
     * Show edit Repository form.
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPOSITORY_EDIT);

        $repository = $this->repository->getById($id);
        $users = $this->user->getUnattachedUsers($repository['id_user']);
        $pembimbings = $this->lecturer->getAll(['status' => LecturerModel::STATUS_ACTIVE]);

        $this->render('repository/edit', compact('users', 'repository', 'pembimbings'));
    }

    /**
     * Save new Repository data.
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPOSITORY_EDIT);

        if ($this->validate($this->_validation_rules($id))) {
            $repositoryNo = $this->input->post('no_repository');
            $name = $this->input->post('name');
            $status = $this->input->post('status');
            $pembimbing = $this->input->post('pembimbing');
            $user = $this->input->post('user');
            $description = $this->input->post('description');

            $repository = $this->repository->getById($id);

            $save = $this->repository->update([
                'id_user' => if_empty($user, null),
                'id_pembimbing' => if_empty($pembimbing, null),
                'name' => $name,
                'no_repository' => $repositoryNo,
                'description' => $description,
                'status' => $status,
            ], $id);

            if ($save) {
                flash('success', "User {$name} successfully updated", 'master/repository');
            } else {
                flash('danger', "Update Repository failed, try again of contact administrator");
            }
        }
        $this->edit($id);
    }

    /**
     * Perform deleting Repository data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPOSITORY_DELETE);

        $repository = $this->repository->getById($id);

        if ($this->repository->delete($id, true)) {
            flash('warning', "Repository {$repository['name']} successfully deleted");
        } else {
            flash('danger', "Delete Repository failed, try again or contact administrator");
        }
        redirect('repository');
    }

    /**
	 * Get paid leave data.
	 */
	public function ajax_get_pembimbing()
	{
		$repositoryId = get_url_param('id_repository');

		$repository = $this->repository->getById($repositoryId);

		$this->renderJson($repository);
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
            'name' => 'trim|required|max_length[50]',
            'description' => 'trim|max_length[500]',
        ];
    }

}

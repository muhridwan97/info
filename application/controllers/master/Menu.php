<?php

use Svg\Tag\Path;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Menu
 * @property LecturerModel $lecturer
 * @property MenuModel $menu
 * @property PageModel $page
 * @property CategoryModel $category
 * @property StatusHistoryModel $statusHistory
 * @property DepartmentModel $department
 * @property UserModel $user
 * @property Exporter $exporter
 * @property Mailer $mailer
 * @property Uploader $uploader
 */
class Menu extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MenuModel', 'menu');
        $this->load->model('PageModel', 'page');
        $this->load->model('CategoryModel', 'category');
        $this->load->model('LecturerModel', 'lecturer');
        $this->load->model('StatusHistoryModel', 'statusHistory');

        $this->load->model('DepartmentModel', 'department');
        $this->load->model('UserModel', 'user');
        $this->load->model('modules/Mailer', 'mailer');
        $this->load->model('modules/Exporter', 'exporter');
        $this->load->model('modules/Uploader', 'uploader');

        $this->setFilterMethods([
			'sub' => 'POST|PUT|GET',
		]);
    }

    /**
     * Show Menu index page.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_MENU_VIEW);

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
        $filters['parentNull'] = 1;
        $menus = $this->menu->getAll($filters);

        if ($export) {
            $this->exporter->exportFromArray('menu', $menus);
        }
        $path = "Menu Utama";

        $this->render('menu/index', compact('menus','path'));
    }

    /**
     * Show sub Menu index page.
     */
    public function sub($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_MENU_VIEW);

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
        $filters['parent'] = $id;
        $menus = $this->menu->getAll($filters);

        if ($export) {
            $this->exporter->exportFromArray('menu', $menus);
        }
        $path = $this->getPath($id);
        // print_debug($path);
        $this->render('menu/index', compact('menus', 'path'));
    }

    /**
     * Show Skripsi data.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_MENU_VIEW);

        $menu = $this->menu->getById($id);

        $this->render('menu/view', compact('menu'));
    }

    /**
     * Show create Menu.
     */
    public function create($id =null)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_MENU_CREATE);

        $pages = $this->page->getAll();
        $categories = $this->category->getAll();
        $temp = [];
        foreach ($categories as $key => $category) {
            $temp=[
                'url' => "landing/blog/".$category['slug'],
                'page_name' => "Blog category ".$category['category']
            ];
            $pages[] = $temp;
        };
        $parentId = $id;

        $this->render('menu/create', compact('pages', 'parentId'));
    }

    /**
     * Save new Menu data.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_MENU_CREATE);

        if ($this->validate()) {
            $menu_name = $this->input->post('menu_name');
            $link_type = $this->input->post('link_type');
            $url = $this->input->post('url');
            $id_parent = $this->input->post('id_parent');
            $this->db->trans_start();
            if($link_type == 'INTERNAL'){
                $url = $this->input->post('url_page');
            }
            $this->menu->create([
                'link_type' => $link_type,
                'menu_name' => $menu_name,
                'url' => $url,
                'id_parent' => if_empty($id_parent, null),
            ]);
            $sub = '';
            if(!empty($id_parent)){
                $sub = '/sub/'.$id_parent;
            }

            $this->db->trans_complete();
            if ($this->db->trans_status()) {
                flash('success', "Menu {$menu_name} successfully created", 'master/menu'.$sub);
            } else {
                flash('danger', "Create menu failed, try again of contact administrator");
            }
        }
        if(!empty($id_parent)){
            $this->create($id_parent);
        }
        $this->create();
    }

    /**
     * Show edit Menu form.
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_MENU_EDIT);

        $menu = $this->menu->getById($id);
        $pages = $this->page->getAll();
        $categories = $this->category->getAll();
        $temp = [];
        foreach ($categories as $key => $category) {
            $temp=[
                'url' => "landing/blog/".$category['slug'],
                'page_name' => "Blog category ".$category['category']
            ];
            $pages[] = $temp;
        };

        $this->render('menu/edit', compact('menu', 'pages'));
    }

    /**
     * Save new Menu data.
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_MENU_EDIT);

        if ($this->validate($this->_validation_rules($id))) {
            $menu_name = $this->input->post('menu_name');
            $link_type = $this->input->post('link_type');
            $url = $this->input->post('url');

            $dataMenu = $this->menu->getById($id);
            $id_parent = $dataMenu['id_parent'];
            $this->db->trans_start();
            if($link_type == 'INTERNAL'){
                $url = $this->input->post('url_page');
            }
            $this->menu->update([
                'link_type' => $link_type,
                'menu_name' => $menu_name,
                'url' => $url,
                'id_parent' => $id_parent,
            ],$id);
            $sub = '';
            if(!empty($id_parent)){
                $sub = '/sub/'.$id_parent;
            }

            $this->db->trans_complete();
            if ($this->db->trans_status()) {
                flash('success', "Menu {$dataMenu['menu_name']} successfully updated", 'master/menu'.$sub);
            } else {
                flash('danger', "Update Menu failed, try again of contact administrator");
            }
        }
        $this->edit($id);
    }

    /**
     * Perform deleting Menu data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_MENU_DELETE);

        $menu = $this->menu->getById($id);
        // push any status absent to history
        $this->statusHistory->create([
            'type' => StatusHistoryModel::TYPE_MENU,
            'id_reference' => $id,
            // 'status' => $menu['status'],
            'description' => "Menu is deleted",
            'data' => json_encode($menu)
        ]);
        $id_parent = $menu['id_parent'];
        $sub = '';
        if(!empty($id_parent)){
            $sub = '/sub/'.$id_parent;
        }

        if ($this->menu->delete($id, true)) {
            flash('warning', "Menu {$menu['menu_name']} successfully deleted");
        } else {
            flash('danger', "Delete Menu failed, try again or contact administrator");
        }
        redirect('master/menu'.$sub);
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
            'menu_name' => 'trim|required',
        ];
    }

    private function getPath($id){
        $idNow = $id;
        $path = "";
        
        
        // <li class="breadcrumb-item"><a href="#">Library</a></li>
        // <li class="breadcrumb-item active" aria-current="page">Data</li>
        do {
            $menu = $this->menu->getById($id);
            if($idNow == $id){
                $path = '<li class="breadcrumb-item active" aria-current="page">'. $menu['menu_name'].'</li>'.$path;
            }else{
                $path = '<li class="breadcrumb-item"><a href="'.base_url().'master/menu/sub/'.$id.'">'. $menu['menu_name'].'</a></li>'.$path;
            }
            $id = $menu['id_parent'];
            // print_debug($id);
        } while (!empty($menu['id_parent']));
        $home = base_url().'master/menu';
        return '<li class="breadcrumb-item"><a href="'.$home.'">Menu Utama</a></li>'.$path;
    }
}

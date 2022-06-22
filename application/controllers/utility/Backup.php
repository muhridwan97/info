<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class System_log
 * @property LogModel $logger
 * @property Exporter $exporter
 */
class Backup extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('LogModel', 'logger');
        $this->load->model('modules/Exporter', 'exporter');

        $this->load->helper('file');
        $this->load->helper('directory');
        $this->load->helper('download');
        $this->load->library('zip');
        $this->zip->compression_level = 5;

        $this->setFilterMethods([
            'database' => 'GET',
            'upload' => 'GET',
            'app' => 'GET',
        ]);

        AuthorizationModel::mustAuthorized(PERMISSION_SETTING_EDIT);
    }

    /**
     * Show log index page.
     */
    public function index()
    {
        $this->render('backup/index');
    }

    /**
     * Backup of database file.
     */
    public function database()
    {
        $this->load->dbutil();
        $backup = $this->dbutil->backup([
            'format' => 'zip',
            'filename' => $this->db->database . '.sql'
        ]);
        force_download($this->db->database . '_' . date('Ymd') . '.zip', $backup);
    }

    /**
     * Backup of uploaded file.
     */
    public function upload()
    {
        $this->zip->read_dir(FCPATH . 'uploads', false);
        $this->zip->download('upload_' . date('Ymd') . '.zip');
    }

    /**
     * Backup of application file.
     */
    public function app()
    {
        $directories = directory_map('./', 1);
        $includes = ['application', 'system', 'vendor', 'assets', 'index.php'];
        foreach ($directories as $directory) {
            if (in_array(rtrim($directory, "/\\"), $includes)) {
                if (is_dir(FCPATH . $directory)) {
                    $this->zip->read_dir($directory, false);
                } else {
                    $this->zip->read_file($directory, false);
                }
            }
        }
        $this->zip->read_file('.env', false);
        $this->zip->read_file('.htaccess', false);
        $this->zip->download('app_' . date('Ymd') . '.zip');
    }

}
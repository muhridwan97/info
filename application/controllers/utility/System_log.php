<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class System_log
 * @property LogModel $logger
 * @property Exporter $exporter
 */
class System_log extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('LogModel', 'logger');
        $this->load->model('modules/Exporter', 'exporter');

        $this->load->helper('directory');
        $this->load->helper('download');

        $this->setFilterMethods([
            'download' => 'GET'
        ]);

        AuthorizationModel::mustAuthorized(PERMISSION_SETTING_EDIT);
    }

    /**
     * Show log index page.
     */
    public function index()
    {
        $export = $this->input->get('export');

        $path = $this->input->get('path');
        if (empty($path)) {
            $path = './application/logs';
        }
        $directories = explode('/', preg_replace('{/$}', '', $path));
        array_pop($directories);
        $logs = directory_map($path, 1);

        foreach ($logs as $index => &$file) {
            if ($file == 'index.html') {
                unset($logs[$index]);
            }
            $file = [
                'log_file' => $file,
                'file_size' => round(filesize(FCPATH . 'application/logs/' . $file) / 1000),
                'last_modified' => date("d M Y H:i", filemtime(FCPATH . 'application/logs/' . $file))
            ];
        }
        rsort($logs);

        if ($export) {
            $this->exporter->exportFromArray('Log', $logs);
        }

        $this->render('log/system', compact('logs'));
    }

    /**
     * Download log file.
     *
     * @param $logFile
     */
    public function download($logFile)
    {
        force_download(FCPATH . 'application/logs/' . $logFile, NULL);
    }

}
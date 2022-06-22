<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Access_log
 * @property LogModel $logger
 * @property Exporter $exporter
 */
class Access_log extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('LogModel', 'logger');
        $this->load->model('modules/Exporter', 'exporter');

        AuthorizationModel::mustAuthorized(PERMISSION_SETTING_EDIT);
    }

    /**
     * Show log index page.
     */
    public function index()
    {
        $filters = array_merge($_GET, ['page' => get_url_param('page', 1)]);

        $export = $this->input->get('export');
        if ($export) unset($filters['page']);

        $logs = $this->logger->getAll($filters);

        foreach ($logs['data'] as &$log) {
            $data = json_decode($log['data'], true);
            $log['data_label'] = '-';
            if ($log['event_type'] == 'access') {
                $log['data_label'] = get_if_exist($data, 'ip', '-');
            }
            if (strpos($log['event_type'], "query-") !== FALSE) {
                $log['data_label'] = get_if_exist($data, 'table', '-');
            }
        }

        if ($export) {
            $this->exporter->exportFromArray('Log', $logs);
        }

        $this->render('log/access', compact('logs'));
    }

    /**
     * Show log data.
     *
     * @param $id
     */
    public function view($id)
    {
        $log = $this->logger->getById($id);
        $data = json_decode($log['data'], true);
        if (is_array($data)) {
            $log['data'] = $data;
        }

        $this->render('log/view', compact('log'));
    }

}
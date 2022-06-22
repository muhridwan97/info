<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class History
 * @property StatusHistoryModel $statusHistory
 */
class History extends App_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('StatusHistoryModel', 'statusHistory');
    }

    /**
     * Show status history data.
     * @param $id
     */
    public function view($id)
    {
        $statusHistory = $this->statusHistory->getById($id);
        $data = json_decode($statusHistory['data'], true);
        if(is_array($data)) {
            $statusHistory['data'] = $data;
        }
        $this->render('history/view', compact('statusHistory'));
    }

}

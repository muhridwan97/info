<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Notification
 * @property NotificationModel $notification
 */
class Notification extends App_Controller
{
    /**
     * Notification constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('NotificationModel', 'notification');

        $this->setFilterMethods([
            'read_all' => 'GET|POST',
            'read' => 'GET|POST|PUT|PATCH',
            'ajax_list_notification' => 'POST',
        ]);
    }

    /**
     * Get all notification.
     */
    public function index()
    {
        $userId = UserModel::loginData('id');

        $notifications = $this->notification->getByUser($userId);

        $this->render('notification/index', compact('notifications'));
    }

    /**
     * Mark all as read
     */
    public function read_all()
    {
        $userId = UserModel::loginData('id');

        $status = $this->notification->update([
            'is_read' => 1
        ], ['id_user' => $userId]);

        if($status) {
            flash('success', 'All notification is marked as read');
        } else {
            flash('danger', 'Something went wrong');
        }

        redirect('notification');
    }

    /**
     * Redirect after read notification.
     *
     * @param $id
     */
    public function read($id)
    {
        $this->notification->update([
            'is_read' => true
        ], $id);

        $redirect = $this->input->get('redirect');

        if (empty($redirect)) {
            $redirect = 'notification';
        }

        redirect($redirect);
    }

    /**
     * ajax notification.
     *
     * @param $id
     */
    public function ajax_list_notification(){
        $this->load->view('notification/ajax_list_notification');
    }
}

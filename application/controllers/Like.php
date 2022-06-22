<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Like
 * @property LikeModel $like
 */
class Like extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('LikeModel', 'like');

        $this->setFilterMethods([
			'ajax_set_like' => 'POST|PUT|GET',
			'ajax_set_unlike' => 'POST|PUT|GET',
		]);
    }

    /**
     * Get ajax by type.
     */
    public function ajax_set_like()
    {
        if ($this->input->is_ajax_request()) {
            $blogId = $this->input->post('blogId');

            $like = $this->like->getBy([
                'id_reference' => $blogId,
                'created_by' => UserModel::loginData('id', '-1')
            ], true);
            $save = false;
            if (empty($like)) {
                $save = $this->like->create([
                    'id_reference' => $blogId,
                ]);
            }

            $this->render_json(['message' => $save]);
        }
    }

    
    /**
     * Get ajax by type.
     */
    public function ajax_set_unlike()
    {
        if ($this->input->is_ajax_request()) {
            $blogId = $this->input->post('blogId');

            $like = $this->like->getBy([
                'id_reference' => $blogId,
                'created_by' => UserModel::loginData('id', '-1')
            ], true);

            $delete = false;

            if (!empty($like)) {
                $delete = $this->like->delete($like['id']);
            }

            $this->render_json(['message' =>$delete ]);
        }
    }

}

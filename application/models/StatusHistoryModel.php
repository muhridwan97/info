<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StatusHistoryModel extends App_Model
{
    protected $table = 'status_histories';

    const TYPE_TRAINING = 'training';
    const TYPE_EXAM = 'exam';
    const TYPE_EXAM_EXERCISE = 'exam-exercise';
    
    const TYPE_PAGE = 'page';
    const TYPE_MENU = 'menu';
    const TYPE_BANNER = 'banner';
    const TYPE_BLOG = 'blog';
    const TYPE_AGENDA = 'agenda';

    /**
     * Get base query of table.
     *
     * @return CI_DB_query_builder
     */
    public function getBaseQuery()
    {
        return parent::getBaseQuery()
            ->select([
                'prv_users.name AS creator_name'
            ])
            ->join(UserModel::$tableUser, UserModel::$tableUser . '.id = status_histories.created_by', 'left');
    }
}

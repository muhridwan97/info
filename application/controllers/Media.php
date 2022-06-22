<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Media
 */
class Media extends App_Controller
{
	protected $layout = 'layouts/page';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Show media viewer.
     */
    public function view()
    {
        $source = get_url_param('source');
        $title = basename($source);

        $this->render('media/view', compact('source', 'title'));
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Search
 * @property BlogModel $blog
 * @property PageModel $page
 */
class Search extends App_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('BlogModel', 'blog');
        $this->load->model('PageModel', 'page');
    }

    /**
     * Show result search page.
     */
    public function index()
    {
        $q = get_url_param('q');
        $blogs = empty($q) ? [] : $this->blog->search($q, 10);
        $pages = empty($q) ? [] : $this->page->search($q, 10);

        $this->render('search/index', compact('blogs', 'pages'));
    }
}

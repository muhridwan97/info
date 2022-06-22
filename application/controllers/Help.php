<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Help
 */
class Help extends App_Controller
{
    /**
     * Help constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Show help page.
     */
    public function index()
    {
        $this->render('help/index');
    }

}
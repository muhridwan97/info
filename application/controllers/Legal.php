<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Legal extends App_Controller
{
    protected $layout = 'layouts/page';
    protected $strictRequest = false;

    /**
     * Show SLA static page
     */
    public function index()
    {
        $this->render('legal/index');
    }

    /**
     * Show SLA static page
     */
    public function sla()
    {
        $this->render('legal/sla');
    }

    /**
     * Show privacy static page
     */
    public function privacy()
    {
        $this->render('legal/privacy');
    }

    /**
     * Show agreement static page
     */
    public function agreement()
    {
        $this->render('legal/agreement');
    }

    /**
     * Show cookie static page
     */
    public function cookie()
    {
        $this->render('legal/cookie');
    }
}

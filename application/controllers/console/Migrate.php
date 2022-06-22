<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migrate extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (is_cli()) {
            echo 'Migration module is initiating...' . PHP_EOL;
        } else {
            echo "This module is CLI only!" . PHP_EOL;
            die();
        }
        $this->load->dbforge();
    }

    /**
     * Create new database by config value.
     * @param bool $dropOldDb
     */
    public function init($dropOldDb = true)
    {
        $dbName = $this->db->database;
        if ($dropOldDb) {
            $this->dbforge->drop_database($dbName);
        }
        $this->dbforge->create_database($dbName);

        $this->db = $this->load->database('default', true);
    }

    /**
     * Run migration to latest version.
     */
    public function index()
    {
        $this->load->library('migration');
        if ($this->migration->latest() === FALSE) {
            show_error($this->migration->error_string());
        } else {
            echo 'Migration complete.' . PHP_EOL;
        }
    }

    /**
     * Run migration to specific version.
     * @param $target_version
     */
    public function to($target_version = null)
    {
        $this->load->library('migration');
        if (is_null($target_version)) {
            echo 'Missing argument version migration.' . PHP_EOL;
        } else {
            if ($this->migration->version($target_version) === FALSE) {
                show_error($this->migration->error_string());
            } else {
                echo 'Migration to version ' . $target_version . ' complete.' . PHP_EOL;
            }
        }
    }

    /**
     * Rollback migration version.
     */
    public function rollback()
    {
        $this->load->library('migration');
        if ($this->migration->version(0) === FALSE) {
            show_error($this->migration->error_string());
        } else {
            echo 'Rollback database complete.' . PHP_EOL;
        }
    }

    /**
     * Rollback and migrate database.
     */
    public function reset()
    {
        $this->rollback();
        $this->index();
    }

    /**
     * Recreating database and migrate.
     */
    public function fresh()
    {
        $this->init();
        $this->index();
    }
}
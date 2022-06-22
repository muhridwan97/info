<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SettingModel extends App_Model
{
    protected $table = 'settings';
    protected $id = 'key';

    /**
     * SettingModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Retrieve all configuration keys.
     *
     * @return array
     */
    public function getAllSettings()
    {
        $settings = $this->db->get($this->table)->result_array();
        $dataSettings = [];
        foreach ($settings as $data) {
            $dataSettings[$data['key']] = $data['value'];
        }
        return $dataSettings;
    }

    /**
     * Get single setting key.
     *
     * @param string $key
     * @param string $default
     * @return string
     */
    public function getSetting($key, $default = '')
    {
        $settings = $this->getAllSettings();
        if (key_exists($key, $settings)) {
            return $settings[$key];
        }
        return $default;
    }

}

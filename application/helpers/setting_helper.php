<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('get_setting')) {
    /**
     * Helper get single value of settings.
     * @param $key
     * @param $default
     * @return array
     */
    function get_setting($key, $default = '')
    {
        $CI = get_instance();
        $CI->load->model("SettingModel", "setting");
        return $CI->setting->getSetting($key, $default);
    }
}

if (!function_exists('get_settings')) {
    /**
     * Get all system preferences.
     * @return mixed
     */
    function get_settings()
    {
        $CI = get_instance();
        $CI->load->model("SettingModel", "setting");
        return $CI->setting->getAllSettings();
    }
}

if (!function_exists('get_asset')) {
    /**
     * Get manifest data.
     * @param string $key
     * @return mixed
     */
    function get_asset($key = null)
    {
        $rawManifest = file_get_contents('./assets/dist/manifest.json');
        $manifest = json_decode($rawManifest, true);

        if (!is_null($key)) {
            if (key_exists($key, $manifest)) {
                return $manifest[$key];
            }
            return null;
        }
        return $manifest;
    }
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Setting
 * @property SettingModel $setting
 */
class Setting extends App_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('SettingModel', 'setting');
    }

    /**
     * Index Page for this controller.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SETTING_EDIT);

        if (_is_method('put')) {
            if ($this->validate()) {
                $settings = $this->input->post();

                $this->db->trans_start();

                if (key_exists('_method', $settings)) {
                    unset($settings['_method']);
                }

                foreach ($settings as $key => $value) {
                    $this->setting->update(['value' => $value], $key);
                }

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    flash('success', 'Settings successfully updated', 'setting');
                } else {
                    flash('danger', 'Update setting failed, try again or contact administrator');
                }
            }
        }

        $setting = $this->setting->getAllSettings();

        $this->render('setting/index', compact('setting'));
    }

    /**
     * General validation rule.
     *
     * @return array
     */
    protected function _validation_rules()
    {
        return [
            'app_name' => 'trim|required|max_length[20]',
            'meta_url' => 'trim|required|valid_url|max_length[200]',
            'meta_keywords' => 'trim|required|max_length[300]|regex_match[/^[a-zA-Z0-9\-, ]+$/]',
            'meta_description' => 'trim|required|max_length[300]',
            'meta_author' => 'trim|required|max_length[50]',
            'email_bug_report' => 'trim|required|valid_email|max_length[50]',
            'email_support' => 'trim|required|max_length[100]',
            'company_name' => 'trim|required|max_length[50]',
            'company_address' => 'trim|required|max_length[200]',
            'company_contact' => 'trim|required|max_length[100]',
        ];
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mailer extends CI_Model
{
    public function __construct()
    {
        $this->load->library('email');
    }

    /**
     * Simple send template email.
     *
     * @param $to
     * @param $title
     * @param $template
     * @param $data
     * @param null $options
     * @return bool
     */
    public function send($to, $title, $template, $data, $options = null)
    {
        $this->email->from($this->config->item('from_address'), $this->config->item('from_name'));
        $this->email->to($to);
        $this->email->reply_to(get_if_exist($options, 'reply_to', get_setting('email_support', $this->config->item('admin_email'))));
        $this->email->subject(get_setting('app_name', $this->config->item('app_name')) . ' - ' . $title);
        $this->email->message($this->load->view($template, $data, true));

        if (!empty($options)) {
            if (key_exists('cc', $options) && !empty($options['cc'])) {
                $this->email->cc($options['cc']);
            }

            if (key_exists('bcc', $options) && !empty($options['bcc'])) {
                $this->email->bcc($options['bcc']);
            }

            if (key_exists('attachment', $options) && !empty($options['attachment'])) {
                if (!is_array($options['attachment'])) {
                    $options['attachment'] = [
                        ['source' => $options['attachment']]
                    ];
                } else {
                    if (!key_exists(0, $options['attachment'])) {
                        $options['attachment'] = [$options['attachment']];
                    }
                }
                foreach ($options['attachment'] as $attachment) {
                    $source = get_if_exist($attachment, 'source', $attachment);
                    $disposition = get_if_exist($attachment, 'disposition', 'attachment');
                    $fileName = get_if_exist($attachment, 'file_name', null);
                    $mime = get_if_exist($attachment, 'mime', '');
                    $this->email->attach($source, $disposition, $fileName, $mime);
                }
            }
        }

        return $this->email->send();
    }
}
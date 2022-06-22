<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['protocol'] = env('MAIL_DRIVER');
$config['useragent'] = 'Mail Agent';
$config['charset'] = 'iso-8859-1';
$config['wordwrap'] = TRUE;
$config['smtp_host'] = env('MAIL_HOST');
$config['smtp_port'] = env('MAIL_PORT');
$config['smtp_timeout'] = '7';
$config['smtp_user'] = env('MAIL_USERNAME');
$config['smtp_pass'] = env('MAIL_PASSWORD');
$config['smtp_crypto'] = env('MAIL_ENCRYPTION');
$config['mailtype'] = 'html';
$config['validation'] = TRUE;
$config['newline'] = "\r\n";
$config['crlf'] = "\r\n";
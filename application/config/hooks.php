<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/
$hook['post_controller_constructor'] = [
    [
        'class' => 'RedirectIfAuthenticated',
        'function' => 'isGuest',
        'filename' => 'middleware/RedirectIfAuthenticated.php',
        'filepath' => 'hooks',
        'params' => 'dashboard'
    ],
    [
        'class' => 'MustAuthenticated',
        'function' => 'checkAuth',
        'filename' => 'middleware/MustAuthenticated.php',
        'filepath' => 'hooks',
    ],
    [
        'class' => 'RequestFilter',
        'function' => 'filterRequestMethod',
        'filename' => 'middleware/RequestFilter.php',
        'filepath' => 'hooks',
    ],
    [
        'class' => 'Logging',
        'function' => 'logAccess',
        'filename' => 'middleware/Logging.php',
        'filepath' => 'hooks',
    ],
];
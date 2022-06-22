<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="<?= $this->security->get_csrf_hash() ?>">
    <meta name="base-url" content="<?= site_url() ?>">
    <meta name="user-id" content="">
    <meta name="theme-color" content="#5983e8">
    <title><?= $this->config->item('app_name') ?> | <?= isset($title) ? $title : 'Home' ?></title>
    <link rel="stylesheet" href="<?= base_url(get_asset('vendors.css')) ?>">
    <link rel="stylesheet" href="<?= base_url(get_asset('app.css')) ?>">
    <link rel="icon" href="<?= base_url('assets/dist/img/layouts/icon.png') ?>" type="image/x-icon">
</head>
<body>
<div>
    <div class="container-fluid">
        <div class="content-wrapper d-flex align-items-center">
            <div class="row w-100">
                <div class="col-lg-4 mx-auto">
                    <?php $this->load->view($page, $data) ?>
                </div>
            </div>
        </div>
    </div>

    <p class="text-center pt-3">
        Copyright &copy <?= date('Y') ?> <?= $this->config->item('app_author') ?>. All rights reserved.
    </p>
</div>

<script src="<?= base_url(get_asset('runtime.js')) ?>"></script>
<script src="<?= base_url(get_asset('vendors.js')) ?>"></script>
<script src="<?= base_url(get_asset('app.js')) ?>"></script>
</body>

</html>

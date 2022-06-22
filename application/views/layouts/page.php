<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="<?= $this->security->get_csrf_hash() ?>">
    <meta name="base-url" content="<?= site_url() ?>">
	<meta name="user-id" content="">
    <title><?= $this->config->item('app_name') ?> | <?= isset($title) ? $title : 'Home' ?></title>
    <link rel="stylesheet" href="<?= base_url(get_asset('vendors.css')) ?>">
	<link rel="stylesheet" href="<?= base_url(get_asset('app.css')) ?>">
    <link rel="icon" href="<?= base_url('assets/dist/img/icon.png') ?>" type="image/x-icon">
    <script src="https://js.pusher.com/4.3/pusher.min.js"></script>
</head>
<body>
<div class="container mt-4" style="min-height: calc(100vh - 120px)">
    <?php $this->load->view($page, $data) ?>
</div>
<footer class="container py-3">
	<div class="py-3 d-flex justify-content-between">
		<p class="mb-0">
			&copy; <?= date('Y') ?> <strong><?= $this->config->item('app_author') ?></strong> all rights reserved.
		</p>
		<ul class="list-inline mb-0">
			<li class="list-inline-item"><a href="<?= site_url('/') ?>">Home</a></li>
			<li class="list-inline-item"><a href="<?= site_url('legal') ?>">Legal</a></li>
			<li class="list-inline-item"><a href="<?= site_url('cookie') ?>">Cookie</a></li>
			<li class="list-inline-item"><a href="<?= site_url('privacy') ?>">Privacy</a></li>
			<li class="list-inline-item"><a href="<?= site_url('agreement') ?>">Agreement</a></li>
			<li class="list-inline-item"><a href="<?= site_url('sla') ?>">SLA</a></li>
		</ul>
	</div>
</footer>

<script src="<?= base_url(get_asset('runtime.js')) ?>"></script>
<script src="<?= base_url(get_asset('vendors.js')) ?>"></script>
<script src="<?= base_url(get_asset('app.js')) ?>"></script>
</body>
</html>

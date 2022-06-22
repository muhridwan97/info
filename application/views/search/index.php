<div class="card mb-3">
    <div class="card-body">
        <div class="d-sm-flex align-items-center justify-content-between">
            <h5 class="card-title">Scan Data</h5>
        </div>
		<form action="<?= site_url('search') ?>" method="get">
			<div class="form-group mb-2">
				<div class="input-group search-input">
					<input type="text" class="form-control" maxlength="50" name="q" id="q" aria-label="Search"
						   autofocus placeholder="Search data..." value="<?= get_url_param('q') ?>" required>
					<div class="input-group-append">
						<button type="submit" class="btn btn-primary">Search</button>
					</div>
				</div>
			</div>
		</form>
    </div>
</div>

<?php if(!empty(get_url_param('q'))): ?>
	<?php
	$blogStatuses = [
		BlogModel::STATUS_ACTIVE => 'success',
		BlogModel::STATUS_REJECTED => 'danger',
		BlogModel::STATUS_PENDING => 'secondary',
	];
	?>
	<div class="d-sm-flex align-items-center justify-content-between mb-3">
		<h5 class="card-title mb-sm-0">Result of Blog</h5>
	</div>
	<div class="form-row mb-4">
		<?php foreach ($blogs as $index => $blog): ?>
			<div class="col-sm-6 mb-3">
				<div class="card d-flex flex-row overflow-hidden">
					<div class="d-flex flex-fill">
						<div class="card-body d-flex justify-content-between">
							<div>
								<a href="<?= site_url('landing/blog-view/' . $blog['id']) ?>" class="stretched-link">
									<h5 class="font-weight-bold mb-1"><?= $blog['title'] ?></h5>
								</a>
								<p class="mb-0 text-muted">
              						<?= substr(strip_tags($blog['body']), 0, 110) . "..." ?>
								</p>
								<span class="text-fade small">
									<?= $blog['category'] ?> Category - Last updated at <?= format_date(if_empty($blog['updated_at'], $blog['created_at']), 'd M Y H:i') ?>
								</span>
							</div>
							<div>
								<span class="badge badge-<?= get_if_exist($blogStatuses, $blog['status'], 'primary') ?>">
									<?= $blog['status'] ?>
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
		<?php if(empty($blogs)): ?>
			<div class="col text-muted">
				No any blogs found
			</div>
		<?php endif; ?>
	</div>


	<?php
	// $pageStatuses = [
	// 	PageModel::STATUS_VALIDATE => 'success',
	// 	PageModel::STATUS_REJECTED => 'danger',
	// 	PageModel::STATUS_PENDING => 'secondary',
	// ];
	?>
	<div class="d-sm-flex align-items-center justify-content-between mb-3">
		<h5 class="card-title mb-sm-0">Result of Pages</h5>
	</div>
	<div class="form-row mb-4">
		<?php foreach ($pages as $page): ?>
			<div class="col-6 col-md-3 mb-3">
				<div class="card h-100">
					<div class="card-body">
						<div class="d-flex flex-fill flex-column">
							<a href="<?= site_url('landing/page/' . $page['id']) ?>">
								<h6 class="font-weight-bold mb-1"><?= $page['page_name'] ?></h6>
							</a>
							<p class="mb-0 text-muted">
              						<?= substr(strip_tags($page['content']), 0, 110) . "..." ?>
							</p>
						</div>
					</div>
					<div class="card-footer bg-white">
						<div class="d-flex justify-content-between align-items-center">
							<span class="text-fade small">
								<?= $page['page_name'] ?>
							</span>
							<!-- <span class="badge badge-<?= get_if_exist($pageStatuses, $page['status'], 'primary') ?>">
								<?= $page['status'] ?>
							</span> -->
						</div>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
		<?php if(empty($pages)): ?>
			<div class="col text-muted">
				No any pages found
			</div>
		<?php endif; ?>
	</div>

<?php endif; ?>

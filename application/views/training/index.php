<div class="card mb-3">
    <div class="card-body">
        <div class="d-sm-flex justify-content-between">
            <h5 class="card-title mb-sm-0">Data Training</h5>
            <div>
				<div class="btn-group">
					<?php if(!empty($_GET)): ?>
						<a href="<?= site_url(uri_string()) ?>" class="btn btn-danger btn-sm px-2" data-toggle="tooltip" data-title="Reset Filter">
							<i class="mdi mdi-close"></i>
						</a>
					<?php endif; ?>
					<a href="#modal-filter" data-toggle="modal" class="btn <?= !empty($_GET) ? 'btn-danger active' : 'btn-info' ?> btn-sm pr-2 pl-2">
						<i class="mdi <?= !empty($_GET) ? '' : 'mdi-filter-variant' ?>"></i><?= !empty($_GET) ? ' Filtered' : '' ?>
					</a>
				</div>
                <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=true" class="btn btn-info btn-sm pr-2 pl-2">
                    <i class="mdi mdi-file-download-outline"></i>
                </a>
				<?php if(AuthorizationModel::isAuthorized(PERMISSION_TRAINING_CREATE)): ?>
					<a href="<?= site_url('training/class/create') ?>" class="btn btn-sm btn-primary">
						<i class="mdi mdi-plus-box-outline mr-2"></i>CREATE
					</a>
				<?php endif; ?>
            </div>
        </div>
		<table class="table table-hover table-sm mt-3 responsive">
			<thead>
			<tr class="toggle-row" data-button="toggle-course">
				<th class="text-md-center" style="width: 60px">
					No
				</th>
				<th>Curriculum</th>
				<th>Employee</th>
				<th>Created At</th>
				<th style="width: 90px">Status</th>
				<th style="width: 100px" class="text-md-right">Action</th>
			</tr>
			</thead>
			<tbody>
			<?php
				$trainingStatuses = [
					TrainingModel::STATUS_ACTIVE => 'success',
					TrainingModel::STATUS_INACTIVE => 'danger',
				];
			?>
			<?php $no = isset($trainings) ? ($trainings['current_page'] - 1) * $trainings['per_page'] : 0 ?>
			<?php foreach ($trainings['data'] as $training): ?>
				<tr>
					<td class="text-md-center"><?= ++$no ?></td>
					<td>
						<div class="d-flex align-items-center">
							<img src="<?= empty($training['cover_image']) ? base_url('assets/dist/img/no-image.png') : asset_url($training['cover_image']) ?>"
								 alt="Image cover" id="image-cover" class="rounded mr-3" style="height: 45px; width: 50px; object-fit: cover">
							<div>
								<h6 class="font-weight-bold mb-0"><?= $training['curriculum_title'] ?></h6>
								<p class="text-muted mb-0"><?= if_empty($training['department'], 'No') ?> department</p>
							</div>
						</div>
					</td>
					<td><?= $training['employee_name'] ?></td>
					<td><?= format_date($training['created_at'], 'd M Y H:i') ?></td>
					<td>
						<span class="badge badge-<?= get_if_exist($trainingStatuses, $training['status'], 'primary') ?>">
							<?= $training['status'] ?>
						</span>
					</td>
					<td class="text-md-right">
						<div class="dropdown">
							<button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="actionButton" data-toggle="dropdown">
								Action
							</button>
							<div class="dropdown-menu dropdown-menu-right" aria-labelledby="actionButton">
								<?php if(AuthorizationModel::isAuthorized(PERMISSION_TRAINING_VIEW)): ?>
									<a class="dropdown-item"
									   href="<?= site_url('training/class/view/' . $training['id']) ?>">
										<i class="mdi mdi-eye-outline mr-2"></i> View
									</a>
								<?php endif; ?>
								<?php if ($training['status'] == TrainingModel::STATUS_ACTIVE): ?>
									<?php if(AuthorizationModel::isAuthorized(PERMISSION_TRAINING_VIEW)): ?>
										<a class="dropdown-item"
										   href="<?= site_url("training/classroom/{$training['id']}") ?>">
											<i class="mdi mdi-play-outline mr-2"></i> Start Learning
										</a>
									<?php endif; ?>
									<?php if(AuthorizationModel::isAuthorized(PERMISSION_TRAINING_EDIT)): ?>
										<div class="dropdown-divider"></div>
										<a class="dropdown-item btn-validate" data-action="inactive" data-label="Training <?= $training['curriculum_title'] ?>"
										   href="<?= site_url('training/class/inactive/' . $training['id']) ?>">
											<i class="mdi mdi-close mr-2"></i> Set Inactive
										</a>
									<?php endif; ?>
								<?php endif; ?>
								<?php if(AuthorizationModel::isAuthorized(PERMISSION_TRAINING_DELETE)): ?>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item btn-delete" href="#modal-delete" data-toggle="modal"
									   data-id="<?= $training['id'] ?>" data-label="Training <?= $training['curriculum_title'] ?> - <?= $training['employee_name'] ?>" data-title="Training"
									   data-url="<?= site_url('training/class/delete/' . $training['id']) ?>">
										<i class="mdi mdi-trash-can-outline mr-2"></i> Delete
									</a>
								<?php endif; ?>
							</div>
						</div>
					</td>
				</tr>
			<?php endforeach; ?>
			<?php if(empty($trainings['data'])): ?>
				<tr>
					<td colspan="6">No training data available</td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>
        <?php $this->load->view('partials/_pagination', ['pagination' => $trainings]) ?>
    </div>
</div>

<?php $this->load->view('training/_modal_filter') ?>
<?php $this->load->view('partials/modals/_validate') ?>

<?php if(AuthorizationModel::isAuthorized(PERMISSION_TRAINING_DELETE)): ?>
    <?php $this->load->view('partials/modals/_delete') ?>
<?php endif; ?>

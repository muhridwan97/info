<form class="form-plaintext">
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">View Training</h5>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group row">
						<label class="col-sm-4 col-form-label">Curriculum</label>
						<div class="col-sm-8">
							<p class="form-control-plaintext">
								<?= $training['curriculum_title'] ?>
							</p>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-4 col-form-label">Department</label>
						<div class="col-sm-8">
							<p class="form-control-plaintext">
								<?= if_empty($training['department'], 'No department') ?>
							</p>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-4 col-form-label">Employee</label>
						<div class="col-sm-8">
							<p class="form-control-plaintext">
								<?= $training['employee_name'] ?>
							</p>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-4 col-form-label">Status</label>
						<div class="col-sm-8">
							<?php
							$trainingStatuses = [
								TrainingModel::STATUS_ACTIVE => 'success',
								TrainingModel::STATUS_INACTIVE => 'danger',
							];
							?>
							<p class="form-control-plaintext">
								<span class="badge badge-<?= get_if_exist($trainingStatuses, $training['status'], 'primary') ?>">
									<?= $training['status'] ?>
								</span>
							</p>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group row">
						<label class="col-sm-4 col-form-label">Description</label>
						<div class="col-sm-8">
							<p class="form-control-plaintext">
								<?= if_empty($training['description'], 'No description') ?>
							</p>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-4 col-form-label">Assigned By</label>
						<div class="col-sm-8">
							<p class="form-control-plaintext">
								<?= if_empty($training['assigned_by'], 'Default') ?>
							</p>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-4 col-form-label">Created At</label>
						<div class="col-sm-8">
							<p class="form-control-plaintext">
								<?= format_date($training['created_at'], 'd F Y H:i') ?>
							</p>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-4 col-form-label">Updated At</label>
						<div class="col-sm-8">
							<p class="form-control-plaintext">
								<?= if_empty(format_date($training['updated_at'], 'd F Y H:i'), '-') ?>
							</p>
						</div>
					</div>
				</div>
			</div>
        </div>
    </div>

	<div class="card mb-3">
		<div class="card-body">
			<h5 class="card-title mb-3">Status Histories</h5>
			<table class="table table-sm responsive">
				<thead>
				<tr>
					<th>No</th>
					<th>Status</th>
					<th style="max-width: 300px">Description</th>
					<th>Data</th>
					<th>Created At</th>
					<th>Created By</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($statusHistories as $index => $statusHistory): ?>
					<tr>
						<td><?= ($index + 1) ?></td>
						<td>
							<span class="badge badge-<?= get_if_exist($trainingStatuses, $statusHistory['status'], 'light') ?>">
								<?= $statusHistory['status'] ?>
							</span>
						</td>
						<td style="max-width: 300px"><?= if_empty($statusHistory['description'], 'No description') ?></td>
						<td>
							<?php if(empty($statusHistory['data'])): ?>
								-
							<?php else: ?>
								<a href="<?= site_url('history/view/' . $statusHistory['id']) ?>">
									View History
								</a>
							<?php endif; ?>
						</td>
						<td><?= if_empty(format_date($statusHistory['created_at'], 'd F Y H:i'), '-') ?></td>
						<td>
							<a href="<?= site_url('master/user/view/' . $statusHistory['created_by']) ?>">
								<?= if_empty($statusHistory['creator_name'], 'No user') ?>
							</a>
						</td>
					</tr>
				<?php endforeach; ?>
				<?php if(empty($statusHistories)): ?>
					<tr class="row-no-header">
						<td colspan="6">No status history available</td>
					</tr>
				<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>

	<?php if($training['status'] == TrainingModel::STATUS_ACTIVE): ?>
		<div class="d-flex justify-content-between align-items-center mb-3">
			<h5 class="card-title">Courses</h5>
			<a href="<?= site_url("training/classroom/{$training['id']}") ?>" class="btn btn-success">
				Start Learning
			</a>
		</div>
		<?php foreach ($courses as $course): ?>
			<div class="card d-flex flex-row overflow-hidden" style="z-index: 2; border-bottom-right-radius: 0">
				<img src="<?= empty($course['cover_image']) ? base_url('assets/dist/img/no-image.png') : asset_url($course['cover_image']) ?>"
					 alt="Image cover" id="image-cover" style="width: 120px; height: 110px; object-fit: cover">
				<div class="d-flex flex-fill">
					<div class="card-body d-flex justify-content-between">
						<div class="align-self-center">
							<h5 class="font-weight-bold mb-1">
								<a href="<?= site_url("training/classroom/{$training['id']}/course/{$course['id']}") ?>">
									<?= $course['course_title'] ?>
								</a>
							</h5>
							<p class="mb-0 text-muted">
								<?= word_limiter(if_empty($course['description'], 'No description'), 20) ?>
							</p>
							<p class="text-fade small mb-0">
								<?= $course['total_lessons'] ?> Lessons - Last updated at <?= format_date(if_empty($course['updated_at'], $course['created_at']), 'd M Y H:i') ?>
							</p>
						</div>
						<div class="d-none d-sm-flex align-items-start">
							<a href="<?= site_url("training/classroom/{$training['id']}/course/{$course['id']}") ?>" class="btn btn-sm btn-primary">
								Start
							</a>
						</div>
					</div>
				</div>
			</div>
			<?php if (!empty($course['lessons'])): ?>
				<div class="list-group list-group-flush ml-3 mb-4">
					<div class="list-group-item font-weight-bold">
						Lessons
					</div>
					<?php foreach ($course['lessons'] as $lessonIndex => $lesson): ?>
						<a href="<?= site_url("training/classroom/{$training['id']}/course/{$course['id']}?lesson=" . $lesson['id']) ?>" class="list-group-item list-group-item-action text-muted d-flex justify-content-between">
							<?= $lessonIndex + 1 ?>. <?= $lesson['lesson_title'] ?>
							<i class="<?= get_file_icon($lesson['source']) ?>"></i>
						</a>
						<?php foreach ($lesson['exercises'] as $exercise): ?>
							<a href="<?= site_url("training/classroom/{$training['id']}/course/{$course['id']}?exercise={$exercise['id']}") ?>" class="list-group-item list-group-item-action text-fade d-flex justify-content-between" style="font-size: 0.9rem; padding-left: 2.3rem">
								<p class="mb-0">
									<i class="mdi mdi-ballot-outline mr-2"></i>
									<?= ucfirst(strtolower($exercise['category'])) ?> Questions (<?= $exercise['total_questions'] ?>) - <?= $exercise['exercise_title'] ?>
								</p>
								<i class="mdi mdi-arrow-right"></i>
							</a>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>

		<?php if(empty($courses)): ?>
			<div class="card mb-3">
				<div class="card-body">
					<p class="text-muted mb-0">No courses available</p>
				</div>
			</div>
		<?php endif; ?>
	<?php else: ?>
		<div class="alert alert-warning">
			<h6 class="font-weight-bold mb-1">Training is deactivated</h6>
			<p class="mb-0">
				The training session of curriculum <?= $training['curriculum_title'] ?> currently <strong>INACTIVE</strong>.
			</p>
		</div>
	<?php endif; ?>

    <div class="card mb-3">
        <div class="card-body d-flex justify-content-between">
            <button onclick="history.back()" type="button" class="btn btn-light">Back</button>
        </div>
    </div>
</form>

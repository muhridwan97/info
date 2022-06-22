<h4 class="mb-2">Dashboard</h4>
<p class="text-fade mb-4">
	FisApp is a program from physics education in managing student letters and theses.
</p>

<?php if(AuthorizationModel::hasPermission(PERMISSION_LECTURER_CREATE)): ?>
	<div class="row">
		<div class="col-6 col-xl-3">
			<div class="card border-0 shadow-sm mb-3">
				<div class="card-body d-flex justify-content-between align-items-center">
					<div>
						<h3 class="mb-0"><?= numerical($totalLecturer) ?></h3>
						<p class="text-fade mb-0">Total Lecturer</p>
					</div>
					<h1 class="mdi mdi-folder-settings-outline display-4 text-warning" style="line-height: 1; flex-basis: 70px"></h1>
				</div>
				<a href="<?= site_url('master/lecturer') ?>" class="card-footer btn-link d-flex justify-content-between align-items-center bg-white py-2">
					<span class="small">Show Detail</span>
					<i class="mdi mdi-arrow-right"></i>
				</a>
			</div>
		</div>
		<div class="col-6 col-xl-3">
			<div class="card border-0 shadow-sm mb-3">
				<div class="card-body d-flex justify-content-between align-items-center">
					<div>
						<h3 class="mb-0"><?= numerical($totalStudent) ?></h3>
						<p class="text-fade mb-0">Total Student</p>
					</div>
					<h1 class="mdi mdi-form-select display-4 text-primary" style="line-height: 1; flex-basis: 70px"></h1>
				</div>
				<a href="<?= site_url('master/student') ?>" class="card-footer btn-link d-flex justify-content-between align-items-center bg-white py-2">
					<span class="small">Show Detail</span>
					<i class="mdi mdi-arrow-right"></i>
				</a>
			</div>
		</div>
		<div class="col-6 col-xl-3">
			<div class="card border-0 shadow-sm mb-3">
				<div class="card-body d-flex justify-content-between align-items-center">
					<div>
						<h3 class="mb-0"><?= numerical($totalBlog) ?></h3>
						<p class="text-fade mb-0">Total Post</p>
					</div>
					<h1 class="mdi mdi-book-check-outline display-4 text-info" style="line-height: 1; flex-basis: 70px"></h1>
				</div>
				<a href="<?= site_url('blog') ?>" class="card-footer btn-link d-flex justify-content-between align-items-center bg-white py-2">
					<span class="small">Show Detail</span>
					<i class="mdi mdi-arrow-right"></i>
				</a>
			</div>
		</div>
		<div class="col-6 col-xl-3">
			<div class="card border-0 shadow-sm mb-3">
				<div class="card-body d-flex justify-content-between align-items-center">
					<div>
						<h3 class="mb-0"><?= numerical($totalAgenda) ?></h3>
						<p class="text-fade mb-0">Total Agenda</p>
					</div>
					<h1 class="mdi mdi-ballot-outline display-4 text-success" style="line-height: 1; flex-basis: 70px"></h1>
				</div>
				<a href="<?= site_url('agenda') ?>" class="card-footer btn-link d-flex justify-content-between align-items-center bg-white py-2">
					<span class="small">Show Detail</span>
					<i class="mdi mdi-arrow-right"></i>
				</a>
			</div>
		</div>
	</div>
<?php endif; ?>

<?php if(!empty($activeTrainings)): ?>
	<p class="card-title">Active Trainings</p>
	<div class="mb-3">
		<?php foreach ($activeTrainings as $index => $training): ?>
			<div class="card d-flex flex-row mb-2">
				<img src="<?= empty($training['cover_image']) ? base_url('assets/dist/img/no-image.png') : asset_url($training['cover_image']) ?>"
					 alt="Image cover" id="image-cover" style="width: 100px; height: 85px; object-fit: cover">
				<div class="d-flex flex-fill">
					<div class="card-body py-2 px-3 d-flex justify-content-between align-items-center">
						<div>
							<a href="<?= site_url('training/class/view/' . $training['id']) ?>">
								<h5 class="font-weight-bold mb-1"><?= $training['lecturer_title'] ?></h5>
							</a>
							<p class="mb-0 text-muted">
								<?= word_limiter(if_empty($training['description'], 'No description'), 20) ?>
							</p>
							<span class="text-fade small">
							<?= $training['total_students'] ?> Students - Last updated at <?= format_date(if_empty($training['updated_at'], $training['created_at']), 'd M Y H:i') ?>
						</span>
						</div>
						<div>
							<a href="<?= site_url('training/classroom/' . $training['id']) ?>" class="btn btn-sm btn-success">
								<span class="d-none d-sm-inline-block">Continue</span><i class="mdi mdi-arrow-right"></i>
							</a>
						</div>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>


<?php if(false): ?>
<div class="card mb-3">
	<div class="card-body">
		<div class="d-flex justify-content-between">
			<p class="card-title text-info">Latest Training Exams</p>
			<a href="<?= site_url('training/exam') ?>" class="text-base">
				Show Exams <i class="mdi mdi-arrow-right"></i>
			</a>
		</div>
		<table class="table table-md responsive mb-0">
			<thead>
			<tr>
				<th>Exam Title</th>
				<th>Employee</th>
				<th>Category</th>
				<th>Question</th>
				<th>Score</th>
				<th class="text-md-right">Status</th>
			</tr>
			</thead>
			<tbody>
			<?php
			$examStatuses = [
				ExamExerciseModel::STATUS_PENDING => 'primary',
				ExamExerciseModel::STATUS_STARTED => 'warning',
				ExamExerciseModel::STATUS_FINISHED => 'info',
				ExamExerciseModel::STATUS_ASSESSED => 'success',
			];
			?>
			<?php foreach ($latestExams as $index => $exam): ?>
				<tr>
					<td>
						<div class="d-flex align-items-center">
							<img src="<?= empty($exam['cover_image']) ? base_url('assets/dist/img/no-image.png') : asset_url($exam['cover_image']) ?>"
								 alt="Image cover" id="image-cover" class="rounded-sm mr-3" style="height: 25px; width: 30px; object-fit: cover">
							<a href="<?= site_url('training/exam-exercise/view/' . $exam['id']) ?>">
								<?= $exam['title'] ?>
							</a>
						</div>
					</td>
					<td><?= $exam['employee_name'] ?></td>
					<td><?= $exam['category'] ?></td>
					<td><?= $exam['total_questions'] ?> items</td>
					<td>
						<h5 class="mb-0 font-weight-bold text-<?= $exam['score'] > 50 ? 'success' : 'danger' ?>">
							<?= numerical($exam['score'], 1) ?>
						</h5>
					</td>
					<td class="text-md-right">
						<span class="badge badge-<?= get_if_exist($examStatuses, $exam['status'], 'light') ?>">
							<?= $exam['status'] ?>
						</span>
					</td>
				</tr>
			<?php endforeach; ?>
			<?php if(empty($latestExams)): ?>
				<tr>
					<td colspan="6">No latest exams available</td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>
<?php endif; ?>

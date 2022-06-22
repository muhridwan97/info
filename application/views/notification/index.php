<div class="card mb-2">
    <div class="card-body">
        <div class="d-flex justify-content-between">
            <h5 class="card-title mb-0">Notification</h5>
            <a style="font-size: 0.9rem" href="<?= site_url('notification/read-all') ?>">
                Mark All as Read
            </a>
        </div>
    </div>
</div>
<?php
$notificationIcons = [
	NotificationModel::EVENT_CURRICULUM_MUTATION => 'mdi-folder-settings-outline',
	NotificationModel::EVENT_COURSE_MUTATION => 'mdi-form-select',
	NotificationModel::EVENT_LESSON_MUTATION => 'mdi-book-check-outline',
	NotificationModel::EVENT_TRAINING_ASSIGNED => 'mdi-account-cog-outline',
	NotificationModel::EVENT_EXAM_ASSIGNED => 'mdi-ballot-outline',
	NotificationModel::EVENT_EXAM_FINISHED => 'mdi-checkbox-marked-outline',
];
$notificationColors = [
	NotificationModel::EVENT_CURRICULUM_MUTATION => 'warning',
	NotificationModel::EVENT_COURSE_MUTATION => 'success',
	NotificationModel::EVENT_LESSON_MUTATION => 'info',
	NotificationModel::EVENT_TRAINING_ASSIGNED => 'primary',
	NotificationModel::EVENT_EXAM_ASSIGNED => 'info',
	NotificationModel::EVENT_EXAM_FINISHED => 'success',
]
?>
<?php foreach ($notifications as $notification): ?>
	<div class="card mb-2">
		<div class="card-body">
			<div class="d-flex flex-row">
				<i class="mr-2 h5 text-<?= get_if_exist($notificationColors, $notification['event'], 'primary') ?> mdi <?= get_if_exist($notificationIcons, $notification['event'], 'mdi-file-outline') ?> mx-0"></i>
				<div class="d-sm-flex justify-content-sm-between flex-grow-1">
					<div>
						<a href="<?= site_url('notification/read/'.$notification['id'].'?redirect=') ?><?= get_if_exist($notification['data'], 'url', '#') ?>" class="mb-0 d-block <?= $notification['is_read'] ? 'text-muted' : 'text-success' ?>" style="font-size: 0.9rem">
							<?= $notification['data']['message'] ?>
						</a>
						<p class="text-muted small mb-0"><?= get_if_exist($notification['data'], 'description', '') ?></p>
					</div>
					<small class="text-muted text-right" style="min-width: 170px">
						<?= relative_time($notification['created_at']) ?>
					</small>
				</div>
			</div>
		</div>
	</div>
<?php endforeach; ?>
<?php if(empty($notifications)): ?>
	<div class="card mb-2">
		<div class="card-body">
			<p class="text-muted mb-0">No notification available.</p>
		</div>
	</div>
<?php endif; ?>

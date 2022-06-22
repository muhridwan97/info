
<?php $stickyNotifications = NotificationModel::getUnreadNotification() ?>
            <?php $stickyUnread = count($stickyNotifications) ?>
            <a class="nav-link count-indicator text-white" href="#" id="notification-dropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="mdi mdi-bell-outline lead"></i>
                    <?php if($stickyUnread > 0): ?>
                        <span class="count"><?= $stickyUnread > 9 ? '9+' : $stickyUnread ?></span>
                    <?php endif; ?>
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown-notification" aria-labelledby="notification-dropdown">
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
				<?php foreach ($stickyNotifications as $notify): ?>
					<a class="dropdown-item text-wrap d-flex flex-row mb-2" style="line-height: 1.2" href="<?= site_url('notification/read/' . $notify['id'] .'?redirect=' . $notify['data']['url']) ?>">
						<h3 class="mdi <?= get_if_exist($notificationIcons, $notify['event'], 'mdi-information-outline') ?> text-<?= get_if_exist($notificationColors, $notify['event'], 'body') ?>" style="flex: 0 0 40px"></h3>
						<div>
							<p class="mb-0"><?= $notify['data']['message'] ?></p>
							<small class="text-fade"><?= relative_time($notify['created_at']) ?></small>
						</div>
					</a>
				<?php endforeach; ?>
				<?php if(empty($stickyNotifications)): ?>
					<div class="dropdown-item">
						<span class="text-muted">No notification available</span>
					</div>
				<?php endif; ?>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item d-flex justify-content-between" href="<?= site_url('notification') ?>">
                    <span>Show all notifications </span>
                    <i class="mdi mdi-arrow-right"></i>
                </a>
            </div>
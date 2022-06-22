
<nav class="navbar navbar-expand navbar-dark bg-primary border-bottom">
    <button class="btn btn-lg p-0 btn-link text-white" id="menu-toggle">
        <span class="mdi mdi-menu lead"></span>
    </button>
    <img src="<?= base_url('assets/dist/img/icon.png') ?>" alt="Logo"
         class="d-sm-none ml-2 rounded" height="35px" style="opacity: 0.95">

    <ul class="navbar-nav align-items-center ml-auto mt-2 mt-lg-0">
        <li class="nav-item dropdown mr-1">
            <a class="nav-link text-white" href="<?= site_url('search') ?>">
                <i class="mdi mdi-magnify lead"></i>
            </a>
        </li>
        <li class="nav-item dropdown navbar-notification mr-2 ajax-notification">
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
				<?php $notifyCount = 1 ?>
				<?php foreach ($stickyNotifications as $notify): ?>
					<a class="dropdown-item text-wrap d-flex flex-row mb-2" style="line-height: 1.2" href="<?= site_url('notification/read/' . $notify['id'] .'?redirect=' . $notify['data']['url']) ?>">
						<h3 class="mdi <?= get_if_exist($notificationIcons, $notify['event'], 'mdi-information-outline') ?> text-<?= get_if_exist($notificationColors, $notify['event'], 'body') ?>" style="flex: 0 0 40px"></h3>
						<div>
							<p class="mb-0"><?= $notify['data']['message'] ?></p>
							<small class="text-fade"><?= relative_time($notify['created_at']) ?></small>
						</div>
					</a>
                    <?php if($notifyCount++ == 4) break; ?>
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
        </li>
        <li class="nav-item dropdown">
            <a id="navbarDropdown" class="nav-link dropdown-toggle text-white" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img src="<?= base_url(if_empty(UserModel::loginData('avatar'), 'assets/dist/img/no-avatar.png', 'uploads/')) ?>"
                     alt="Profile" class="img-fluid rounded-circle mr-2" style="width: 30px; height: 30px">
                <p class="d-none d-sm-inline-block mb-0">
                    <?= UserModel::loginData('name') ?>
                </p>
            </a>

            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="<?= site_url('dashboard') ?>">
                    <i class="mdi mdi-speedometer-slow mr-1"></i> Dashboard
                </a>
				<?php if($this->config->item('sso_enable')): ?>
					<a class="dropdown-item" href="<?= sso_url('app') ?>">
						<i class="mdi mdi-backburger mr-1"></i>
						Switch App
					</a>
					<a class="dropdown-item" href="<?= sso_url('account') ?>">
						<i class="mdi mdi-account-outline mr-1"></i> My Account
					</a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="<?= sso_url('auth/logout') ?>">
						<i class="mdi mdi-logout mr-1"></i> Logout
					</a>
				<?php else: ?>
					<a class="dropdown-item" href="<?= site_url('account') ?>">
						<i class="mdi mdi-account-outline mr-1"></i> My Account
					</a>
					<a class="dropdown-item" href="<?= site_url('auth/logout') ?>"
					   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
						<i class="mdi mdi-logout-variant mr-1"></i> Logout
					</a>
					<form id="logout-form" action="<?= site_url('auth/logout') ?>" method="POST" style="display: none;">
						<?= _csrf() ?>
					</form>
				<?php endif; ?>
            </div>
        </li>
    </ul>
</nav>

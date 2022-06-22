<?php
$segment1 = $this->uri->segment(1);
$segment2 = $this->uri->segment(2);
$segment3 = $this->uri->segment(3);
?>
<div class="bg-white d-flex flex-column" id="sidebar-wrapper">
	<div class="sidebar-heading bg-primary">
		<a href="<?= site_url('/') ?>" class="text-white btn-link">
			<?= get_setting('app_name', env('APP_NAME')) ?>
		</a>
	</div>
	<ul class="nav d-flex flex-column flex-fill pb-4">
		<li class="nav-item mt-1">
			<a href="<?= $this->config->item('sso_enable') ? sso_url('account') : site_url('account') ?>" class="nav-link" style="width: 240px">
				<div class="nav-profile-image" style="width: 50px">
					<?php if ($this->config->item('sso_enable')) : ?>
						<img src="<?= sso_url(if_empty(UserModel::loginData('avatar'), 'assets/dist/img/no-avatar.png', 'uploads/')) ?>" alt="avatar" class="flex-shrink-0 rounded-circle" style="height: 50px; width: 50px">
					<?php else : ?>
						<img src="<?= base_url(if_empty(UserModel::loginData('avatar'), 'assets/dist/img/no-avatar.png', 'uploads/')) ?>" alt="profile" class="flex-shrink-0 rounded-circle" style="height: 50px; width: 50px">
					<?php endif; ?>
				</div>
				<div class="d-flex flex-column text-truncate ml-2">
					<p class="font-weight-bold mb-0"><?= UserModel::loginData('name') ?></p>
					<small class="text-fade text-truncate"><?= UserModel::loginData('email') ?></small>
				</div>
			</a>
		</li>

		<li class="nav-title">MAIN MENU <i class="mdi mdi-arrow-right ml-auto"></i></li>
		<li class="nav-item<?= $segment1 == 'dashboard' ? ' active' : '' ?>">
			<a class="nav-link" href="<?= site_url('dashboard') ?>">
				<i class="mdi mdi-speedometer-slow menu-icon"></i>
				<span class="menu-title">Dashboard</span>
			</a>
		</li>

		<?php
		if (AuthorizationModel::hasPermission([
			PERMISSION_ROLE_VIEW, PERMISSION_USER_VIEW,
		])) :
		?>
			<li class="nav-item<?= $segment1 == 'master' && in_array($segment2, ['role', 'user']) ? ' active' : '' ?>">
				<a class="nav-link" data-toggle="collapse" href="#user-access" aria-expanded="<?= $segment1 == 'master' && in_array($segment2, ['role', 'user']) ? 'true' : 'false' ?>" aria-controls="master">
					<i class="mdi mdi-lock-outline menu-icon"></i>
					<span class="menu-title">User Access</span>
					<i class="menu-arrow"></i>
				</a>
				<div class="collapse<?= $segment1 == 'master' && in_array($segment2, ['role', 'user']) ? ' show' : '' ?>" id="user-access">
					<ul class="nav flex-column sub-menu">
						<?php if (AuthorizationModel::isAuthorized(PERMISSION_ROLE_VIEW)) : ?>
							<li class="nav-item<?= $segment1 == 'master' && $segment2 == 'role' ? ' active' : '' ?>">
								<a class="nav-link" href="<?= site_url('master/role') ?>">
									<i class="mdi mdi-shield-account-outline mr-2"></i>Role
								</a>
							</li>
						<?php endif; ?>
						<?php if (AuthorizationModel::isAuthorized(PERMISSION_USER_VIEW)) : ?>
							<li class="nav-item<?= $segment1 == 'master' && $segment2 == 'user' ? ' active' : '' ?>">
								<a class="nav-link" href="<?= site_url('master/user') ?>">
									<i class="mdi mdi-account-multiple-outline mr-2"></i>User
									<?php if ($this->config->item('sso_enable')) : ?>
										<span class="badge badge-info badge-pill ml-auto">
											SSO
										</span>
									<?php endif; ?>
								</a>
							</li>
						<?php endif; ?>
					</ul>
				</div>
			</li>
		<?php endif; ?>

		<?php
		if (AuthorizationModel::hasPermission([
			PERMISSION_DEPARTMENT_VIEW, PERMISSION_MENU_VIEW,
		])) :
		?>
			<li class="nav-item<?= $segment1 == 'master' && !in_array($segment2, ['role', 'user']) ? ' active' : '' ?>">
				<a class="nav-link" data-toggle="collapse" href="#master" aria-expanded="<?= $segment1 == 'master' && !in_array($segment2, ['role', 'user']) ? 'true' : 'false' ?>" aria-controls="master">
					<i class="mdi mdi-cube-outline menu-icon"></i>
					<span class="menu-title">Master</span>
					<i class="menu-arrow"></i>
				</a>
				<div class="collapse<?= $segment1 == 'master' && !in_array($segment2, ['role', 'user']) ? ' show' : '' ?>" id="master">
					<ul class="nav flex-column sub-menu">
						<?php if (AuthorizationModel::isAuthorized(PERMISSION_MENU_VIEW)) : ?>
							<li class="nav-item<?= $segment1 == 'master' && $segment2 == 'menu' ? ' active' : '' ?>">
								<a class="nav-link" href="<?= site_url('master/menu') ?>">
									<i class="mdi mdi-apps mr-2"></i>Menu
								</a>
							</li>
						<?php endif; ?>
						<?php if (AuthorizationModel::isAuthorized(PERMISSION_CATEGORY_VIEW)) : ?>
							<li class="nav-item<?= $segment1 == 'master' && $segment2 == 'category' ? ' active' : '' ?>">
								<a class="nav-link" href="<?= site_url('master/category') ?>">
									<i class="mdi mdi-tag-text-outline mr-2"></i>Category
								</a>
							</li>
						<?php endif; ?>
						<?php if (AuthorizationModel::isAuthorized(PERMISSION_STUDENT_VIEW)) : ?>
							<li class="nav-item<?= $segment1 == 'master' && $segment2 == 'student' ? ' active' : '' ?>">
								<a class="nav-link" href="<?= site_url('master/student') ?>">
									<i class="mdi mdi-office-building-outline mr-2"></i>Student
									<?php if ($this->config->item('sso_enable')) : ?>
										<span class="badge badge-info badge-pill ml-auto">
											LTR
										</span>
									<?php endif; ?>
								</a>
							</li>
						<?php endif; ?>
					</ul>
				</div>
			</li>
		<?php endif; ?>

		<?php if (AuthorizationModel::isAuthorized(PERMISSION_PAGE_VIEW)) : ?>
		<li class="nav-item<?= $segment1 == 'page' ? ' active' : '' ?>">
			<a class="nav-link" href="<?= base_url('/page') ?>">
				<i class="mdi mdi-mail menu-icon"></i>
				<span class="menu-title">Page</span>
			</a>
		</li>		
		<?php endif; ?>

		<?php if (AuthorizationModel::isAuthorized(PERMISSION_BANNER_VIEW)) : ?>
		<li class="nav-item<?= $segment1 == 'banner' ? ' active' : '' ?>">
			<a class="nav-link" href="<?= base_url('/banner') ?>">
				<i class="mdi mdi-satellite menu-icon"></i>
				<span class="menu-title">Banner</span>
			</a>
		</li>		
		<?php endif; ?>

		<?php if (AuthorizationModel::isAuthorized(PERMISSION_BLOG_VIEW)) : ?>
		<li class="nav-item<?= $segment1 == 'blog' ? ' active' : '' ?>">
			<a class="nav-link" href="<?= base_url('/blog') ?>">
				<i class="mdi mdi-blogger menu-icon"></i>
				<span class="menu-title">Blog</span>
			</a>
		</li>		
		<?php endif; ?>

		<?php if (AuthorizationModel::isAuthorized(PERMISSION_AGENDA_VIEW)) : ?>
		<li class="nav-item<?= $segment1 == 'agenda' ? ' active' : '' ?>">
			<a class="nav-link" href="<?= base_url('/agenda') ?>">
				<i class="mdi mdi-view-agenda menu-icon"></i>
				<span class="menu-title">Agenda</span>
			</a>
		</li>		
		<?php endif; ?>

		<?php if (AuthorizationModel::isAuthorized(PERMISSION_ACCOUNT_EDIT)) : ?>
			<li class="nav-title">ACCOUNT & SETTING <i class="mdi mdi-arrow-right ml-auto"></i></li>
			<li class="nav-item<?= $segment1 == 'account' ? ' active' : '' ?>">
				<a class="nav-link" href="<?= $this->config->item('sso_enable') ? sso_url('account') : site_url('account') ?>">
					<i class="mdi mdi-account-outline menu-icon"></i>
					<span class="menu-title">Account</span>
				</a>
			</li>
		<?php endif; ?>

		<?php if (AuthorizationModel::isAuthorized(PERMISSION_SETTING_EDIT)) : ?>
			<li class="nav-item<?= $segment1 == 'utility' ? ' active' : '' ?>">
				<a class="nav-link" data-toggle="collapse" href="#utility" aria-expanded="false" aria-controls="utility">
					<i class="mdi mdi-toolbox-outline menu-icon"></i>
					<span class="menu-title">Utility</span>
					<i class="menu-arrow"></i>
				</a>
				<div class="collapse<?= $segment1 == 'utility' ? ' show' : '' ?>" id="utility">
					<ul class="nav flex-column sub-menu">
						<li class="nav-item<?= $segment1 == 'utility' && $segment2 == 'backup' ? ' active' : '' ?>">
							<a class="nav-link" href="<?= site_url('utility/backup') ?>">Backup</a>
						</li>
						<li class="nav-item<?= $segment1 == 'utility' && $segment2 == 'system-log' ? ' active' : '' ?>">
							<a class="nav-link" href="<?= site_url('utility/system-log') ?>">System Log</a>
						</li>
						<li class="nav-item<?= $segment1 == 'utility' && $segment2 == 'access-log' ? ' active' : '' ?>">
							<a class="nav-link" href="<?= site_url('utility/access-log') ?>">Access Log</a>
						</li>
					</ul>
				</div>
			</li>
			<li class="nav-item<?= $segment1 == 'setting' ? ' active' : '' ?>">
				<a class="nav-link" href="<?= site_url('setting') ?>">
					<i class="mdi mdi-cog-outline menu-icon"></i>
					<span class="menu-title">Setting</span>
				</a>
			</li>
		<?php endif; ?>

		<li class="nav-item">
			<a class="nav-link" href="<?= site_url('help') ?>">
				<i class="mdi mdi-help-circle-outline menu-icon"></i>
				<span class="menu-title">Help & Support</span>
			</a>
		</li>

		<li class="nav-item">
			<a class="nav-link" href="<?= $this->config->item('sso_enable') ? sso_url('logout') : site_url('logout') ?>">
				<i class="mdi mdi-logout-variant menu-icon"></i>
				<span class="menu-title">Logout</span>
			</a>
		</li>
	</ul>
</div>
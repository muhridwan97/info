<div class="card shadow-lg border-0 my-5">
	<div class="card-body px-4 py-5 p-sm-5">
		<h3 class="font-weight-bold">Register</h3>
		<h6 class="font-weight-light text-fade">Create New Account.</h6>

		<?php $this->load->view('partials/_alert') ?>

		<form action="<?= site_url('auth/register') ?>" method="post" class="pt-3">
			<?= _csrf() ?>
			<div class="form-group">
				<input type="text" class="form-control form-control-lg" id="name" name="name"
					   value="<?= set_value('name') ?>" placeholder="Your full name" required maxlength="50">
				<?= form_error('name') ?>
			</div>
			<div class="form-group">
				<input type="text" class="form-control form-control-lg" id="username" name="username"
					   value="<?= set_value('username') ?>" placeholder="Username or email" required maxlength="50">
				<?= form_error('username') ?>
			</div>
			<div class="form-group">
				<input type="email" class="form-control form-control-lg" id="email" name="email"
					   value="<?= set_value('email') ?>" placeholder="Email address" required>
				<?= form_error('email') ?>
			</div>
			<div class="form-group">
				<input type="password" class="form-control form-control-lg" id="password" name="password"
					   placeholder="Pick a password" required>
				<?= form_error('password') ?>
			</div>
			<div class="form-group">
				<input type="password" class="form-control form-control-lg" id="confirm" name="confirm"
					   placeholder="Confirm the password" required>
				<?= form_error('confirm') ?>
			</div>
			<div class="mb-4">
				<div class="form-check">
					<label class="form-check-label text-muted">
						<input type="checkbox" class="form-check-input" name="terms" id="terms" required>
						I agree to all Terms & Conditions
					</label>
				</div>
				<?= form_error('terms') ?>
			</div>
			<div class="mt-3">
				<button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">
					SIGN UP
				</button>
			</div>
			<div class="text-center mt-4 font-weight-light">
				Already have an account? <a href="<?= site_url('auth/login') ?>" class="text-primary">Login</a>
			</div>
		</form>
	</div>
</div>

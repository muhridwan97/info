<div class="card shadow-lg border-0 my-5">
	<div class="card-body px-4 py-5 p-sm-5">
		<div class="text-center">
			<h3 class="font-weight-bold">Hello! let's get started</h3>
			<h6 class="font-weight-light mb-3 text-fade">Sign in to continue</h6>
		</div>

		<?php $this->load->view('partials/_alert') ?>

		<form action="<?= site_url('auth/login') . if_empty($_SERVER['QUERY_STRING'], '', '?') ?>" method="post" class="pt-2">
			<?= _csrf() ?>
			<div class="form-group">
				<input type="text" class="form-control form-control-lg" id="username" name="username"
					   value="<?= set_value('username') ?>" placeholder="Username or email">
				<?= form_error('username') ?>
			</div>
			<div class="form-group">
				<input type="password" class="form-control form-control-lg" id="password" name="password"
					   placeholder="Password">
				<?= form_error('password') ?>
			</div>
			<div class="form-group d-flex justify-content-between align-items-center">
				<div class="custom-control custom-checkbox">
					<input type="checkbox" class="custom-control-input" id="remember" name="remember">
					<label class="custom-control-label" for="remember">Remember</label>
				</div>
				<a href="<?= site_url('auth/password/forgot-password') ?>">
					Forgot password?
				</a>
			</div>
			<div class="mt-3">
				<button type="submit" class="btn btn-block btn-warning btn-lg" data-toggle="one-touch">
					SIGN IN
				</button>
			</div>
			<div class="text-center mt-4 font-weight-light">
				Don't have an account? <a href="<?= site_url('auth/register') ?>" class="text-primary">Register now</a>
			</div>
		</form>
	</div>
</div>

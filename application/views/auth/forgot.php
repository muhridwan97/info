<div class="card shadow-lg border-0 my-5">
	<div class="card-body px-4 py-5 p-sm-5">
		<h3 class="font-weight-bold mb-3">Reset Password</h3>

		<?php $this->load->view('partials/_alert') ?>

		<p class="mb-2">
			Enter your email address that you used to register. We'll send you an email with your username and a
			link to reset your password.
		</p>

		<form action="<?= site_url('auth/password/forgot-password') ?>" method="post" class="pt-3">
			<?= _csrf() ?>
			<div class="form-group">
				<input type="email" class="form-control form-control-lg" id="email" name="email" required
					   value="<?= set_value('email') ?>" placeholder="Registered email">
				<?= form_error('email') ?>
			</div>
			<div class="mt-3">
				<button type="submit" class="btn btn-block btn-danger btn-lg" data-toggle="one-touch">
					RESET PASSWORD
				</button>
			</div>
			<div class="text-center mt-4 font-weight-light">
				Remember password? <a href="<?= site_url('auth/login') ?>" class="text-primary">Login</a>
			</div>
		</form>
	</div>
</div>

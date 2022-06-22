<form class="form-plaintext">
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">View User</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Name</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= if_empty($user['name'], 'No name') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Username</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= if_empty($user['username'], 'No username') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Email</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <a href="mailto:<?= $user['email'] ?>">
                                    <?= if_empty($user['email'], 'No email') ?>
                                </a>
                            </p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Role</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?php foreach ($roles as $role): ?>
                                    <a href="<?= site_url('master/role/view/' . $role['id']) ?>" class="d-block">
                                        <?= $role['role'] ?>
                                    </a>
                                <?php endforeach; ?>
                                <?php if(empty($roles)): ?>
                                    No roles
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Avatar</label>
                        <div class="col-sm-8">
                            <?php if($this->config->item('sso_enable')): ?>
                                <img src="<?= sso_url(if_empty($user['avatar'], 'assets/dist/img/no-avatar.png', 'uploads/')) ?>"
                                     alt="avatar" class="img-fluid rounded my-2" style="max-width: 100px">
                            <?php else: ?>
                                <img src="<?= base_url(if_empty($user['avatar'], 'assets/dist/img/no-avatar.png', 'uploads/')) ?>"
                                     alt="avatar" class="img-fluid rounded my-2" style="max-width: 100px">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Status</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= if_empty($user['status'], 'No status') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">User Type</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= if_empty($user['user_type'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Related Employee</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
								<?php if(!empty($user['employee_name'])): ?>
									<a href="<?= site_url('master/employee/view/' . $user['id_employee']) ?>">
										<?= if_empty($user['employee_name'], '-') ?>
									</a>
								<?php else: ?>
									-
								<?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Created At</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= format_date($user['created_at'], 'd F Y H:i') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Updated At</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= if_empty(format_date($user['updated_at'], 'd F Y H:i'),  '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body d-flex justify-content-between">
            <button onclick="history.back()" type="button" class="btn btn-light">Back</button>
            <?php if(AuthorizationModel::isAuthorized(PERMISSION_USER_EDIT)): ?>
                <a href="<?= site_url('master/user/edit/' . $user['id']) ?>" class="btn btn-primary">
                    Edit User
                </a>
            <?php endif; ?>
        </div>
    </div>
</form>

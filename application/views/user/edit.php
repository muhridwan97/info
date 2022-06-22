<form action="<?= site_url('master/user/update/' . $user['id']) ?>" method="POST" id="form-user" enctype="multipart/form-data">
    <?= _csrf() ?>
    <?= _method('put') ?>
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Edit User</h5>
            <div class="row">
                <div class="col-md-7">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" required maxlength="50"
                               <?= $user['username'] == 'admin' ? 'readonly' : '' ?>
                               value="<?= set_value('name', $user['name']) ?>" placeholder="Enter your full name">
                        <?= form_error('name') ?>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" maxlength="20"
                            <?= $user['username'] == 'admin' ? 'readonly' : '' ?>
                               value="<?= set_value('username', $user['username']) ?>" placeholder="User unique ID">
                        <?= form_error('username') ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" required maxlength="50"
                    <?= $user['username'] == 'admin' ? 'readonly' : '' ?>
                       value="<?= set_value('email', $user['email']) ?>" placeholder="Enter email address">
                <?= form_error('email') ?>
            </div>
            <?php if($user['username'] != 'admin'): ?>
                <div class="form-group">
                    <label>Avatar</label>
                    <input type="file" id="avatar" name="avatar" class="file-upload-default" data-max-size="3000000">
                    <div class="input-group col-xs-12">
                        <input type="text" class="form-control file-upload-info" disabled
                               value="<?= $user['avatar'] ?>" placeholder="Upload photo">
                        <span class="input-group-append">
                        <button class="file-upload-browse btn btn-success btn-simple-upload" type="button">
                            Upload
                        </button>
                    </span>
                    </div>
                    <?= form_error('avatar') ?>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="custom-select" name="status" id="status" required>
                                <option value="">-- Please select --</option>
                                <option value="<?= UserModel::STATUS_PENDING ?>"
                                    <?= set_select('status', UserModel::STATUS_PENDING, $user['status'] == UserModel::STATUS_PENDING) ?>>
                                    <?= UserModel::STATUS_PENDING ?>
                                </option>
                                <option value="<?= UserModel::STATUS_ACTIVATED ?>"
                                    <?= set_select('status', UserModel::STATUS_ACTIVATED, $user['status'] == UserModel::STATUS_ACTIVATED) ?>>
                                    <?= UserModel::STATUS_ACTIVATED ?>
                                </option>
                                <option value="<?= UserModel::STATUS_SUSPENDED ?>"
                                    <?= set_select('status', UserModel::STATUS_SUSPENDED, $user['status'] == UserModel::STATUS_SUSPENDED) ?>>
                                    <?= UserModel::STATUS_SUSPENDED ?>
                                </option>
                            </select>
                            <?= form_error('status'); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="user_type">User Type</label>
                            <select class="custom-select" name="user_type" id="user_type" required>
                                <option value="INTERNAL"<?= set_select('user_type', 'INTERNAL', $user['user_type'] == 'INTERNAL') ?>>
                                    INTERNAL (Employee or trusted member)
                                </option>
                                <option value="EXTERNAL"<?= set_select('user_type', 'EXTERNAL', $user['user_type'] == 'EXTERNAL') ?>>
                                    EXTERNAL (Outsider such as applicant)
                                </option>
                            </select>
                            <?= form_error('user_type'); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if($user['username'] != 'admin'): ?>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Role</h5>
                <p class="text-muted">User at least must has one role</p>
                <div class="form-group">
                    <div class="row">
                        <?php foreach ($roles as $role): ?>
                            <?php $isChecked = in_array($role['id'], array_column($userRoles, 'id_role')); ?>
                            <div class="col-sm-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input"
                                           id="role_<?= $role['id'] ?>"
                                           name="roles[]" value="<?= $role['id'] ?>"
                                        <?= set_checkbox('roles[]', $role['id'], $isChecked); ?>>
                                    <label class="custom-control-label" for="role_<?= $role['id'] ?>">
                                        <?= $role['role'] ?>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?= form_error('roles[]'); ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Credential</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input type="password" class="form-control" id="password" name="password" minlength="6"
                               maxlength="50" placeholder="New password">
                        <span class="form-text">Leave it blank if you don't change the password.</span>
                    </div>
                    <?= form_error('password') ?>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                               maxlength="50" placeholder="Confirm new password">
                    </div>
                    <?= form_error('confirm_password') ?>
                </div>
            </div>
        </div>
    </div>

    <?php if($this->config->item('sso_enable')): ?>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title text-danger">Attention</h5>
                <p>
                    <i class="mdi mdi-information-outline"></i>
                    If Single Sign On is enabled, the user will registered into central User Database!
                </p>
            </div>
        </div>
    <?php endif; ?>

    <div class="card mb-3">
        <div class="card-body d-flex justify-content-between">
            <button type="button" onclick="history.back()" class="btn btn-light">Back</button>
            <button type="submit" class="btn btn-primary" data-toggle="one-touch" data-touch-message="Updating...">Update User</button>
        </div>
    </div>
</form>
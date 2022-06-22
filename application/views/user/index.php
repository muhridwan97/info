<div class="card mb-3">
    <div class="card-body">
        <div class="d-sm-flex align-items-center justify-content-between">
            <h5 class="card-title mb-sm-0">Data Users</h5>
            <div>
                <a href="#modal-filter" data-toggle="modal" class="btn btn-info btn-sm pr-2 pl-2">
                    <i class="mdi mdi-filter-variant"></i>
                </a>
                <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=true" class="btn btn-info btn-sm pr-2 pl-2">
                    <i class="mdi mdi-file-download-outline"></i>
                </a>
                <?php if(!$this->config->item('sso_enable')): ?>
                    <?php if(AuthorizationModel::isAuthorized(PERMISSION_USER_CREATE)): ?>
                        <a href="<?= site_url('master/user/create') ?>" class="btn btn-sm btn-success">
                            <i class="mdi mdi-plus-box-outline mr-2"></i>CREATE
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        <table class="table table-hover table-sm mt-3 responsive">
            <thead>
            <tr>
                <th class="text-md-center" style="width: 60px">No</th>
                <th>Name</th>
                <th>Email</th>
                <th>Type</th>
                <th>Role</th>
                <th>Status</th>
                <th style="min-width: 120px" class="text-md-right">Action</th>
            </tr>
            </thead>
            <tbody>
			<?php
			$userStatuses = [
				UserModel::STATUS_PENDING => 'light',
				UserModel::STATUS_ACTIVATED => 'success',
				UserModel::STATUS_SUSPENDED => 'danger',
			];
			?>
            <?php $no = isset($users) ? ($users['current_page'] - 1) * $users['per_page'] : 0 ?>
            <?php foreach ($users['data'] as $user): ?>
                <tr>
                    <td class="text-md-center"><?= ++$no ?></td>
                    <td><?= $user['name'] ?></td>
                    <td><a href="mailto:<?= $user['email'] ?>"><?= $user['email'] ?></a></td>
                    <td><?= $user['user_type'] ?: 'NOT SET' ?></td>
                    <td><?= str_replace(',', '<br>', if_empty($user['roles'], '-')) ?></td>
					<td>
						<span class="badge badge-<?= get_if_exist($userStatuses, $user['status'], 'primary') ?>">
							<?= $user['status'] ?>
						</span>
					</td>
                    <td class="text-md-right">
                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm dropdown-toggle btn-action" type="button" id="actionButton" data-toggle="dropdown">
                                Action
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="actionButton">
                                <?php if(AuthorizationModel::isAuthorized(PERMISSION_USER_VIEW)): ?>
                                    <a class="dropdown-item"
                                       href="<?= site_url('master/user/view/' . $user['id']) ?>">
                                        <i class="mdi mdi-eye-outline mr-2"></i> View
                                    </a>
                                <?php endif; ?>
                                <?php if(AuthorizationModel::isAuthorized(PERMISSION_USER_EDIT)): ?>
                                    <a class="dropdown-item"
                                       href="<?= site_url('master/user/edit/' . $user['id']) ?>">
                                        <i class="mdi mdi-square-edit-outline mr-2"></i> Edit
                                    </a>
                                <?php endif; ?>
                                <?php if(!$this->config->item('sso_enable')): ?>
                                    <?php if(AuthorizationModel::isAuthorized(PERMISSION_USER_DELETE)): ?>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item btn-delete" href="#modal-delete" data-toggle="modal"
                                           data-id="<?= $user['id'] ?>" data-label="<?= $user['name'] ?>" data-title="User"
                                           data-url="<?= site_url('master/user/delete/' . $user['id']) ?>">
                                            <i class="mdi mdi-trash-can-outline mr-2"></i> Delete
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($users['data'])): ?>
                <tr>
                    <td colspan="7" class="text-center">No users data available</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
        <?php $this->load->view('partials/_pagination', ['pagination' => $users]) ?>
    </div>
</div>

<?php $this->load->view('user/_modal_filter') ?>
<?php $this->load->view('partials/modals/_alert') ?>

<?php if(AuthorizationModel::isAuthorized(PERMISSION_USER_DELETE)): ?>
    <?php $this->load->view('partials/modals/_delete') ?>
<?php endif; ?>

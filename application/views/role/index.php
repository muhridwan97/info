<div class="card mb-3">
    <div class="card-body">
        <div class="d-sm-flex align-items-center justify-content-between">
            <h5 class="card-title mb-sm-0">Data Roles</h5>
            <div>
                <a href="#modal-filter" data-toggle="modal" class="btn btn-info btn-sm pr-2 pl-2">
                    <i class="mdi mdi-filter-variant mr-0"></i>
                </a>
                <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=true" class="btn btn-info btn-sm pr-2 pl-2">
                    <i class="mdi mdi-file-download-outline mr-0"></i>
                </a>
                <?php if(AuthorizationModel::isAuthorized(PERMISSION_ROLE_CREATE)): ?>
                    <a href="<?= site_url('master/role/create') ?>" class="btn btn-sm btn-primary">
                        <i class="mdi mdi-plus-box-outline mr-2"></i>CREATE
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <table class="table table-hover table-sm mt-3 responsive">
            <thead>
            <tr>
                <th class="text-md-center" style="width: 60px">No</th>
                <th>Role</th>
                <th>Permission</th>
                <th>Description</th>
                <th>Created At</th>
                <th style="min-width: 120px" class="text-md-right">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = isset($roles) ? ($roles['current_page'] - 1) * $roles['per_page'] : 0 ?>
            <?php foreach ($roles['data'] as $role): ?>
                <tr>
                    <td class="text-md-center"><?= ++$no ?></td>
                    <td><?= $role['role'] ?></td>
                    <td><?= numerical($role['total_permission']) ?></td>
                    <td><?= if_empty($role['description'], '-') ?></td>
                    <td><?= format_date($role['created_at'], 'd F Y H:i') ?></td>
                    <td class="text-md-right">
                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm dropdown-toggle btn-action" type="button" data-toggle="dropdown">
                                Action
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <?php if(AuthorizationModel::isAuthorized(PERMISSION_ROLE_VIEW)): ?>
                                    <a class="dropdown-item" href="<?= site_url('master/role/view/' . $role['id']) ?>">
                                        <i class="mdi mdi-eye-outline mr-2"></i> View
                                    </a>
                                <?php endif; ?>
                                <?php if(AuthorizationModel::isAuthorized(PERMISSION_ROLE_EDIT)): ?>
                                    <a class="dropdown-item" href="<?= site_url('master/role/edit/' . $role['id']) ?>">
                                        <i class="mdi mdi-square-edit-outline mr-2"></i> Edit
                                    </a>
                                <?php endif; ?>
                                <?php if(AuthorizationModel::isAuthorized(PERMISSION_ROLE_DELETE)): ?>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item btn-delete" href="#modal-delete" data-toggle="modal"
                                       data-id="<?= $role['id'] ?>" data-label="<?= $role['role'] ?>" data-title="Role"
                                       data-url="<?= site_url('master/role/delete/' . $role['id']) ?>">
                                        <i class="mdi mdi-trash-can-outline mr-2"></i> Delete
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if(empty($roles['data'])): ?>
                <tr>
                    <td colspan="6">No roles data available</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>

        <?php $this->load->view('partials/_pagination', ['pagination' => $roles]) ?>
    </div>
</div>

<?php $this->load->view('role/_modal_filter') ?>

<?php if(AuthorizationModel::isAuthorized(PERMISSION_ROLE_DELETE)): ?>
    <?php $this->load->view('partials/modals/_delete') ?>
<?php endif; ?>

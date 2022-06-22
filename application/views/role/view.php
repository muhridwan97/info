<form class="form-plaintext">
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">View Role</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Role Name</label>
                        <div class="col-sm-9">
                            <p class="form-control-plaintext">
                                <?= if_empty($role['role'], 'No role') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Permission</label>
                        <div class="col-sm-9">
                            <p class="form-control-plaintext">
                                <?= if_empty($role['total_permission'], 0) ?> permissions
                            </p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Description</label>
                        <div class="col-sm-9">
                            <p class="form-control-plaintext">
                                <?= if_empty($role['description'], 'No description') ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Created At</label>
                        <div class="col-sm-9">
                            <p class="form-control-plaintext">
                                <?= format_date($role['created_at'], 'd F Y H:i') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Updated At</label>
                        <div class="col-sm-9">
                            <p class="form-control-plaintext">
                                <?= if_empty(format_date($role['updated_at'], 'd F Y H:i'),  '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Permissions</h5>
            <span class="text-muted">Role at least must has one permission</span>

            <div class="form-group">
                <div class="row">
                    <?php $lastGroup = '' ?>
                    <?php $lastSubGroup = '' ?>
                    <?php foreach ($permissions as $permission): ?>
                        <?php
                        $module = $permission['module'];
                        $submodule = $permission['submodule'];
                        ?>

                        <?php if($lastGroup != $module): ?>
                            <?php
                            $lastGroup = $module;
                            $lastGroupName = preg_replace('/ /', '_', $lastGroup);
                            ?>
                            <div class="col-12 mt-3">
                                <hr>
                                <h5 class="mt-2">
                                    Module <?= ucwords($lastGroup) ?>
                                </h5>
                                <hr class="mb-0">
                            </div>
                        <?php endif; ?>

                        <?php if($lastSubGroup != $submodule): ?>
                            <?php $lastSubGroup = $submodule; ?>
                            <div class="col-12">
                                <div class="mb-2 mt-3">
                                    <h6>
                                        <?= ucwords(preg_replace('/\-/', ' ', $lastSubGroup)) ?>
                                    </h6>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="col-sm-4">
                            <p class="mb-0 text-muted">
                                <i class="mdi mdi-check-box-outline mr-2"></i>
								<?= ucwords(preg_replace('/(_|\-)/', ' ', $permission['permission'])) ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?= form_error('permissions[]'); ?>
            </div>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body d-flex justify-content-between">
            <button onclick="history.back()" type="button" class="btn btn-light">Back</button>
            <?php if(AuthorizationModel::isAuthorized(PERMISSION_ROLE_EDIT)): ?>
                <a href="<?= site_url('master/role/edit/' . $role['id']) ?>" class="btn btn-primary">
                    Edit Role
                </a>
            <?php endif; ?>
        </div>
    </div>
</form>

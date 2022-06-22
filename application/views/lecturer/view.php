<div class="form-plaintext">
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">View Lecturer</h5>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Name</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= if_empty($lecturer['name'], 'No name') ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">NO NIP</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= if_empty($lecturer['no_lecturer'], '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Position</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= if_empty($lecturer['position'], 'position') ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Status</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?php
                                $statuses = [
                                    LecturerModel::STATUS_ACTIVE => 'success',
                                    LecturerModel::STATUS_INACTIVE => 'danger',
                                ]
                                ?>
                                <label class="mb-0 small badge badge-<?= $statuses[$lecturer['status']] ?>">
                                    <?= $lecturer['status'] ?>
                                </label>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Related Account</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?php if(empty($lecturer['username'])): ?>
                                    No account
                                <?php else: ?>
                                    <a href="<?= site_url('master/user/view/' . $lecturer['id_user']) ?>">
                                        <?= $lecturer['username'] ?>
                                    </a>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Description</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= if_empty($lecturer['description'], '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card grid-margin">
        <div class="card-body d-flex justify-content-between">
            <button onclick="history.back()" type="button" class="btn btn-light">Back</button>
            <?php if(!$this->config->item('sso_enable')): ?>
                <?php if(AuthorizationModel::isAuthorized(PERMISSION_LECTURER_EDIT)): ?>
                    <a href="<?= site_url('master/lecturer/edit/' . $lecturer['id']) ?>" class="btn btn-primary">
                        Edit Lecturer
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

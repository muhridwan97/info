<div class="form-plaintext">
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">View Student</h5>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Name</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= if_empty($student['name'], 'No name') ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">NIM</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= if_empty($student['no_student'], '-') ?>
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
                                <?php if(empty($student['username'])): ?>
                                    No account
                                <?php else: ?>
                                    <a href="<?= site_url('master/user/view/' . $student['id_user']) ?>">
                                        <?= $student['username'] ?>
                                    </a>
                                <?php endif; ?>
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
                                    StudentModel::STATUS_ACTIVE => 'success',
                                    StudentModel::STATUS_INACTIVE => 'danger',
                                ]
                                ?>
                                <label class="mb-0 small badge badge-<?= $statuses[$student['status']] ?>">
                                    <?= $student['status'] ?>
                                </label>
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
                                <?= if_empty($student['description'], '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Pembimbing</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= if_empty($student['nama_pembimbing'], '-') ?>
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
                <?php if(AuthorizationModel::isAuthorized(PERMISSION_STUDENT_EDIT)): ?>
                    <a href="<?= site_url('master/student/edit/' . $student['id']) ?>" class="btn btn-primary">
                        Edit Student
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

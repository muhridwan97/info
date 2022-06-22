<div class="form-plaintext">
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">View Employee</h5>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Name</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= if_empty($employee['name'], 'No name') ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Employee ID</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= if_empty($employee['no_employee'], '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Supervisor</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= if_empty($employee['supervisor_name'], 'No supervisor') ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Photo</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <a href="<?= base_url(if_empty($employee['photo'], 'assets/dist/img/no-image.png', 'uploads/')) ?>">
                                    Download
                                </a>
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
                                <?= if_empty($employee['position'], 'position') ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Position Level</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= if_empty($employee['position_level'], 'No position level') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Hire Date</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= if_empty(format_date($employee['enter_date'], 'd F Y'), '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Quit Enter</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= if_empty(format_date($employee['quit_date'], 'd F Y'), '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Department</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= if_empty($employee['department'], 'No department') ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Gender</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= ucfirst($employee['gender']) ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Company</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= if_empty($employee['company'], 'No company') ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Work Location</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= if_empty($employee['work_location'], 'No work location') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Tax No</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= if_empty($employee['tax_no'], 'No tax') ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Tax Address</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= if_empty($employee['tax_address'], 'No address') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">ID Card No</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= if_empty($employee['id_card_no'], 'No ID card') ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">ID Card Address</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= if_empty($employee['id_card_address'], 'No address') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Place of Birth</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= if_empty($employee['place_of_birth'], 'No place of birth') ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Date of Birth</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= if_empty(format_date($employee['date_of_birth'], 'd F Y'), 'No birthday') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Contact Phone</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= if_empty($employee['contact_phone'], 'No contact phone') ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Contact Mobile</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= if_empty($employee['contact_mobile'], 'No contact mobile') ?>
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
                                <?php if(empty($employee['username'])): ?>
                                    No account
                                <?php else: ?>
                                    <a href="<?= site_url('master/user/view/' . $employee['id_user']) ?>">
                                        <?= $employee['username'] ?>
                                    </a>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Related Email</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= if_empty($employee['email'], 'No email') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Created At</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= format_date($employee['created_at'], 'd F Y H:i') ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Updated At</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= if_empty(format_date($employee['updated_at'], 'd F Y H:i'), '-') ?>
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
                                <?= if_empty($employee['description'], '-') ?>
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
                                    EmployeeModel::STATUS_ACTIVE => 'success',
                                    EmployeeModel::STATUS_INACTIVE => 'danger',
                                ]
                                ?>
                                <label class="mb-0 small badge badge-<?= $statuses[$employee['status']] ?>">
                                    <?= $employee['status'] ?>
                                </label>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Subordinates</h5>
            <table class="table table-md responsive">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>No Employee</th>
                    <th>Department</th>
                    <th>Position</th>
                    <th>Level</th>
                    <th>Email</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($subordinates as $index => $subordinate): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td>
                            <a href="<?= site_url('master/employee/view/'. $subordinate['id']) ?>">
                                <?= $subordinate['name'] ?>
                            </a>
                        </td>
                        <td><?= $subordinate['no_employee'] ?></td>
                        <td><?= if_empty($subordinate['department'], '-') ?></td>
                        <td><?= if_empty($subordinate['position'], '-') ?></td>
                        <td><?= if_empty($subordinate['position_level'], '-') ?></td>
                        <td><?= if_empty($subordinate['email'], '-') ?></td>
                    </tr>
                <?php endforeach; ?>

                <?php if(empty($subordinates)): ?>
                    <tr>
                        <td colspan="6" class="text-center">No subordinate available</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card grid-margin">
        <div class="card-body d-flex justify-content-between">
            <button onclick="history.back()" type="button" class="btn btn-light">Back</button>
            <?php if(!$this->config->item('sso_enable')): ?>
                <?php if(AuthorizationModel::isAuthorized(PERMISSION_EMPLOYEE_EDIT)): ?>
                    <a href="<?= site_url('master/employee/edit/' . $employee['id']) ?>" class="btn btn-primary">
                        Edit Employee
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

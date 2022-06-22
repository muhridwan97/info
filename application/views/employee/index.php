<div class="card mb-3">
    <div class="card-body">
        <div class="d-sm-flex justify-content-between">
            <h5 class="card-title mb-sm-0">Data Employees</h5>
            <div>
                <a href="#modal-filter" data-toggle="modal" class="btn btn-info btn-sm pr-2 pl-2">
                    <i class="mdi mdi-filter-variant"></i>
                </a>
                <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=true" class="btn btn-info btn-sm pr-2 pl-2">
                    <i class="mdi mdi-file-download-outline"></i>
                </a>
                <?php if(!$this->config->item('sso_enable')): ?>
                    <?php if(AuthorizationModel::isAuthorized(PERMISSION_EMPLOYEE_CREATE)): ?>
                        <a href="<?= site_url('master/employee/create') ?>" class="btn btn-sm btn-primary">
                            <i class="mdi mdi-plus-box-outline mr-2"></i>CREATE
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="<?= $employees['total_data'] > 3 ? 'table-responsive' : '' ?>">
            <table class="table table-hover table-sm mt-3 responsive" id="table-employee">
                <thead>
                <tr>
                    <th class="text-md-center" style="width: 60px">No</th>
                    <th>No Employee</th>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Level</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th style="min-width: 120px" class="text-md-right">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $statuses = [
                    EmployeeModel::STATUS_ACTIVE => 'success',
                    EmployeeModel::STATUS_INACTIVE => 'danger',
                ]
                ?>
                <?php $no = isset($employees) ? ($employees['current_page'] - 1) * $employees['per_page'] : 0 ?>
                <?php foreach ($employees['data'] as $employee): ?>
                    <tr>
                        <td class="text-md-center"><?= ++$no ?></td>
                        <td><?= $employee['no_employee'] ?></td>
                        <td><?= $employee['name'] ?></td>
                        <td><?= if_empty($employee['position'], '-') ?></td>
                        <td><?= if_empty($employee['position_level'], '-') ?></td>
                        <td><?= if_empty($employee['department'], '-') ?></td>
                        <td>
                            <label class="badge badge-<?= $statuses[$employee['status']] ?>">
                                <?= $employee['status'] ?>
                            </label>
                        </td>
                        <td class="text-md-right">
                            <div class="dropdown">
                                <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="actionButton" data-toggle="dropdown">
                                    Action
                                </button>
                                <div class="dropdown-menu dropdown-menu-right row-employee"
                                     data-id="<?= $employee['id'] ?>"
                                     data-label="<?= $employee['name'] ?>">
                                    <?php if(AuthorizationModel::isAuthorized(PERMISSION_EMPLOYEE_VIEW)): ?>
                                        <a class="dropdown-item" href="<?= site_url('master/employee/view/' . $employee['id']) ?>">
                                            <i class="mdi mdi-eye-outline mr-2"></i> View
                                        </a>
                                    <?php endif; ?>
                                    <?php if(!$this->config->item('sso_enable')): ?>
                                        <?php if(AuthorizationModel::isAuthorized(PERMISSION_EMPLOYEE_EDIT)): ?>
                                            <a class="dropdown-item" href="<?= site_url('master/employee/edit/' . $employee['id']) ?>">
                                                <i class="mdi mdi-square-edit-outline mr-2"></i> Edit
                                            </a>
                                        <?php endif; ?>
                                        <?php if(AuthorizationModel::isAuthorized(PERMISSION_EMPLOYEE_DELETE)): ?>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item btn-delete" href="#modal-delete" data-toggle="modal"
                                               data-id="<?= $employee['id'] ?>" data-label="<?= $employee['name'] ?>" data-title="Employee"
                                               data-url="<?= site_url('master/employee/delete/' . $employee['id']) ?>">
                                                <i class="mdi mdi-trash-can-outline mr-2"></i> Delete
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if(empty($employees['data'])): ?>
                    <tr>
                        <td colspan="8">No employees data available</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php $this->load->view('partials/_pagination', ['pagination' => $employees]) ?>
    </div>
</div>

<?php $this->load->view('employee/_modal_filter') ?>

<?php if(AuthorizationModel::isAuthorized(PERMISSION_EMPLOYEE_DELETE)): ?>
    <?php $this->load->view('partials/modals/_delete') ?>
<?php endif; ?>

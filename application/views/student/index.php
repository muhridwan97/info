<div class="card mb-3">
    <div class="card-body">
        <div class="d-sm-flex justify-content-between">
            <h5 class="card-title mb-sm-0">Data Students</h5>
            <div>
                <a href="#modal-filter" data-toggle="modal" class="btn btn-info btn-sm pr-2 pl-2">
                    <i class="mdi mdi-filter-variant"></i>
                </a>
                <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=true" class="btn btn-info btn-sm pr-2 pl-2">
                    <i class="mdi mdi-file-download-outline"></i>
                </a>
                <?php if(!$this->config->item('sso_enable')): ?>
                    <?php if(AuthorizationModel::isAuthorized(PERMISSION_STUDENT_CREATE)): ?>
                        <a href="<?= site_url('master/student/create') ?>" class="btn btn-sm btn-primary">
                            <i class="mdi mdi-plus-box-outline mr-2"></i>CREATE
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="<?= $students['total_data'] > 3 ? 'table-responsive' : '' ?>">
            <table class="table table-hover table-sm mt-3 responsive" id="table-student">
                <thead>
                <tr>
                    <th class="text-md-center" style="width: 60px">No</th>
                    <th>No Student</th>
                    <th>Name</th>
                    <th>Pembimbing</th>
                    <th style="width: 20px">Status</th>
                    <th style="width: 20px" class="text-md-right">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $statuses = [
                    StudentModel::STATUS_ACTIVE => 'success',
                    StudentModel::STATUS_INACTIVE => 'danger',
                ]
                ?>
                <?php $no = isset($students) ? ($students['current_page'] - 1) * $students['per_page'] : 0 ?>
                <?php foreach ($students['data'] as $student): ?>
                    <tr>
                        <td class="text-md-center"><?= ++$no ?></td>
                        <td><?= $student['no_student'] ?></td>
                        <td><?= $student['name'] ?></td>
                        <td><?= $student['nama_pembimbing'] ?></td>
                        <td>
                            <label class="badge badge-<?= $statuses[$student['status']] ?>">
                                <?= $student['status'] ?>
                            </label>
                        </td>
                        <td class="text-md-right">
                            <div class="dropdown">
                                <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="actionButton" data-toggle="dropdown">
                                    Action
                                </button>
                                <div class="dropdown-menu dropdown-menu-right row-student"
                                     data-id="<?= $student['id'] ?>"
                                     data-label="<?= $student['name'] ?>">
                                    <?php if(AuthorizationModel::isAuthorized(PERMISSION_STUDENT_VIEW)): ?>
                                        <a class="dropdown-item" href="<?= site_url('master/student/view/' . $student['id']) ?>">
                                            <i class="mdi mdi-eye-outline mr-2"></i> View
                                        </a>
                                    <?php endif; ?>
                                    <?php if(!$this->config->item('sso_enable')): ?>
                                        <?php if(AuthorizationModel::isAuthorized(PERMISSION_STUDENT_EDIT)): ?>
                                            <a class="dropdown-item" href="<?= site_url('master/student/edit/' . $student['id']) ?>">
                                                <i class="mdi mdi-square-edit-outline mr-2"></i> Edit
                                            </a>
                                        <?php endif; ?>
                                        <?php if(AuthorizationModel::isAuthorized(PERMISSION_STUDENT_DELETE)): ?>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item btn-delete" href="#modal-delete" data-toggle="modal"
                                               data-id="<?= $student['id'] ?>" data-label="<?= $student['name'] ?>" data-title="Student"
                                               data-url="<?= site_url('master/student/delete/' . $student['id']) ?>">
                                                <i class="mdi mdi-trash-can-outline mr-2"></i> Delete
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if(empty($students['data'])): ?>
                    <tr>
                        <td colspan="6">No students data available</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php $this->load->view('partials/_pagination', ['pagination' => $students]) ?>
    </div>
</div>

<?php $this->load->view('student/_modal_filter') ?>

<?php if(AuthorizationModel::isAuthorized(PERMISSION_STUDENT_DELETE)): ?>
    <?php $this->load->view('partials/modals/_delete') ?>
<?php endif; ?>

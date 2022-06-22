<div class="card mb-3">
    <div class="card-body">
        <div class="d-sm-flex justify-content-between">
            <h5 class="card-title mb-sm-0">Data Lecturers</h5>
            <div>
                <a href="#modal-filter" data-toggle="modal" class="btn btn-info btn-sm pr-2 pl-2">
                    <i class="mdi mdi-filter-variant"></i>
                </a>
                <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=true" class="btn btn-info btn-sm pr-2 pl-2">
                    <i class="mdi mdi-file-download-outline"></i>
                </a>
                <?php if(!$this->config->item('sso_enable')): ?>
                    <?php if(AuthorizationModel::isAuthorized(PERMISSION_LECTURER_CREATE)): ?>
                        <a href="<?= site_url('master/lecturer/create') ?>" class="btn btn-sm btn-primary">
                            <i class="mdi mdi-plus-box-outline mr-2"></i>CREATE
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="<?= $lecturers['total_data'] > 3 ? 'table-responsive' : '' ?>">
            <table class="table table-hover table-sm mt-3 responsive" id="table-lecturer">
                <thead>
                <tr>
                    <th class="text-md-center" style="width: 60px">No</th>
                    <th>No Lecturer</th>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Status</th>
                    <th style="min-width: 120px" class="text-md-right">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $statuses = [
                    LecturerModel::STATUS_ACTIVE => 'success',
                    LecturerModel::STATUS_INACTIVE => 'danger',
                ]
                ?>
                <?php $no = isset($lecturers) ? ($lecturers['current_page'] - 1) * $lecturers['per_page'] : 0 ?>
                <?php foreach ($lecturers['data'] as $lecturer): ?>
                    <tr>
                        <td class="text-md-center"><?= ++$no ?></td>
                        <td><?= $lecturer['no_lecturer'] ?></td>
                        <td><?= $lecturer['name'] ?></td>
                        <td><?= if_empty($lecturer['position'], '-') ?></td>
                        <td>
                            <label class="badge badge-<?= $statuses[$lecturer['status']] ?>">
                                <?= $lecturer['status'] ?>
                            </label>
                        </td>
                        <td class="text-md-right">
                            <div class="dropdown">
                                <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="actionButton" data-toggle="dropdown">
                                    Action
                                </button>
                                <div class="dropdown-menu dropdown-menu-right row-lecturer"
                                     data-id="<?= $lecturer['id'] ?>"
                                     data-label="<?= $lecturer['name'] ?>">
                                    <?php if(AuthorizationModel::isAuthorized(PERMISSION_LECTURER_VIEW)): ?>
                                        <a class="dropdown-item" href="<?= site_url('master/lecturer/view/' . $lecturer['id']) ?>">
                                            <i class="mdi mdi-eye-outline mr-2"></i> View
                                        </a>
                                    <?php endif; ?>
                                    <?php if(!$this->config->item('sso_enable')): ?>
                                        <?php if(AuthorizationModel::isAuthorized(PERMISSION_LECTURER_EDIT)): ?>
                                            <a class="dropdown-item" href="<?= site_url('master/lecturer/edit/' . $lecturer['id']) ?>">
                                                <i class="mdi mdi-square-edit-outline mr-2"></i> Edit
                                            </a>
                                        <?php endif; ?>
                                        <?php if(AuthorizationModel::isAuthorized(PERMISSION_LECTURER_DELETE)): ?>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item btn-delete" href="#modal-delete" data-toggle="modal"
                                               data-id="<?= $lecturer['id'] ?>" data-label="<?= $lecturer['name'] ?>" data-title="Lecturer"
                                               data-url="<?= site_url('master/lecturer/delete/' . $lecturer['id']) ?>">
                                                <i class="mdi mdi-trash-can-outline mr-2"></i> Delete
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if(empty($lecturers['data'])): ?>
                    <tr>
                        <td colspan="6">No lecturers data available</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php $this->load->view('partials/_pagination', ['pagination' => $lecturers]) ?>
    </div>
</div>

<?php $this->load->view('lecturer/_modal_filter') ?>

<?php if(AuthorizationModel::isAuthorized(PERMISSION_LECTURER_DELETE)): ?>
    <?php $this->load->view('partials/modals/_delete') ?>
<?php endif; ?>

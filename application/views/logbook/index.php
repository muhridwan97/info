<div class="card mb-3">
    <div class="card-body">
        <div class="d-sm-flex justify-content-between">
            <h5 class="card-title mb-sm-0">Data Logbook</h5>
            <div>
                <a href="#modal-filter" data-toggle="modal" class="btn btn-info btn-sm pr-2 pl-2">
                    <i class="mdi mdi-filter-variant"></i>
                </a>
                <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=true" class="btn btn-info btn-sm pr-2 pl-2">
                    <i class="mdi mdi-file-download-outline"></i>
                </a>
                <?php if(!$this->config->item('sso_enable')): ?>
                    <?php if(AuthorizationModel::isAuthorized(PERMISSION_LOGBOOK_CREATE)): ?>
                        <a href="<?= site_url('skripsi/logbook/create') ?>" class="btn btn-sm btn-primary">
                            <i class="mdi mdi-plus-box-outline mr-2"></i>CREATE
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="<?= $logbooks['total_data'] > 3 ? 'table-responsive' : '' ?>">
            <table class="table table-hover table-sm mt-3 responsive" id="table-logbook">
                <thead>
                <tr>
                    <th class="text-md-center" style="width: 60px">No</th>
                    <th>Tanggal</th>
                    <th>Judul</th>
                    <th>Konsultasi</th>
                    <th>Rincian</th>
                    <th>Mahasiswa</th>
                    <th>NIM</th>
                    <th style="width: 20px">Status</th>
                    <th style="min-width: 20px" class="text-md-right">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $statuses = [
                    LogbookModel::STATUS_VALIDATE => 'success',
                    LogbookModel::STATUS_REJECTED => 'danger',
                    LogbookModel::STATUS_PENDING => 'secondary',
                ]
                ?>
                <?php $no = isset($logbooks) ? ($logbooks['current_page'] - 1) * $logbooks['per_page'] : 0 ?>
                <?php foreach ($logbooks['data'] as $logbook): ?>
                    <tr>
                        <td class="text-md-center"><?= ++$no ?></td>
                        <td><?= format_date($logbook['tanggal'], 'd F Y') ?></td>
                        <td><?= $logbook['judul'] ?></td>
                        <td><?= $logbook['konsultasi'] ?></td>
                        <td><?= $logbook['description'] ?></td>
                        <td><?= $logbook['nama_mahasiswa'] ?></td>
                        <td><?= $logbook['no_student'] ?></td>
                        <td>
                            <label class="badge badge-<?= $statuses[$logbook['status']] ?>">
                                <?= $logbook['status'] ?>
                            </label>
                        </td>
                        <td class="text-md-right">
                            <div class="dropdown">
                                <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="actionButton" data-toggle="dropdown">
                                    Action
                                </button>
                                <div class="dropdown-menu dropdown-menu-right row-logbook"
                                     data-id="<?= $logbook['id'] ?>"
                                     data-label="<?= $logbook['konsultasi'] ?>">
                                    <?php if(AuthorizationModel::isAuthorized(PERMISSION_LOGBOOK_VIEW)): ?>
                                        <a class="dropdown-item" href="<?= site_url('skripsi/logbook/view/' . $logbook['id']) ?>">
                                            <i class="mdi mdi-eye-outline mr-2"></i> View
                                        </a>
                                    <?php endif; ?>
                                    <?php if(!$this->config->item('sso_enable')): ?>
                                        <?php if(AuthorizationModel::isAuthorized(PERMISSION_LOGBOOK_EDIT)): ?>
                                            <a class="dropdown-item" href="<?= site_url('skripsi/logbook/edit/' . $logbook['id']) ?>">
                                                <i class="mdi mdi-square-edit-outline mr-2"></i> Edit
                                            </a>
                                        <?php endif; ?>
                                        <?php if(AuthorizationModel::isAuthorized(PERMISSION_LOGBOOK_VALIDATE) && $logbook['status'] != LogbookModel::STATUS_VALIDATE): ?>
                                            <a class="dropdown-item btn-validate" href="#modal-validate" data-toggle="modal"
                                               data-id="<?= $logbook['id'] ?>" data-label="<?= $logbook['konsultasi'] ?>" data-title="Validate Logbook"
                                               data-url="<?= site_url('skripsi/logbook/validate-logbook/' . $logbook['id']) ?>" data-action="VALIDATED">
                                                <i class="mdi mdi-check-outline mr-2"></i> Validate
                                            </a>
                                            <a class="dropdown-item btn-validate" data-action="REJECTED" data-id="<?= $logbook['id'] ?>"
											   data-label="<?= $logbook['konsultasi'] ?>" data-title="Reject Absent"
											   href="<?= site_url('skripsi/logbook/validate-logbook/' . $logbook['id']) ?>?redirect=<?= base_url(uri_string()) ?>">
												<i class="mdi mdi-close mr-2"></i> Reject
											</a>
                                        <?php endif; ?>
                                        <?php if(AuthorizationModel::isAuthorized(PERMISSION_LOGBOOK_DELETE)): ?>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item btn-delete" href="#modal-delete" data-toggle="modal"
                                               data-id="<?= $logbook['id'] ?>" data-label="<?= $logbook['konsultasi'] ?>" data-title="Logbook"
                                               data-url="<?= site_url('skripsi/logbook/delete/' . $logbook['id']) ?>">
                                                <i class="mdi mdi-trash-can-outline mr-2"></i> Delete
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if(empty($logbooks['data'])): ?>
                    <tr>
                        <td colspan="8">No logbooks data available</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php $this->load->view('partials/_pagination', ['pagination' => $logbooks]) ?>
    </div>
</div>

<?php $this->load->view('logbook/_modal_filter') ?>

<?php if(AuthorizationModel::isAuthorized(PERMISSION_LOGBOOK_DELETE)): ?>
    <?php $this->load->view('partials/modals/_delete') ?>
<?php endif; ?>
<?php if(AuthorizationModel::isAuthorized(PERMISSION_LOGBOOK_VALIDATE)): ?>
    <?php $this->load->view('partials/modals/_validate') ?>
<?php endif; ?>

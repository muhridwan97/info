<div class="card mb-3">
    <div class="card-body">
        <div class="d-sm-flex justify-content-between">
            <h5 class="card-title mb-sm-0">Data Agenda</h5>
            <div>
                <a href="#modal-filter" data-toggle="modal" class="btn btn-info btn-sm pr-2 pl-2">
                    <i class="mdi mdi-filter-variant"></i>
                </a>
                <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=true" class="btn btn-info btn-sm pr-2 pl-2">
                    <i class="mdi mdi-file-download-outline"></i>
                </a>
                <?php if(AuthorizationModel::isAuthorized(PERMISSION_AGENDA_CREATE)): ?>
                    <a href="<?= site_url('agenda/create') ?>" class="btn btn-sm btn-primary">
                        <i class="mdi mdi-plus-box-outline mr-2"></i>CREATE
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="<?= $agendas['total_data'] > 3 ? 'table-responsive' : '' ?>">
            <table class="table table-hover table-sm mt-3 responsive" id="table-agenda">
                <thead>
                <tr>
                    <th class="text-md-center" style="width: 60px">No</th>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Agenda</th>
                    <th style="min-width: 20px" class="text-md-right">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php $no = isset($agendas) ? ($agendas['current_page'] - 1) * $agendas['per_page'] : 0 ?>
                <?php foreach ($agendas['data'] as $agenda): ?>
                    <tr>
                        <td class="text-md-center"><?= ++$no ?></td>
                        <td><?= $agenda['title'] ?></td>
                        <td><?= format_date($agenda['date'], 'd F Y') ?></td>
                        <td><?= substr(strip_tags($agenda['content']),0,110) . "..."?></td>
                        <td class="text-md-right">
                            <div class="dropdown">
                                <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="actionButton" data-toggle="dropdown">
                                    Action
                                </button>
                                <div class="dropdown-menu dropdown-menu-right row-agenda"
                                     data-id="<?= $agenda['id'] ?>"
                                     data-label="<?= $agenda['title'] ?>">
                                    <?php if(AuthorizationModel::isAuthorized(PERMISSION_AGENDA_VIEW)): ?>
                                        <a class="dropdown-item" href="<?= site_url('agenda/view/' . $agenda['id']) ?>">
                                            <i class="mdi mdi-eye-outline mr-2"></i> View
                                        </a>
                                    <?php endif; ?>
                                    <?php if(AuthorizationModel::isAuthorized(PERMISSION_AGENDA_EDIT)): ?>
                                        <a class="dropdown-item" href="<?= site_url('agenda/edit/' . $agenda['id']) ?>">
                                            <i class="mdi mdi-square-edit-outline mr-2"></i> Edit
                                        </a>
                                    <?php endif; ?>
                                    <?php if(AuthorizationModel::isAuthorized(PERMISSION_AGENDA_DELETE)): ?>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item btn-delete" href="#modal-delete" data-toggle="modal"
                                            data-id="<?= $agenda['id'] ?>" data-label="<?= $agenda['title'] ?>" data-title="Agenda"
                                            data-url="<?= site_url('agenda/delete/' . $agenda['id']) ?>">
                                            <i class="mdi mdi-trash-can-outline mr-2"></i> Delete
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if(empty($agendas['data'])): ?>
                    <tr>
                        <td colspan="5">No agendas data available</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php $this->load->view('partials/_pagination', ['pagination' => $agendas]) ?>
    </div>
</div>

<?php $this->load->view('agenda/_modal_filter') ?>

<?php if(AuthorizationModel::isAuthorized(PERMISSION_AGENDA_DELETE)): ?>
    <?php $this->load->view('partials/modals/_delete') ?>
<?php endif; ?>
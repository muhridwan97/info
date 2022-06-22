<div class="form-plaintext">
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">View Logbook</h5>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Judul</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= if_empty($logbook['judul'], 'No judul') ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Tanggal</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= if_empty(format_date($logbook['tanggal'],'d F Y' ), '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Konsultasi</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= if_empty($logbook['konsultasi'], 'no konsultasi') ?>
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
                                    LogbookModel::STATUS_VALIDATE => 'success',
                                    LogbookModel::STATUS_REJECTED => 'danger',
                                    LogbookModel::STATUS_PENDING => 'secondary',
                                ]
                                ?>
                                <label class="mb-0 small badge badge-<?= $statuses[$logbook['status']] ?>">
                                    <?= $logbook['status'] ?>
                                </label>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Rincian</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= if_empty($logbook['description'], '-') ?>
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
                <?php if(AuthorizationModel::isAuthorized(PERMISSION_LOGBOOK_EDIT)): ?>
                    <a href="<?= site_url('skripsi/logbook/edit/' . $logbook['id']) ?>" class="btn btn-primary">
                        Edit Logbook
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

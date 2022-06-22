<div class="card mb-3">
    <div class="card-body py-3">
        <div class="d-sm-flex justify-content-between align-items-center">
            <div>
                <h5 class="card-title mb-2">Database</h5>
                <p class="mb-sm-0 text-muted"><?= $this->config->item('app_name') ?>'s database file</p>
            </div>
            <a href="<?= site_url("utility/backup/database") ?>" class="btn btn-sm btn-info">
                Backup Now
            </a>
        </div>
    </div>
    <div class="card-footer bg-primary text-white small">
        <strong>Live on</strong>
        <?= $this->db->hostname ?><?= empty($this->db->port) ? '' : ':' . $this->db->port ?>/<?= $this->db->database ?>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body py-3">
        <div class="d-sm-flex justify-content-between align-items-center">
            <div>
                <h5 class="card-title mb-2">Upload</h5>
                <p class="mb-sm-0 text-muted"><?= $this->config->item('app_name') ?>'s uploaded file</p>
            </div>
            <a href="<?= site_url("utility/backup/upload") ?>" class="btn btn-sm btn-info">
                Backup Now
            </a>
        </div>
    </div>
    <div class="card-footer bg-primary text-white small">
        <strong>Live on</strong>
        app/uploads
    </div>
</div>

<div class="card mb-3">
    <div class="card-body py-3">
        <div class="d-sm-flex justify-content-between align-items-center">
            <div>
                <h5 class="card-title mb-2">Application</h5>
                <p class="mb-sm-0 text-muted"><?= $this->config->item('app_name') ?>'s source file</p>
            </div>
            <a href="<?= site_url("utility/backup/app") ?>" class="btn btn-sm btn-info">
                Backup Now
            </a>
        </div>
    </div>
    <div class="card-footer bg-primary text-white small">
        <strong>Live on</strong>
        app
    </div>
</div>

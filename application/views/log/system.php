<div class="card mb-3">
    <div class="card-body">
        <div class="d-sm-flex align-items-center justify-content-between">
            <h5 class="card-title mb-sm-0">Data Log</h5>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=true" class="btn btn-info btn-sm pr-2 pl-2">
                <i class="mdi mdi-file-download-outline mr-0"></i>
            </a>
        </div>
        <table class="table table-hover table-md mt-3 responsive">
            <thead>
            <tr>
                <th class="text-md-center" style="width: 60px">No</th>
                <th>Log File</th>
                <th>File Size</th>
                <th>Last Modified</th>
                <th style="min-width: 120px" class="text-md-right">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($logs as $index => $log): ?>
                <tr>
                    <td class="text-md-center"><?= $index + 1 ?></td>
                    <td><?= if_empty($log['log_file'], '-') ?></td>
                    <td><?= numerical($log['file_size']) ?> KB</td>
                    <td><?= format_date($log['last_modified'], 'd F Y H:i') ?></td>
                    <td class="text-md-right">
                        <a href="<?= site_url('utility/system-log/download/' . $log['log_file']) ?>" class="btn btn-sm btn-primary" type="button">
                            Download
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($logs)): ?>
                <tr>
                    <td colspan="2">No log data available</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="card mb-3">
    <div class="card-body">
        <div class="d-sm-flex align-items-center justify-content-between">
            <h5 class="card-title mb-sm-0">Data Log</h5>
            <div>
                <a href="#modal-filter" data-toggle="modal" class="btn btn-info btn-sm pr-2 pl-2">
                    <i class="mdi mdi-filter-variant mr-0"></i>
                </a>
                <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=true" class="btn btn-info btn-sm pr-2 pl-2">
                    <i class="mdi mdi-file-download-outline mr-0"></i>
                </a>
            </div>
        </div>
        <table class="table table-hover table-md mt-3 responsive">
            <thead>
            <tr>
                <th class="text-md-center" style="width: 60px">No</th>
                <th>Event Access</th>
                <th>Event Type</th>
                <th>Data</th>
                <th>Created By</th>
                <th>Created At</th>
                <th style="min-width: 120px" class="text-md-right">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = isset($logs) ? ($logs['current_page'] - 1) * $logs['per_page'] : 0 ?>
            <?php foreach ($logs['data'] as $log): ?>
                <tr>
                    <td class="text-md-center"><?= ++$no ?></td>
                    <td><?= if_empty($log['event_access'], '-') ?></td>
                    <td><?= if_empty($log['event_type'], '-') ?></td>
                    <td><?= if_empty($log['data_label'], '-') ?></td>
                    <td><?= if_empty($log['creator_name'], '-') ?></td>
                    <td><?= if_empty(format_date($log['created_at'], 'd M Y H:i'), '-') ?></td>
                    <td class="text-md-right">
                        <a href="<?= site_url('utility/access-log/view/' . $log['id']) ?>" class="btn btn-sm btn-primary" type="button">
                            View
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if(empty($logs['data'])): ?>
                <tr>
                    <td colspan="7">No log data available</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
        <?php $this->load->view('partials/_pagination', ['pagination' => $logs]) ?>
    </div>
</div>

<?php $this->load->view('log/_modal_access_filter') ?>
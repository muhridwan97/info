<div class="card mb-3">
    <div class="card-body">
        <div class="d-sm-flex justify-content-between">
            <h5 class="card-title mb-sm-0">Data Blog</h5>
            <div>
                <a href="#modal-filter" data-toggle="modal" class="btn btn-info btn-sm pr-2 pl-2">
                    <i class="mdi mdi-filter-variant"></i>
                </a>
                <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=true" class="btn btn-info btn-sm pr-2 pl-2">
                    <i class="mdi mdi-file-download-outline"></i>
                </a>
                <?php if(AuthorizationModel::isAuthorized(PERMISSION_BLOG_CREATE)): ?>
                    <a href="<?= site_url('blog/create') ?>" class="btn btn-sm btn-primary">
                        <i class="mdi mdi-plus-box-outline mr-2"></i>CREATE
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="<?= $blogs['total_data'] > 3 ? 'table-responsive' : '' ?>">
            <table class="table table-hover table-sm mt-3 responsive" id="table-blog">
                <thead>
                <tr>
                    <th class="text-md-center" style="width: 60px">No</th>
                    <th>Category</th>
                    <th>Title</th>
                    <th>Body</th>
                    <th>Writer</th>
                    <th>Date</th>
                    <th>Total View</th>
				    <th style="width: 90px">Status</th>
                    <th style="min-width: 20px" class="text-md-right">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                    $blogStatuses = [
                        BlogModel::STATUS_ACTIVE => 'success',
                        BlogModel::STATUS_INACTIVE => 'warning',
                        BlogModel::STATUS_PENDING => 'default',
                        BlogModel::STATUS_REJECTED => 'danger',
                    ];
                ?>
                <?php $no = isset($blogs) ? ($blogs['current_page'] - 1) * $blogs['per_page'] : 0 ?>
                <?php foreach ($blogs['data'] as $blog): ?>
                    <tr>
                        <td class="text-md-center"><?= ++$no ?></td>
                        <td><?= $blog['category'] ?></td>
                        <td><?= $blog['title'] ?></td>
                        <td><?= substr(strip_tags($blog['body']),0,110) . "..."?></td>
                        <td><?= $blog['writer_name'] ?></td>
                        <td><?= format_date($blog['date'], 'd F Y') ?></td>
                        <td><?php if($blog['count_view']>0):?>
                            <label class="badge badge-success">
                            <?= $blog['count_view'] ?>
                            </label>
                            <?php else: ?>
                            <label class="badge badge-info">
                            <?= $blog['count_view'] ?>
                            </label>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge badge-<?= get_if_exist($blogStatuses, $blog['status'], 'primary') ?>">
                                <?= $blog['status'] ?>
                            </span>
                        </td>
                        <td class="text-md-right">
                            <div class="dropdown">
                                <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="actionButton" data-toggle="dropdown">
                                    Action
                                </button>
                                <div class="dropdown-menu dropdown-menu-right row-blog"
                                     data-id="<?= $blog['id'] ?>"
                                     data-label="<?= $blog['title'] ?>">
                                    <?php if(AuthorizationModel::isAuthorized(PERMISSION_BLOG_VIEW)): ?>
                                        <a class="dropdown-item" href="<?= site_url('blog/view/' . $blog['id']) ?>">
                                            <i class="mdi mdi-eye-outline mr-2"></i> View
                                        </a>
                                    <?php endif; ?>
                                    <?php if(AuthorizationModel::isAuthorized(PERMISSION_BLOG_EDIT)): ?>
                                        <a class="dropdown-item" href="<?= site_url('blog/edit/' . $blog['id']) ?>">
                                            <i class="mdi mdi-square-edit-outline mr-2"></i> Edit
                                        </a>
                                    <?php endif; ?>
                                    <?php if(AuthorizationModel::isAuthorized(PERMISSION_BLOG_VALIDATE) && $blog['status'] == BlogModel::STATUS_PENDING): ?>
                                        <a class="dropdown-item btn-validate" href="#modal-validate" data-toggle="modal"
                                            data-id="<?= $blog['id'] ?>" data-label="<?= $blog['title'] ?>" data-title="Validate Blog"
                                            data-url="<?= site_url('blog/validate-blog/' . $blog['id']) ?>" data-action="VALIDATED">
                                            <i class="mdi mdi-check-outline mr-2"></i> Validate
                                        </a>
                                        <a class="dropdown-item btn-validate" data-action="REJECTED" data-id="<?= $blog['id'] ?>"
                                            data-label="<?= $blog['title'] ?>" data-title="Reject Absent"
                                            href="<?= site_url('blog/validate-blog/' . $blog['id']) ?>?redirect=<?= base_url(uri_string()) ?>">
                                            <i class="mdi mdi-close mr-2"></i> Reject
                                        </a>
                                    <?php endif; ?>
                                    <?php if(AuthorizationModel::isAuthorized(PERMISSION_BLOG_DELETE)): ?>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item btn-delete" href="#modal-delete" data-toggle="modal"
                                            data-id="<?= $blog['id'] ?>" data-label="<?= $blog['title'] ?>" data-title="Blog"
                                            data-url="<?= site_url('blog/delete/' . $blog['id']) ?>">
                                            <i class="mdi mdi-trash-can-outline mr-2"></i> Delete
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if(empty($blogs['data'])): ?>
                    <tr>
                        <td colspan="8">No blogs data available</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php $this->load->view('partials/_pagination', ['pagination' => $blogs]) ?>
    </div>
</div>

<?php $this->load->view('blog/_modal_filter') ?>

<?php if(AuthorizationModel::isAuthorized(PERMISSION_BLOG_DELETE)): ?>
    <?php $this->load->view('partials/modals/_delete') ?>
<?php endif; ?>
<?php if(AuthorizationModel::isAuthorized(PERMISSION_BLOG_VALIDATE)): ?>
    <?php $this->load->view('partials/modals/_validate') ?>
<?php endif; ?>
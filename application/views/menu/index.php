<?php
$segment4 = $this->uri->segment(4);
?>
<div class="card mb-3">
    <div class="card-body">

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
            <?= $path ?>
            </ol>
        </nav>
        <div class="d-sm-flex justify-content-between">
            <h5 class="card-title mb-sm-0">Data Menu</h5>
            <div>
                <a href="#modal-filter" data-toggle="modal" class="btn btn-info btn-sm pr-2 pl-2">
                    <i class="mdi mdi-filter-variant"></i>
                </a>
                <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=true" class="btn btn-info btn-sm pr-2 pl-2">
                    <i class="mdi mdi-file-download-outline"></i>
                </a>
                <?php if (AuthorizationModel::isAuthorized(PERMISSION_MENU_CREATE)) : ?>
                    <a href="<?= site_url('master/menu/create/') . if_empty($segment4) ?>" class="btn btn-sm btn-primary">
                        <i class="mdi mdi-plus-box-outline mr-2"></i>CREATE
                    </a>
                <?php endif; ?>

            </div>
        </div>
        <div>
            <p class="mb-0 pt-1 mt-2 mb-0"></p>
        </div>
        <div class="<?= $menus['total_data'] > 3 ? 'table-responsive' : '' ?>">
            <table class="table table-hover table-sm mt-3 responsive" id="table-menu">
                <thead>
                    <tr>
                        <th class="text-md-center" style="width: 60px">No</th>
                        <th>Url</th>
                        <th>Nama Menu</th>
                        <th>Type</th>
                        <th style="min-width: 20px" class="text-md-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = isset($menus) ? ($menus['current_page'] - 1) * $menus['per_page'] : 0 ?>
                    <?php foreach ($menus['data'] as $menu) : ?>
                        <tr>
                            <td class="text-md-center"><?= ++$no ?></td>
                            <td><?= $menu['url'] ?></td>
                            <td><?= $menu['menu_name'] ?></td>
                            <td><?= $menu['link_type'] ?></td>
                            <td class="text-md-right">
                                <div class="dropdown">
                                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="actionButton" data-toggle="dropdown">
                                        Action
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right row-menu" data-id="<?= $menu['id'] ?>" data-label="<?= $menu['menu_name'] ?>">
                                        <?php if (AuthorizationModel::isAuthorized(PERMISSION_MENU_VIEW)) : ?>
                                            <a class="dropdown-item" href="<?= site_url('master/menu/view/' . $menu['id']) ?>">
                                                <i class="mdi mdi-eye-outline mr-2"></i> View
                                            </a>
                                        <?php endif; ?>
                                        <?php if (AuthorizationModel::isAuthorized(PERMISSION_MENU_EDIT)) : ?>
                                            <a class="dropdown-item" href="<?= site_url('master/menu/edit/' . $menu['id']) ?>">
                                                <i class="mdi mdi-square-edit-outline mr-2"></i> Edit
                                            </a>
                                        <?php endif; ?>
                                        <?php if (AuthorizationModel::isAuthorized(PERMISSION_MENU_CREATE)) : ?>
                                            <a class="dropdown-item" href="<?= site_url('master/menu/sub/' . $menu['id']) ?>">
                                                <i class="mdi mdi-plus-outline mr-2"></i> Add Sub Menu
                                            </a>
                                        <?php endif; ?>
                                        <?php if (AuthorizationModel::isAuthorized(PERMISSION_MENU_DELETE)) : ?>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item btn-delete" href="#modal-delete" data-toggle="modal" data-id="<?= $menu['id'] ?>" data-label="<?= $menu['menu_name'] ?>" data-title="Menu" data-url="<?= site_url('master/menu/delete/' . $menu['id']) ?>">
                                                <i class="mdi mdi-trash-can-outline mr-2"></i> Delete
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($menus['data'])) : ?>
                        <tr>
                            <td colspan="6">No menus data available</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php $this->load->view('partials/_pagination', ['pagination' => $menus]) ?>
    </div>
</div>

<?php $this->load->view('menu/_modal_filter') ?>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_MENU_DELETE)) : ?>
    <?php $this->load->view('partials/modals/_delete') ?>
<?php endif; ?>
<?php if (AuthorizationModel::isAuthorized(PERMISSION_MENU_VALIDATE)) : ?>
    <?php $this->load->view('partials/modals/_validate') ?>
<?php endif; ?>
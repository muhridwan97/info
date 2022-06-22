<div class="form-plaintext">
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">View Blog</h5>
            <div class="col-md-9 single-post-posts" style="padding-bottom: 20px">

                <div id="title-post">
                    <h2><?= $blog['title'] ?></h2>
                </div>
                <div class="detail-post">
                    <p class="date-post">
                        <span class="glyphicon glyphicon-dashboard" style="margin-right:5px;color:rgb(28, 71, 128)"></span><b>Tanggal :</b>
                        <span class="text-date-post"><?= format_date($blog['date'], 'd F Y') ?></span>
                    </p>
                    <p class="created-post" style="margin-right:10px">
                        <span class="glyphicon glyphicon-user" style="margin-right:5px;color:rgb(28, 71, 128)"></span><b>Ditulis oleh : </b>
                        <span class="text-created-post"><?= $blog['writer_name'] ?></span>
                    </p>
                    <p class="created-post">
                        <span class="glyphicon glyphicon-star" style="margin-right:5px;color:rgb(28, 71, 128)"></span><b>Dilihat oleh : </b>
                        <span class="text-created-post"><?= $blog['count_view'] ?></span>
                    </p>
                </div>
                <?php if (!empty($blog['attachment'])) : ?>
                    <a href="<?= base_url() . 'uploads/' . $blog['attachment'] ?>">
                        <button class="btn btn-primary">Lihat Lampiran</button>
                    </a>
                <?php endif; ?>
                <div id="img-post-wrap">
                    <img class="img-responsive img-post" src="<?= asset_url($blog['photo']) ?>" />
                </div>
                <p id="isi-post">
                <div style="text-align:justify">
                    <?= $blog['body'] ?>
                </div>
            </div>
        </div>

        <div class="card grid-margin">
            <div class="card-body d-flex justify-content-between">
                <button onclick="history.back()" type="button" class="btn btn-light">Back</button>
                <?php if (AuthorizationModel::isAuthorized(PERMISSION_BLOG_EDIT)) : ?>
                    <a href="<?= site_url('blog/edit/' . $blog['id']) ?>" class="btn btn-primary ml-auto ml-1">
                        Edit Blog
                    </a>
                <?php endif; ?>
                
                <?php if(AuthorizationModel::isAuthorized(PERMISSION_BLOG_VALIDATE) && $blog['status'] == BlogModel::STATUS_PENDING): ?>
                    <a class="btn btn-info btn-validate ml-1" href="#modal-validate" data-toggle="modal"
                        data-id="<?= $blog['id'] ?>" data-label="<?= $blog['title'] ?>" data-title="Validate Blog"
                        data-url="<?= site_url('blog/validate-blog/' . $blog['id']) ?>" data-action="VALIDATED">
                        <i class="mdi mdi-check-outline mr-2"></i> Validate
                    </a>
                    <a class="btn btn-outline-warning btn-validate ml-1" data-action="REJECTED" data-id="<?= $blog['id'] ?>"
                        data-label="<?= $blog['title'] ?>" data-title="Reject Absent"
                        href="<?= site_url('blog/validate-blog/' . $blog['id']) ?>?redirect=<?= base_url(uri_string()) ?>">
                        <i class="mdi mdi-close mr-2"></i> Reject
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php if(AuthorizationModel::isAuthorized(PERMISSION_BLOG_VALIDATE)): ?>
    <?php $this->load->view('partials/modals/_validate') ?>
<?php endif; ?>
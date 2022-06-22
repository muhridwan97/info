<div class="form-plaintext">
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">View Page</h5>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Nama Halaman</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= if_empty($page['page_name'], 'No name') ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">url</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= if_empty($page['url'], '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Content</h5>
            <div class="col-md-12 mb-3">
                <img src="<?= base_url() . 'uploads/' . $page['photo'] ?>" alt="avatar" class="flex-shrink-0" style="height: 200px">
            </div>
            <br>
            <div class="col-md-12 mb-3">
            <p class="form-control-plaintext">
                <?= if_empty($page['content'], 'No content') ?>
            </p>
            </div>
        </div>
    </div>

    <div class="card grid-margin">
        <div class="card-body d-flex justify-content-between">
            <button onclick="history.back()" type="button" class="btn btn-light">Back</button>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_PAGE_EDIT)) : ?>
                <a href="<?= site_url('page/edit/' . $page['id']) ?>" class="btn btn-primary">
                    Edit Page
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>
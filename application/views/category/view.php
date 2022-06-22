<div class="form-plaintext">
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">View Category</h5>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Nama category</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= if_empty($category['category'], 'No name') ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Description</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">
                                <?= if_empty($category['description'], '-') ?>
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
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_CATEGORY_EDIT)) : ?>
                <a href="<?= site_url('master/category/edit/' . $category['id']) ?>" class="btn btn-primary">
                    Edit Category
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>
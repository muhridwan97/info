<form action="<?= site_url('master/category/save') ?>" method="POST" enctype="multipart/form-data" id="form-category">
    <?= _csrf() ?>
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Create New Category</h5>

            <div class="form-group">
                <label for="category">Nama Category</label>
                <input type="category" class="form-control" id="category" name="category" maxlength="500" required
                        value="<?= set_value('category') ?>" placeholder="menu name">
                <?= form_error('category') ?>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" maxlength="500"
                          placeholder="Enter description"><?= set_value('description') ?></textarea>
                <?= form_error('description') ?>
            </div>

            <div class="d-flex justify-content-between mt-3">
                <button onclick="history.back()" type="button" class="btn btn-light">Back</button>
                <button type="submit" class="btn btn-success mr-2" data-toggle="one-touch" data-touch-message="Saving...">Save Page</button>
            </div>
        </div>
    </div>
</form>

<?php $this->load->view('partials/modals/_alert') ?>
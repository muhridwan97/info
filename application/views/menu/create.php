<form action="<?= site_url('master/menu/save') ?>" method="POST" enctype="multipart/form-data" id="form-menu">
    <?= _csrf() ?>
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Create New Menu</h5>

            <div class="form-group">
                <label for="menu_name">Nama Menu</label>
                <input type="hidden" name="id_parent" value="<?= $parentId ?>">
                <input type="menu_name" class="form-control" id="menu_name" name="menu_name" maxlength="500" required
                        value="<?= set_value('menu_name') ?>" placeholder="menu name">
                <?= form_error('menu_name') ?>
            </div>
            <div class="form-group">
                <label for="link_type">Jenis link</label>
                <select class="custom-select" id="link_type" name="link_type">
                    <option value="">-- link type --</option>
                    <option value="INTERNAL" <?= set_select('link_type', 'INTERNAL') ?>>INTERNAL</option>
                    <option value="EXTERNAL" <?= set_select('link_type', 'EXTERNAL') ?>>EXTERNAL</option>
                </select>
                <?= form_error('link_type') ?>
            </div>
            <div class="form-group" style="display: none ;" id="type-internal">
                <label for="url_page">Pilih Halaman</label>
                <select class="custom-select" id="url_page" name="url_page">
                    <option value="">-- Pilihan Halaman --</option>
                    <?php foreach ($pages as $key => $page) :?>
                    <option value="<?= base_url().$page['url'] ?>" <?= set_select('url_page', base_url().$page['url']) ?>><?= $page['page_name'] ?></option>
                    <?php endforeach; ?>
                </select>
                <?= form_error('url') ?>
            </div>
            <div class="form-group" style="display:none ;" id="type-external">
                <label for="url">Url</label>
                <input type="url" class="form-control" id="url" name="url" maxlength="500"
                        value="<?= set_value('url') ?>" placeholder="Link">
                <?= form_error('url') ?>
            </div>

            <div class="d-flex justify-content-between mt-3">
                <button onclick="history.back()" type="button" class="btn btn-light">Back</button>
                <button type="submit" class="btn btn-success mr-2" data-toggle="one-touch" data-touch-message="Saving...">Save Menu</button>
            </div>
        </div>
    </div>
</form>

<?php $this->load->view('partials/modals/_alert') ?>
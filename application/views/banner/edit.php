<form action="<?= site_url('banner/update/' . $banner['id']) ?>" method="POST" enctype="multipart/form-data" id="form-banner">
    <?= _csrf() ?>
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Edit Banner</h5>

            <div class="form-group">
                <label for="title">Title</label>
                <input type="title" class="form-control" id="title" name="title" maxlength="500" required
                        value="<?= set_value('title', $banner['title']) ?>" placeholder="menu name">
                <?= form_error('title') ?>
            </div>
            <div class="form-group">
                <label for="caption">Caption</label>
                <input type="caption" class="form-control" id="caption" name="caption" maxlength="500" required
                        value="<?= set_value('caption', $banner['caption']) ?>" placeholder="menu name">
                <?= form_error('caption') ?>
            </div>
            <div class="form-group">
                <label>Banner</label>
                <input type="file" id="photo" name="photo" class="file-upload-default" data-max-size="10000000">
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" value="<?= $banner['photo'] ?>" disabled placeholder="Upload photo">
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-success btn-simple-upload" type="button">
                            Upload
                        </button>
                    </span>
                </div>
                <?= form_error('photo') ?>
            </div>

            <div class="d-flex justify-content-between mt-3">
                <button onclick="history.back()" type="button" class="btn btn-light">Back</button>
                <button type="submit" class="btn btn-success mr-2" data-toggle="one-touch" data-touch-message="Updating...">Update Banner</button>
            </div>
        </div>
    </div>
</form>

<?php $this->load->view('partials/modals/_alert') ?>
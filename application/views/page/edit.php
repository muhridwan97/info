<form action="<?= site_url('page/update/' . $page['id']) ?>" method="POST" enctype="multipart/form-data" id="form-page">
    <?= _csrf() ?>
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Edit Page</h5>

            <div class="form-group">
                <label for="page_name">Nama Halaman</label>
                <input type="page_name" class="form-control" id="page_name" name="page_name" maxlength="500" required
                        value="<?= set_value('page_name', $page['page_name']) ?>" placeholder="menu name">
                <?= form_error('page_name') ?>
            </div>
            <div class="form-group">
                <label>Foto</label>
                <input type="file" id="photo" name="photo" class="file-upload-default" data-max-size="10000000">
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" value="<?= $page['photo'] ?>" disabled placeholder="Upload photo">
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-success btn-simple-upload" type="button">
                            Upload
                        </button>
                    </span>
                </div>
                <?= form_error('photo') ?>
            </div>
            <div class="form-group">
                <label for="content">Content</label>
                <textarea class="form-control" id="content" name="content" maxlength="500"
                          placeholder="Enter content"><?= set_value('content', $page['content']) ?></textarea>
                <?= form_error('content') ?>
            </div>

            <div class="d-flex justify-content-between mt-3">
                <button onclick="history.back()" type="button" class="btn btn-light">Back</button>
                <button type="submit" class="btn btn-success mr-2" data-toggle="one-touch" data-touch-message="Updating...">Update Page</button>
            </div>
        </div>
    </div>
</form>

<?php $this->load->view('partials/modals/_alert') ?>
<script src="https://cdn.ckeditor.com/4.16.0/standard-all/ckeditor.js"></script>

<script>
        CKEDITOR.replace( 'content', {
            extraPlugins : 'emoji,colorbutton,justify',
            // removeButtons : 'Underline,Subscript,Superscript,Styles,Table,Symbol,SpecialChar',
            // removePlugins : 'link,image,blockquote,format,horizontalrule,about,list',
        } );
</script>
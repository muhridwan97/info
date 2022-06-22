<form action="<?= site_url('agenda/update/'.$agenda['id']) ?>" method="POST" enctype="multipart/form-data" id="form-agenda">
    <?= _csrf() ?>
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Create New Agenda</h5>

            <div class="form-group">
                <label for="title">Judul</label>
                <input type="text" class="form-control" id="title" name="title" maxlength="500" required
                        value="<?= set_value('title', $agenda['title']) ?>" placeholder="Judul">
                <?= form_error('title') ?>
            </div>
            <div class="form-group">
                <label>Photo</label>
                <input type="file" id="photo" name="photo" class="file-upload-default" data-max-size="10000000">
                <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" value="<?= $agenda['photo'] ?>"disabled placeholder="Upload file">
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-success btn-simple-upload" type="button">
                            Upload
                        </button>
                    </span>
                </div>
                <?= form_error('photo') ?>
            </div>
            <div class="form-group">
                <label for="content">Isi Agenda</label>
                <textarea class="form-control ckeditor" id="content" name="content"
                          placeholder="Enter content"><?= set_value('content', $agenda['content']) ?></textarea>
                <?= form_error('content') ?>
            </div>
            <div class="form-group">
                <label for="date">Tanggal</label>
                <input type="text" class="form-control datepicker" id="date" name="date" required
                        value="<?= set_value('date', $agenda['date']) ?>" placeholder="tanggal">
                <?= form_error('date') ?>
            </div>

            <div class="d-flex justify-content-between mt-3">
                <button onclick="history.back()" type="button" class="btn btn-light">Back</button>
                <button type="submit" class="btn btn-success mr-2" data-toggle="one-touch" data-touch-message="Saving...">Save Review</button>
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
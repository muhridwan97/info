<form action="<?= site_url('master/student/save') ?>" method="POST" enctype="multipart/form-data" id="form-student">
    <?= _csrf() ?>
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Create New Student</h5>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required maxlength="50"
                               value="<?= set_value('name') ?>" placeholder="Student name">
                        <?= form_error('name') ?>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="no_student">NO NIM</label>
                        <input type="text" class="form-control" id="no_student" name="no_student" required maxlength="100"
                               value="<?= set_value('no_student') ?>" placeholder="Student unique ID">
                        <?= form_error('no_student') ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="pembimbing">Pembimbing</label>
                <select class="custom-select select2" id="pembimbing" name="pembimbing" data-placeholder="Select Pembimbing">>
                    <option value=""></option>
                    <?php foreach ($pembimbings as $pembimbing): ?>
                        <option value="<?= $pembimbing['id'] ?>"<?= set_select('pembimbing', $pembimbing['id']) ?>>
                            <?= $pembimbing['name'] ?> (<?= $pembimbing['no_lecturer'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <?= form_error('pembimbing') ?>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="user">Related User Account</label>
                        <select class="custom-select select2" id="user" name="user" data-placeholder="Select User">
                            <option value=""></option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['id'] ?>"<?= set_select('user', $user['id']) ?>>
                                    <?= $user['name'] ?> (<?= $user['email'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('user') ?>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="custom-select" id="status" name="status" required>
                            <option value="">-- Status --</option>
                            <option value="ACTIVE"<?= set_select('status', 'ACTIVE') ?>>ACTIVE</option>
                            <option value="INACTIVE"<?= set_select('status', 'INACTIVE') ?>>INACTIVE</option>
                        </select>
                        <?= form_error('status') ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" maxlength="500"
                          placeholder="Enter student description"><?= set_value('description') ?></textarea>
                <?= form_error('description') ?>
            </div>
            <div class="d-flex justify-content-between mt-3">
                <button onclick="history.back()" type="button" class="btn btn-light">Back</button>
                <button type="submit" class="btn btn-success mr-2" data-toggle="one-touch" data-touch-message="Saving...">Save Student</button>
            </div>
        </div>
    </div>
</form>

<?php $this->load->view('partials/modals/_alert') ?>

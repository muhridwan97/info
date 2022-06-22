<form action="<?= site_url('master/employee/update/' . $employee['id']) ?>" method="POST" enctype="multipart/form-data" id="form-employee">
    <?= _csrf() ?>
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Edit Employee</h5>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required maxlength="50"
                               value="<?= set_value('name', $employee['name']) ?>" placeholder="Employee name">
                        <?= form_error('name') ?>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="no_employee">Employee ID</label>
                        <input type="text" class="form-control" id="no_employee" name="no_employee" required maxlength="20"
                               value="<?= set_value('no_employee', $employee['no_employee']) ?>" placeholder="Employee unique ID">
                        <?= form_error('no_employee') ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 col-md-3">
                    <div class="form-group">
                        <label for="department">Department</label>
                        <select class="custom-select" id="department" name="department" required>
                            <option value="">-- Department --</option>
                            <?php foreach ($departments as $department): ?>
                                <option value="<?= $department['id'] ?>"
                                    <?= set_select('department', $department['id'], $department['id'] == $employee['id_department']) ?>>
                                    <?= $department['department'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('department') ?>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="form-group">
                        <label for="parent_employee">Supervisor</label>
                        <select class="custom-select" id="parent_employee" name="parent_employee">
                            <option value="">-- Parent Employee --</option>
                            <?php foreach ($parentEmployees as $parentEmployee): ?>
                                <option value="<?= $parentEmployee['id'] ?>"
                                    <?= set_select('parent_employee', $parentEmployee['id'], $parentEmployee['id'] == $employee['id_employee']) ?>>
                                    <?= $parentEmployee['name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('parent_employee') ?>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="form-group">
                        <label for="position">Position</label>
                        <input type="text" class="form-control" id="position" name="position" required maxlength="50"
                               value="<?= set_value('position', $employee['position']) ?>" placeholder="Eg. Admin, Programmer">
                        <?= form_error('position') ?>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="form-group">
                        <label for="position_level">Position Level</label>
                        <select class="custom-select" id="position_level" name="position_level" required>
                            <option value="">-- Level --</option>
                            <option value="STAFF"<?= set_select('position_level', 'STAFF', $employee['position_level'] == 'STAFF') ?>>STAFF</option>
                            <option value="SUPERVISOR"<?= set_select('position_level', 'SUPERVISOR', $employee['position_level'] == 'SUPERVISOR') ?>>SUPERVISOR</option>
                            <option value="MANAGER"<?= set_select('position_level', 'MANAGER', $employee['position_level'] == 'MANAGER') ?>>MANAGER</option>
                            <option value="DIRECTOR"<?= set_select('position_level', 'DIRECTOR', $employee['position_level'] == 'DIRECTOR') ?>>DIRECTOR</option>
                        </select>
                        <?= form_error('position_level') ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="enter_date">Hire Date</label>
                        <input type="text" class="form-control datepicker" id="enter_date" name="enter_date" required maxlength="50" autocomplete="off"
                               value="<?= set_value('enter_date', format_date($employee['enter_date'], 'd F Y')) ?>" placeholder="Date employee is hired">
                        <?= form_error('enter_date') ?>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="quit_date">Quit Date</label>
                        <input type="text" class="form-control datepicker" id="quit_date" name="quit_date" maxlength="50" autocomplete="off"
                               value="<?= set_value('quit_date', format_date($employee['quit_date'], 'd F Y')) ?>" placeholder="Date employee resign">
                        <?= form_error('quit_date') ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 col-md-3">
                    <div class="form-group">
                        <label for="gender_any">Gender</label>
						<div>
							<div class="custom-control custom-radio custom-control-inline mt-1">
								<input type="radio" name="gender" id="gender_male" value="male" class="custom-control-input"
										<?= set_radio('gender', 'male', $employee['gender'] == 'male') ?>>
								<label class="custom-control-label" for="gender_male">Male</label>
							</div>
							<div class="custom-control custom-radio custom-control-inline mt-1">
								<input type="radio" name="gender" id="gender_female" value="female" class="custom-control-input"
										<?= set_radio('gender', 'male', $employee['gender'] == 'female') ?>>
								<label class="custom-control-label" for="gender_female">Female</label>
							</div>
						</div>
                        <?= form_error('gender') ?>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="custom-select" id="status" name="status" required>
                            <option value="">-- Status --</option>
                            <option value="ACTIVE"<?= set_select('status', 'ACTIVE', $employee['status'] == 'ACTIVE') ?>>ACTIVE</option>
                            <option value="INACTIVE"<?= set_select('status', 'INACTIVE', $employee['status'] == 'INACTIVE') ?>>INACTIVE</option>
                        </select>
                        <?= form_error('status') ?>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label>Photo</label>
                        <input type="file" id="photo" name="photo" class="file-upload-default" data-max-size="3000000">
                        <div class="input-group col-xs-12">
                            <input type="text" id="photo-title" class="form-control file-upload-info" value="<?= $employee['photo'] ?>" disabled placeholder="Upload photo">
                            <span class="input-group-append">
                                <button class="file-upload-browse btn btn-info btn-simple-upload" type="button">Upload</button>
                            </span>
                        </div>
                        <?= form_error('photo') ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="company">Company</label>
                        <select class="custom-select" id="company" name="company" required>
                            <option value="">-- Company --</option>
                            <option value="TRANSCON INDONESIA"<?= set_select('company', 'TRANSCON INDONESIA', $employee['company'] == 'TRANSCON INDONESIA') ?>>TRANSCON INDONESIA</option>
                            <option value="BLBJ"<?= set_select('company', 'BLBJ', $employee['company'] == 'BLBJ') ?>>BLBJ</option>
                            <option value="ISPM"<?= set_select('company', 'ISPM', $employee['company'] == 'ISPM') ?>>ISPM</option>
                            <option value="OTHER"<?= set_select('company', 'OTHER', $employee['company'] == 'OTHER') ?>>OTHER</option>
                        </select>
                        <?= form_error('company') ?>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="work_location">Work Location</label>
                        <input type="text" class="form-control" id="work_location" name="work_location" required maxlength="50"
                               value="<?= set_value('work_location', $employee['work_location']) ?>" placeholder="Work location">
                        <?= form_error('work_location') ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="tax_no">Tax No</label>
                        <input type="text" class="form-control" id="tax_no" name="tax_no" maxlength="50"
                               value="<?= set_value('tax_no', $employee['tax_no']) ?>" placeholder="Tax no">
                        <?= form_error('tax_no') ?>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="tax_address">Tax Address</label>
                        <input type="text" class="form-control" id="tax_address" name="tax_address" maxlength="50"
                               value="<?= set_value('tax_address', $employee['tax_address']) ?>" placeholder="Tax address">
                        <?= form_error('tax_address') ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="id_card_no">ID Card No</label>
                        <input type="text" class="form-control" id="id_card_no" name="id_card_no" maxlength="50"
                               value="<?= set_value('id_card_no', $employee['id_card_no']) ?>" placeholder="ID card no">
                        <?= form_error('id_card_no') ?>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="id_card_address">ID Card Address</label>
                        <input type="text" class="form-control" id="id_card_address" name="id_card_address" maxlength="50"
                               value="<?= set_value('id_card_address', $employee['id_card_address']) ?>" placeholder="ID card address">
                        <?= form_error('id_card_address') ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="social_security_no">Social Security No</label>
                        <input type="text" class="form-control" id="social_security_no" name="social_security_no" maxlength="50"
                               value="<?= set_value('social_security_no', $employee['social_security_no']) ?>" placeholder="Social security no">
                        <?= form_error('social_security_no') ?>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="health_insurance_no">Health Insurance No</label>
                        <input type="text" class="form-control" id="health_insurance_no" name="health_insurance_no" maxlength="50"
                               value="<?= set_value('health_insurance_no', $employee['health_insurance_no']) ?>" placeholder="Health insurance no">
                        <?= form_error('health_insurance_no') ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="place_of_birth">Place of Birth</label>
                        <input type="text" class="form-control" id="place_of_birth" name="place_of_birth" maxlength="50"
                               value="<?= set_value('place_of_birth', $employee['place_of_birth']) ?>" placeholder="Place of your birth">
                        <?= form_error('place_of_birth') ?>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="date_of_birth">Date of Birth</label>
                        <input type="text" class="form-control datepicker" id="date_of_birth" name="date_of_birth" maxlength="50" autocomplete="off"
                               value="<?= set_value('date_of_birth', format_date($employee['date_of_birth'], 'd F Y')) ?>" placeholder="Your birthday">
                        <?= form_error('date_of_birth') ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="contact_phone">Contact Phone</label>
                        <input type="tel" class="form-control datepicker" id="contact_phone" name="contact_phone" maxlength="50" autocomplete="off"
                               value="<?= set_value('contact_phone', $employee['contact_phone']) ?>" placeholder="Contact phone">
                        <?= form_error('contact_phone') ?>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="contact_mobile">Contact Mobile</label>
                        <input type="tel" class="form-control" id="contact_mobile" name="contact_mobile" maxlength="50"
                               value="<?= set_value('contact_mobile', $employee['contact_mobile']) ?>" placeholder="Contact mobile">
                        <?= form_error('contact_mobile') ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="user">Related User Account</label>
                <select class="custom-select" id="user" name="user">
                    <option value="">-- User --</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= $user['id'] ?>"<?= set_select('user', $user['id'], $user['id'] == $employee['id_user']) ?>>
                            <?= $user['name'] ?> (<?= $user['email'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <?= form_error('user') ?>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" maxlength="500"
                          placeholder="Enter employee description"><?= set_value('description', $employee['description']) ?></textarea>
                <?= form_error('description') ?>
            </div>
            <div class="d-flex justify-content-between mt-3">
                <button onclick="history.back()" type="button" class="btn btn-light">Back</button>
                <button type="submit" class="btn btn-primary" data-toggle="one-touch" data-touch-message="Updating...">Update Employee</button>
            </div>
        </div>
    </div>
</form>

<?php $this->load->view('partials/modals/_alert') ?>

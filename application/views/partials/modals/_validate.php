<div class="modal fade" id="modal-validate" tabindex="-1" role="dialog" aria-labelledby="modalValidate" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post">
                <?= _csrf() ?>
                <?= _method('put') ?>
				<input type="hidden" name="status" value="">
                <div class="modal-header">
                    <h6 class="modal-title" id="modalValidate">
                        Validate <span class="validate-title"></span>
                    </h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="lead mb-2">Are you sure want to <strong class="validate-label font-weight-medium"></strong> and proceed to the next step?</p>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea class="form-control" name="message" id="message" rows="3" placeholder="Validation message"></textarea>
                        <span class="form-text text-fade">This message may be included to the email <a href="#" class="validate-email"></a></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">CLOSE</button>
                    <button type="submit" class="btn btn-sm btn-danger" data-toggle="one-touch">
                        VALIDATE
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
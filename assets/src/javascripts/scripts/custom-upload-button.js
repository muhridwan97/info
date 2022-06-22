// check if upload file is correct
import showAlert from "../components/alert";

$(document).on('change', '.file-upload-default', function () {
    if (this.files && this.files[0]) {
        let maxFile = $(this).data('max-size');
        if (this.files[0].size > maxFile) {
            showAlert('File too large', 'Maximum file must be less than ' + (maxFile / 1000000) + 'MB');
        } else {
            $(this).closest('.form-group').find('.file-upload-info').val(this.files[0].name);

			// set preview file (image)
			if ($(this).data('target-preview')) {
				const targetPreview = $($(this).data('target-preview'));
				if (targetPreview) {
					const reader = new FileReader();
					reader.onload = function(e) {
						targetPreview.attr('src', e.target.result);
					}
					reader.readAsDataURL(this.files[0]);
				}
			}
        }
    }
});

$(document).on('click', '.btn-simple-upload', function () {
    $(this).closest('.form-group').find('[type=file]').click();
});

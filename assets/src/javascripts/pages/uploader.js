import variables from "../components/variables";
import showAlert from "../components/alert";

export default function () {
	const uploaderWrapper = $('.uploader-wrapper');
	const uploadRequests = {};

	uploaderWrapper.on('click', '#btn-pick-file', function () {
		const target = $(this).data('target');
		const uploadWrapper = $(this).closest('.uploader-wrapper');
		const uploadProgress = uploadWrapper.find('#upload-progress');
		const uploadLabelFile = uploadWrapper.find('.upload-label-file');
		const uploadedFileInput = uploadWrapper.find('#uploaded-file');
		const uploadFileInput = uploadWrapper.find('.upload-file-input');

		if ($(this).hasClass('abort-upload')) {
			if (uploadRequests.hasOwnProperty(target)) {
				// abort request and pop out from request list
				uploadRequests[target].abort();
				delete uploadRequests[target];

				// revert progress and button state, check if before is file uploaded already
				uploadFileInput.removeClass('loading');
				if (uploadedFileInput.val()) {
					uploadLabelFile.text(uploadedFileInput.val());
					$(this)
						.removeClass('btn-outline-primary')
						.removeClass('btn-danger')
						.removeClass('abort-upload')
						.addClass('btn-success')
						.text('CHANGE FILE');

					uploadProgress
						.css('width', '100%')
						.text('100%');
				} else {
					uploadLabelFile.text('Selected file: Unknown file');
					$(this)
						.removeClass('btn-success')
						.removeClass('btn-danger')
						.removeClass('abort-upload')
						.addClass('btn-outline-primary')
						.html('<i class="mdi mdi-upload mr-2"></i>PICK FILE');

					uploadProgress
						.css('width', '0%')
						.text('');
				}
			}
		} else {
			$('#' + target).click();
		}
	});

	/**
	 * Upload file immediately after user pick a file.
	 */
	uploaderWrapper.on('change', '.upload-file-input', function (e) {
		let inputFile = $(this);
		const uploadWrapper = $(this).closest('.uploader-wrapper');
		const btnPickFile = uploadWrapper.find('#btn-pick-file');
		const uploadProgress = uploadWrapper.find('#upload-progress');
		const uploadLabelFile = uploadWrapper.find('.upload-label-file');
		const uploadedFileInput = uploadWrapper.find('#uploaded-file');

		if (window.FileReader && window.Blob) {
			if (this.files && this.files[0]) {
				const anyFile = inputFile.prop('accept') === '*' || !inputFile.prop('accept');
				const allowedType = inputFile.prop('accept').split(',') || [];
				if (anyFile || allowedType.includes(this.files[0].type)) {
					inputFile.addClass('loading');
					uploadLabelFile.html('<i class="mdi mdi-loading mdi-spin mr-2"></i>Upload in progress...');
					btnPickFile
						.removeClass('btn-outline-primary')
						.addClass('btn-danger')
						.addClass('abort-upload')
						.html('<i class="mdi mdi-close mr-1"></i> CANCEL');
					uploadProgress
						.css('width', '0%')
						.text('0%');

					// collect data and prepare ajax post request
					let formData = new FormData();
					formData.append("csrf_token", variables.csrfToken);
					formData.append("file", this.files[0]);
					uploadRequests[inputFile.prop('id')] = $.ajax({
						type: "POST",
						url: variables.baseUrl + 'upload',
						data: formData,
						processData: false,
						contentType: false,
						xhr: function () { // get default xhr function to get listen progress status
							const myXhr = $.ajaxSettings.xhr();
							if (myXhr.upload) {
								myXhr.upload.addEventListener('progress', function (e) {
									if (e.lengthComputable) {
										// calculate percentage based on loaded file
										const max = e.total;
										const current = e.loaded;
										const percentage = ((current * 100) / max).toFixed(1);

										// put it back input and button class after upload completed
										if (percentage >= 100) {
											// percentage 100% doesn't mean server process is done,
											// if server forward the file to S3 or another external process,
											// we need to wait success callback below
											uploadLabelFile.html('<i class="mdi mdi-loading mdi-spin mr-2"></i>Forward uploaded file to external service S3...');
											//inputFile.removeClass('loading');
											//btnPickFile
											//	.removeClass('btn-outline-primary')
											//	.removeClass('btn-danger')
											//	.removeClass('abort-upload')
											//	.addClass('btn-success')
											//	.text('CHANGE FILE');
										}

										// update progress width and label
										uploadProgress
											.css('width', percentage + '%')
											.text(parseFloat(percentage) + '%');
									}
								}, false);
							}
							return myXhr;
						},
						success: function (data) {
							// put uploaded temp file into input hidden, so when we submit the form, server will know
							// which file must be moved from temp to proper folder
							if (data.status === 'success') {
								uploadLabelFile.text(data.result.client_name || data.result.file_name);
								uploadedFileInput.val(data.result.file_name);
							} else {
								showAlert('Error Result', data.message);
								btnPickFile.addClass('abort-upload').trigger('click');
							}
							// delete request and set button
							delete uploadRequests[btnPickFile.data('target')];
							inputFile.removeClass('loading');
							btnPickFile
								.removeClass('btn-outline-primary')
								.removeClass('btn-danger')
								.removeClass('abort-upload')
								.addClass('btn-success')
								.text('CHANGE FILE');
						},
						error: function (xhr, status, error) {
							if (xhr.statusText === 'abort') {
								return;
							}
							showAlert('Error Upload', (xhr.responseText || 'Error occur when upload file') + ' ' + status + ' ' + error);
						}
					});
				} else {
					showAlert("Warning", "File type is not supported");
				}
			} else {
				showAlert("Warning", "Please select an file");
			}
		} else {
			showAlert("Error API", "You're browser doesn't support the FileReader API");
		}
	});

	window.onbeforeunload = function () {
		if (uploaderWrapper.find('.upload-file-input.loading').length) {
			return confirm("Upload may in progress, are you sure want to leave?");
		}
	};

	uploaderWrapper.closest('form').on('submit', function () {
		if ($(this).valid()) {
			if (uploaderWrapper.find('.upload-file-input.loading').length || !(Object.keys(uploadRequests).length === 0 && uploadRequests.constructor === Object)) {
				showAlert('Information', "Upload in progress, abort or wait until it is done.");
				return false;
			} else {
				const uploadedFileInput = $('#uploaded-file');
				if (!uploadedFileInput.val() && uploadedFileInput.prop('required') === true) {
					showAlert("Warning", "File is required before submit your data");
					return false;
				}
				$(this).find('button[type=submit]').text('Saving...').prop('disabled', true);
				return true;
			}
		}
	});
};

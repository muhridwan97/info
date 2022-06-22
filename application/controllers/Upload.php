<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Upload
 * @property Uploader $uploader
 */
class Upload extends App_Controller
{
	/**
	 * Upload constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('modules/Uploader', 'uploader');
	}

	/**
	 * Upload the file.
	 */
	public function index()
	{
		/**
		 * Unlock session write, to allow next request received without waiting last request from same client,
		 * consider a user upload large file and forward to S3 taking to much times, and the user want to replace the new file
		 * remove this line if we need to modify session after this line
		 */
		session_write_close();

		if ($this->input->is_ajax_request() || $this->acceptJson()) {
			if (!empty($_FILES['file']['name'])) {
				$maxSize = 1000 * 200; // max size 200MB
				$uploadFile = $this->uploader->setDriver('s3')->uploadTo('file', ['size' => $maxSize]);
				if ($uploadFile) {
					$uploadedData = $this->uploader->getUploadedData();
					$this->renderJson([
						'status' => 'success',
						'result' => $uploadedData
					]);
				} else {
					$errors = $this->uploader->getErrors();
					$this->renderJson([
						'status' => 'error',
						'message' => empty($errors) ? 'Something went wrong' : reset($errors)
					]);
				}
			} else {
				$this->renderJson([
					'status' => 'error',
					'message' => 'No file uploaded'
				]);
			}
		}
	}
}

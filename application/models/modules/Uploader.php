<?php

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Uploader
 * @property S3FileManager $s3FileManager
 */
class Uploader extends CI_Model
{
    const DRIVER_LOCAL = 'local';
    const DRIVER_S3 = 's3';

    const WHITELISTED_TYPE = 'gif|jpg|jpeg|png|pdf|xls|xml|xlsx|doc|docx|ppt|pptx|txt|zip|rar|mp3|mp4|webm|ogv|mkv|mpeg';
    const UPLOAD_PATH = FCPATH . 'uploads' . DIRECTORY_SEPARATOR;
    const TEMP_PATH = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'temp';

    private $driver = 'local';
    private $uploadedData = [];
    private $errors = [];

    /**
     * Uploader constructor.
     */
    public function __construct()
    {
        $this->load->model('modules/S3FileManager', 's3FileManager');
    }

    /**
     * Change upload driver.
     *
     * @param $driver
     * @return Uploader
     */
    public function setDriver($driver)
    {
        if (in_array($driver, [static::DRIVER_LOCAL, static::DRIVER_S3])) {
            $this->driver = $driver;
        }
        return $this;
    }

	/**
	 * Check if service storage available.
	 * TODO: check service is online and ready to use.
	 *
	 * @return bool
	 */
	private function isS3Available()
	{
		return !empty(env('S3_ENDPOINT')) && !empty(env('S3_ACCESS_KEY_ID')) && !empty(env('S3_SECRET_ACCESS_KEY'));
	}

    /**
     * Upload file to S3.
     *
     * @param $input
     * @param array $options
     * @return bool
     */
    public function uploadToS3($input, $options = [])
    {
		if (!$this->isS3Available()) {
			return $this->uploadTo($input, $options);
		}
		$fileName = get_if_exist($options, 'file_name', (!is_array($_FILES[$input]['name']) ? uniqid() . '.' . strtolower(pathinfo($_FILES[$input]['name'], PATHINFO_EXTENSION)) : ''));
        $uploadDir = get_if_exist($options, 'destination', 'temp');
        $allowedFileType = get_if_exist($options, 'type', self::WHITELISTED_TYPE);
        $maxFileSize = get_if_exist($options, 'size', 3000);
        $bucket = get_if_exist($options, 'bucket', env('S3_BUCKET'));
        $key = get_if_exist($options, 'key', (!empty($uploadDir) ? (rtrim($uploadDir, '/') . '/') : '') . $fileName);
        $acl = get_if_exist($options, 'acl', 'public-read');;
        $copyLocal = get_if_exist($options, 'copy_local', false);;

        // prepare file type and result
        $fileSize = round($_FILES[$input]['size'] / 1024, 2); // convert to KB
        $fileType = if_empty(mime_content_type($_FILES[$input]['tmp_name']), $_FILES[$input]['type']);
        if (in_array($fileType, ['image/x-png'])) {
            $fileType = 'image/png';
        } elseif (in_array($fileType, ['image/jpg', 'image/jpe', 'image/jpeg', 'image/pjpeg'])) {
            $fileType = 'image/jpeg';
        }

        // check if file size allowed
        if ($fileSize > $maxFileSize) {
            $this->errors = "The file you are attempting to upload is larger than the permitted size {$maxFileSize}KB.";
            return false;
        }

        // check if file type allowed
        if (!in_array(pathinfo($fileName, PATHINFO_EXTENSION), is_array($allowedFileType) ? $allowedFileType : explode('|', $allowedFileType), true)) {
            $this->errors = "The filetype you are attempting to upload is not allowed.";
            return false;
        }

        // initialize S3 client
        $s3 = new S3Client([
            'version' => 'latest',
            'region' => env('S3_DEFAULT_REGION'),
            'credentials' => [
                'key' => env('S3_ACCESS_KEY_ID'),
                'secret' => env('S3_SECRET_ACCESS_KEY'),
            ],
            'endpoint' => env('S3_ENDPOINT'),
            'http' => [
                'verify' => false
            ]
        ]);

        try {
            // check if bucket exist, if not then create one
            // or shouldn't, because we might break the right location / let it throw and error instead
            // then comment block bellow
            if (!$s3->doesBucketExist($bucket)) {
                $result = $s3->createBucket([
                    'Bucket' => $bucket,
                ]);
                if (!$result) {
                    $this->errors = "Create bucket {$bucket} failed";
                    return false;
                }
            }

            // get access control list
            // $resp = $s3->getBucketAcl(['Bucket' => $bucket]);

            $uploadOptions = [
                'Bucket' => $bucket,
                'Key' => $key, // $fileName
                //'SourceFile' => $uploadedLocal, // either source file (upload to local directory first)
                //'Body' => fopen($_FILES[$input]['tmp_name'], 'rb'), // or stream data from client, pick one
                'ContentType' => $fileType,
                'ACL' => $acl
            ];

            // Upload data to local then the push into S3
            if ($copyLocal) {
                $uploadedLocal = $_FILES[$input]['tmp_name'];
                if ($this->uploadTo($input, $options)) {
                    $uploadedLocal = $this->getUploadedData()['full_path'];
                }
                $uploadOptions['SourceFile'] = $uploadedLocal;
            } else {
                $uploadOptions['Body'] = fopen($_FILES[$input]['tmp_name'], 'rb');
            }
            $upload = $s3->putObject($uploadOptions);

            // upload multipart for large files
            // $upload = $s3->upload($bucket, $key, fopen($_FILES[$input]['tmp_name'], 'rb', $acl));

            // get the URL to the object.
            $this->uploadedData = [
                'bucket' => $bucket,
                'acl' => $acl,
                'file_url' => env('S3_ENDPOINT') . $bucket . '/' . $key,
                'object_url' => $upload->get('ObjectURL'),
                'uploaded_path' => $key,
                // below is fallback key for codeigniter result
                'file_name' => $fileName,
                'file_type' => $fileType,
                'file_path' => dirname($key),
                'full_path' => $key,
                'raw_name' => pathinfo($fileName, PATHINFO_FILENAME),
                'orig_name' => $fileName,
                'client_name' => $_FILES[$input]['name'],
                'file_ext' => pathinfo($fileName, PATHINFO_EXTENSION),
                'file_size' => $fileSize,
                'is_image' => in_array($fileType, ['image/gif', 'image/jpeg', 'image/png'], TRUE),
                'image_width' => 0,
                'image_height' => 0,
                'image_type' => '',
                'image_size_str' => '',
            ];

            // $tmp_dir = ini_get('upload_tmp_dir') ? ini_get('upload_tmp_dir') : sys_get_temp_dir();
            if (FALSE !== ($D = getimagesize($_FILES[$input]['tmp_name']))) {
                $types = [1 => 'gif', 2 => 'jpeg', 3 => 'png'];
                $this->uploadedData['image_width'] = $D[0];
                $this->uploadedData['image_height'] = $D[1];
                $this->uploadedData['image_type'] = isset($types[$D[2]]) ? $types[$D[2]] : 'unknown';
                $this->uploadedData['image_size_str'] = $D[3]; // string containing height and width
            }
            return true;
        } catch (S3Exception $e) {
            $this->errors = $e->getMessage();
            return false;
        }
    }

    /**
     * Upload file to temporary (default).
     *
     * @param $input
     * @param array $options
     * @return bool
     */
    public function uploadTo($input, $options = [])
    {
		$driver = get_if_exist($options, 'driver', Uploader::DRIVER_LOCAL);
		if (($this->driver == 's3' || $driver == 's3') && $this->isS3Available()) {
			return $this->uploadToS3($input, $options);
		}

        $basePath = get_if_exist($options, 'base_path', Uploader::UPLOAD_PATH);
        $uploadDir = get_if_exist($options, 'destination', 'temp');

        $this->makeFolder($uploadDir, $basePath);

        $destination = $basePath . $uploadDir;
        $fileType = get_if_exist($options, 'type', self::WHITELISTED_TYPE);
        $fileSize = get_if_exist($options, 'size', 3000);
        $maxWidth = get_if_exist($options, 'max_width', 5000);
        $maxHeight = get_if_exist($options, 'max_height', 5000);
        $fileName = get_if_exist($options, 'file_name', (!is_array($_FILES[$input]['name']) ? uniqid() . '.' . strtolower(pathinfo($_FILES[$input]['name'], PATHINFO_EXTENSION)) : ''));

        $config['upload_path'] = $destination;
        $config['allowed_types'] = $fileType;
        $config['max_size'] = $fileSize;
        $config['max_width'] = $maxWidth;
        $config['max_height'] = $maxHeight;
        $config['file_ext_tolower'] = true;
        $config['file_name'] = $fileName;

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if (is_array($_FILES[$input]['name'])) {
            $errors = [];
            $data = [];
            $status = true;

            $totalUploads = count($_FILES[$input]['name']);
            for ($i = 0; $i < $totalUploads; $i++) {
                if (empty($_FILES[$input]['name'][$i])) {
                    $data[] = [];
                } else {
                    $_FILES[$input . '_multiple']['name'] = $_FILES[$input]['name'][$i];
                    $_FILES[$input . '_multiple']['type'] = $_FILES[$input]['type'][$i];
                    $_FILES[$input . '_multiple']['tmp_name'] = $_FILES[$input]['tmp_name'][$i];
                    $_FILES[$input . '_multiple']['error'] = $_FILES[$input]['error'][$i];
                    $_FILES[$input . '_multiple']['size'] = $_FILES[$input]['size'][$i];

                    if ($this->upload->do_upload($input . '_multiple')) {
                        $uploaded = $this->upload->data();
                        $uploaded['uploaded_path'] = $uploadDir . '/' . $uploaded['file_name'];
                        $data[] = $uploaded;
                    } else {
                        $errors[] = $this->upload->error_msg;
                        $status = false;
                        break;
                    }
                }
            }

            $this->errors = $errors;
            $this->uploadedData = $data;
            return $status;
        } else {
            $status = $this->upload->do_upload($input);

            if ($status) {
                $this->uploadedData = $this->upload->data();
                $this->uploadedData['uploaded_path'] = $uploadDir . '/' . $fileName;
            } else {
                $this->errors = $this->upload->error_msg;
            }

            return $status;
        }
    }

    /**
     * Move uploaded file.
     *
     * @param $key
     * @return string
	 */
    public function getUrl($key)
    {
        if($this->driver == 's3') {
            return $this->s3FileManager->getObjectUrl(env('S3_BUCKET'), $key);
        }
        return base_url('uploads/' . $key);
    }

    /**
     * Move uploaded file.
     *
     * @param $from
     * @param $to
     * @param string $base
     * @return bool
     */
    public function move($from, $to, $base = self::UPLOAD_PATH)
    {
		if ($this->driver == 's3' && $this->isS3Available()) {
            return $this->s3FileManager->moveObject(env('S3_BUCKET'), $from, $to);
        }

        $this->makeFolder(dirname($to));
        if (file_exists($base . $from) && is_writable($base)) {
            return rename($base . $from, $base . $to);
        }
        return false;
    }

    /**
     * Move uploaded file.
     *
     * @param $from
     * @param $to
     * @param string $base
     * @return bool
     */
    public function copy($from, $to, $base = self::UPLOAD_PATH)
    {
		if ($this->driver == 's3' && $this->isS3Available()) {
            return $this->s3FileManager->copyObject(env('S3_BUCKET'), $from, $to);
        }

        $this->makeFolder(dirname($to));
        if (file_exists($base . $from) && is_writable($base)) {
            return copy($base . $from, $base . $to);
        }
        return false;
    }

    /**
     * Delete given folder or file.
     *
     * @param $path
     * @param string $base
     * @return bool
     */
    public function delete($path, $base = self::UPLOAD_PATH)
    {
		if ($this->driver == 's3' && $this->isS3Available()) {
            return $this->s3FileManager->deleteObject(env('S3_BUCKET'), $path);
        }

        if (file_exists($base . $path) && is_writable($base . $path)) {
            if (is_dir($base . $path)) {
                $this->deleteRecursive($base . $path);
                return true;
            } else {
                return unlink($base . $path);
            }
        }
        return false;
    }

    /**
     * Delete folder and its content recursively.
     *
     * @param $dir
     * @return \Aws\Result|bool
     */
    private function deleteRecursive($dir)
    {
		if ($this->driver == 's3' && $this->isS3Available()) {
            return $this->s3FileManager->deleteObjects(env('S3_BUCKET'), $dir);
        }

        foreach (scandir($dir) as $file) {
            if ('.' === $file || '..' === $file) continue;
            if (is_dir("$dir/$file")) $this->deleteRecursive("$dir/$file");
            else unlink("$dir/$file");
        }
        return rmdir($dir);
    }

    /**
     * Create folder if it does not exist.
     *
     * @param $directory
     * @param string $base
     * @return bool
     */
    public function makeFolder($directory, $base = self::UPLOAD_PATH)
    {
		if ($this->driver == 's3' && $this->isS3Available()) {
            return $this->s3FileManager->putObject(env('S3_BUCKET'), rtrim($directory, '/') . '/');
        }

        if (!file_exists($base . $directory) && is_writable($base)) {
            return mkdir($base . $directory, 0777, true);
        }
        return false;
    }

    /**
     * Get folder size.
     *
     * @param $dir
     * @return int
     */
    public function folderSize($dir)
    {
        $size = 0;
        foreach (glob(rtrim($dir, '/') . '/*', GLOB_NOSORT) as $each) {
            $size += is_file($each) ? filesize($each) : $this->folderSize($each);
        }
        return $size;
    }

    /**
     * Get uploaded data info.
     *
     * @return array
     */
    public function getUploadedData()
    {
        return $this->uploadedData;
    }

    /**
     * Populate plain errors.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get formatted errors.
     *
     * @return string
     */
    public function getDisplayErrors()
    {
        if (isset($this->upload)) {
            return $this->upload->display_errors('<p class="mb-0">', '</p>');
        }
        if (!empty($this->errors)) {
            return '<p class="mb-0">' . (is_array($this->errors) ? $this->errors[0] : $this->errors) . '</p>';
        }
        return '';
    }

}

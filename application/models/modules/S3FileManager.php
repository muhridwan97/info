<?php

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;

defined('BASEPATH') OR exit('No direct script access allowed');

class S3FileManager
{
    private $s3;

    /**
     * FileManagerS3 constructor.
     *
     * @param S3Client|null $client
     */
    public function __construct(S3Client $client = null)
    {
		if (is_null($client) && !empty(env('S3_ENDPOINT'))) {
            $this->s3 = new S3Client([
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
        } else {
            $this->s3 = $client;
        }
    }

    /**
     * Get bucket list.
     *
     * @return mixed
     */
    public function getBuckets()
    {
        $buckets = $this->s3->listBuckets();

        return $buckets['Buckets'];
    }

    /**
     * Get bucket content (objects).
     *
     * @param $bucket
     * @return bool|mixed
     */
    public function getBucketObjects($bucket)
    {
        try {
            $result = $this->s3->listObjects([
                'Bucket' => $bucket,
            ]);
            return $result['Contents'];
        } catch (S3Exception $e) {
            log_message('error', $e->getMessage());
            return false;
        }
    }

    /**
     * Create new bucket.
     *
     * @param $bucketName
     * @return \Aws\Result|bool
     */
    public function createBucket($bucketName)
    {
        try {
            $result = $this->s3->createBucket([
                'Bucket' => $bucketName,
            ]);
            return $result;
        } catch (S3Exception $e) {
            log_message('error', $e->getMessage());
            return $e->getMessage();
        }
    }

    /**
     * Delete bucket.
     *
     * @param $bucket
     * @return \Aws\Result|bool
     */
    public function deleteBucket($bucket)
    {
        try {
            $result = $this->s3->deleteBucket([
                'Bucket' => $bucket,
            ]);
            return $result;
        } catch (S3Exception $e) {
            log_message('error', $e->getMessage());
            return false;
        }
    }

    /**
     * Get object from bucket.
     *
     * @param $bucket
     * @param $key
     * @return \Aws\Result
     */
    public function getObject($bucket, $key)
    {
        try {
            $result = $this->s3->getObject([
                'Bucket' => $bucket,
                'Key' => $key,
            ]);
            //header("Content-Type: {$result['ContentType']}");
            //echo $result['Body'];
            return $result;
        } catch (S3Exception $e) {
            log_message('error', $e->getMessage());
            return null;
        }
    }

    /**
     * Get object url from bucket.
     *
     * @param $bucket
     * @param $key
     * @return string
     */
    public function getObjectUrl($bucket, $key)
    {
        try {
            return $this->s3->getObjectUrl($bucket, $key);
        } catch (S3Exception $e) {
            log_message('error', $e->getMessage());
            return '';
        }
    }

    /**
     * Get presigned url from bucket.
     *
     * @param $bucket
     * @param $key
     * @param int $minutes
     * @return string
     */
    public function getObjectPresignedUrl($bucket, $key, $minutes = 20)
    {
        try {
            $cmd = $this->s3->getCommand('GetObject', [
                'Bucket' => $bucket,
                'Key' => $key
            ]);

            $request = $this->s3->createPresignedRequest($cmd, "+{$minutes} minutes");

            return (string)$request->getUri();
        } catch (S3Exception $e) {
            log_message('error', $e->getMessage());
            return '';
        }
    }

    /**
     * Upload file to storage.
     *
     * @param $bucket
     * @param $key
     * @param $file
     * @param $acl
     * @return \Aws\Result|bool
     */
    public function putObject($bucket, $key, $file = null, $acl = 'public-read')
    {
        try {
            $result = $this->s3->putObject([
                'Bucket' => $bucket,
                'Key' => $key,
                'SourceFile' => $file,
                'ACL' => $acl
            ]);
            return $result;
        } catch (S3Exception $e) {
            log_message('error', $e->getMessage());
            return false;
        }
    }

    /**
     * Upload content stream to storage.
     *
     * @param $bucket
     * @param $key
     * @param $body
     * @param $fileType
     * @param string $acl
     * @return \Aws\Result|bool
     */
    public function putObjectStream($bucket, $key, $body, $fileType = 'application/octet-stream', $acl = 'public-read')
    {
        try {
            $result = $this->s3->putObject([
                'Bucket' => $bucket,
                'Key' => $key,
                'Body' => $body,
                'ContentType' => $fileType,
                'ACL' => $acl
            ]);
            return $result;
        } catch (S3Exception $e) {
            log_message('error', $e->getMessage());
            return false;
        }
    }

    /**
     * Move object from-to bucket.
     *
     * @param $sourceBucket
     * @param $sourceKeyname
     * @param $targetKeyname
     * @param null $targetBucket
     * @return \Aws\Result|bool
     */
    public function moveObject($sourceBucket, $sourceKeyname, $targetKeyname, $targetBucket = null)
    {
        if(is_null($targetBucket)) {
            $targetBucket = $sourceBucket;
        }

        try {
            $result = $this->copyObject($sourceBucket, $sourceKeyname, $targetKeyname, $targetBucket);

            $this->s3->deleteObject([
                'Bucket' => $sourceBucket,
                'Key' => $sourceKeyname,
            ]);
            return $result;
        } catch (S3Exception $e) {
            log_message('error', $e->getMessage());
            return false;
        }
    }

    /**
     * Copy object from-to bucket.
     *
     * @param $sourceBucket
     * @param $sourceKeyname
     * @param $targetKeyname
     * @param null $targetBucket
     * @param string $acl
     * @return \Aws\Result|bool
     */
    public function copyObject($sourceBucket, $sourceKeyname, $targetKeyname, $targetBucket = null, $acl = 'public-read')
    {
        if(is_null($targetBucket)) {
            $targetBucket = $sourceBucket;
        }

        try {
            $result = $this->s3->copyObject([
                'Bucket' => $targetBucket,
                'Key' => $targetKeyname,
                'CopySource' => "{$sourceBucket}/{$sourceKeyname}",
                'ACL' => $acl
            ]);
            return $result;
        } catch (S3Exception $e) {
            log_message('error', $e->getMessage());
            return false;
        }
    }

    /**
     * Upload file to storage.
     *
     * @param $bucket
     * @param $key
     * @return \Aws\Result|bool
     */
    public function deleteObject($bucket, $key)
    {
        try {
            $result = $this->s3->deleteObject([
                'Bucket' => $bucket,
                'Key' => $key,
            ]);
            return $result;
        } catch (S3Exception $e) {
            log_message('error', $e->getMessage());
            return false;
        }
    }

    /**
     * Upload file to storage.
     *
     * @param $bucket
     * @param $keys
     * @return \Aws\Result|bool
     */
    public function deleteObjects($bucket, $keys)
    {
        try {
            $result = $this->s3->deleteObjects([
                'Bucket' => $bucket,
                'Delete' => [
                    'Objects' => $keys
                ],
            ]);
            return $result;
        } catch (S3Exception $e) {
            log_message('error', $e->getMessage());
            return false;
        }
    }

}

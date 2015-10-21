<?php

/*
 * This File is part of the Lucid\Adapter\Filesystem package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Adapter\Filesystem;

use Aws\S3\S3Client;
use Aws\S3\Enum\Group;
use Aws\S3\Enum\Permission;
use Aws\S3\Model\MultipartUpload\UploadBuilder;
use Aws\S3\Model\MultipartUpload\AbstractTransfer;
use Aws\Common\Exception\MultipartUploadException;

/**
 * @class AwsDriver
 *
 * @package Lucid\Adapter\Filesystem
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class AwsS3Driver implements DriverInterface
{
    private $client;
    private $bucket;
    private $uploadBuilder;

    /**
     * __construct
     *
     * @param S3Client $client
     * @param string $bucket
     * @param string $mount
     * @param array $options
     * @param UploadBuilder $ulBuilder
     *
     * @return void
     */
    public function __construct(S3Client $client, $bucket, $mount = null, array $options = [], UploadBuilder $ulBuilder = null)
    {
        $this->client = $client;
        $this->bucket = $bucket;
        $this->uploadBuilder = $ulBuilder;
        parent::__construct($prefix);
    }

    /**
     * {@inheritdoc}
     */
    public function exists($path)
    {
        return $this->client->doesObjectExist($this->bucket, $this->getPrefixed($path));
    }

    /**
     * {@inheritdoc}
     */
    public function isFile($path)
    {
        $info = $this->getPathInfo($path);

        return 'file' === $info['type'];
    }

    /**
     * {@inheritdoc}
     */
    public function isDir($path)
    {
        $info = $this->getPathInfo($path);

        return 'dir' === $info['type'];
    }

    /**
     * {@inheritdoc}
     */
    public function isLink($path)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getPathInfo($path)
    {
        $response = $this->client->headObject($this->getOptions($path));

        return $this->createPathInfo($this->parseResponse($response->all()));
    }

    /**
     * setUploadBuilder
     *
     * @param UploadBuilder $ulBuilder
     *
     * @return void
     */
    public function setUploadBuilder(UploadBuilder $ulBuilder)
    {
        $this->uploadBuilder = $ulBuilder;
    }

    /**
     * getUploadBuilder
     *
     * @return UploadBuilder
     */
    public function getUploadBuilder()
    {
        if (null === $this->uploadBuilder) {
            $this->uploadBuilder = UploadBuilder::newInstance();
        }

        return $this->uploadBuilder;
    }

    /**
     * parseResponse
     *
     * @param array $response
     * @param string $path
     *
     * @return array
     */
    protected function parseResponse(array $response, $path = null)
    {
        $res = ['path' => $path ?: $this->getUnprefixed($response['Key'])];

        if (0 === strcmp(substr($res['path'], -1), '/')) {
            $res['type'] = 'dir';
            $res['path'] = rtrim($res['path'], '/');

            return $res;
        }

        return array_merge($res, ['type' => 'file']);
    }

    protected function getOptions($path, array $options = [])
    {
        $opts = array_merge($options, ['Bucket' => $this->bucket, 'Key' => $this->getPrefixed($path)]);

        return $opts;
    }

    /**
     * mb2Bytes
     *
     * @param int $mb
     *
     * @return int
     */
    protected function mb2Bytes($mb)
    {
        return $mb * pow(1024);
    }
}

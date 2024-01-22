<?php

namespace UndObs;

use Directory;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Adapter\Polyfill\NotSupportingVisibilityTrait;
use League\Flysystem\Config;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\FileAttributes;

/**
 * Class ObsAdapter
 * @package Obs
 */
class ObsAdapter implements FilesystemAdapter
{
    /**
     * @var ObsClient
     */
    protected $client;

    /**
     * @var
     */
    protected $bucket;

    /**
     * @var
     */
    protected $endpoint;

    /**
     * @var
     */
    protected $cdnDomain;

    /**
     * @var
     */
    protected $ssl;

    /**
     * ObsAdapter constructor.
     * @param ObsClient $client
     * @param string $bucket
     * @param string $prefix
     */
    public function __construct(ObsClient $client, string $bucket, string $endpoint, string $cdnDomain, bool $ssl, string $prefix = '')
    {
        $this->client = $client;
        $this->bucket = $bucket;
        $this->endpoint = $endpoint;
        $this->cdnDomain = $cdnDomain;
        $this->ssl = $ssl;

        //$this->setPathPrefix($prefix);
    }

    /**
     * @return ObsClient
     */
    public function getClient(): ObsClient
    {
        return $this->client;
    }

    /**
     * @return string
     */
    public function getBucket(): string
    {
        return $this->bucket;
    }

    /**
     * @param string $path
     * @param string $contents
     * @param Config $config
     * @return array|bool|false
     */
    public function write($path, $contents, Config $config): void
    {
        //$path = $this->applyPathPrefix($path);

        try {
            $object = $this->client->putObject([
                'Bucket' => $this->getBucket(),
                'Key' => $path,
                'SourceFile' => $contents
            ]);
        } catch (ObsException $e) {
            //return false;
        }

        //return $this->normalizeResponse($object);
    }

    /**
     * @param string $path
     * @param resource $resource
     * @param Config $config
     * @return array|bool|false
     */
    public function writeStream(string $path, $contents, Config $config): void
    {
        $path = $this->applyPathPrefix($path);

        try {
            $object = $this->client->putObject([
                'Bucket' => $this->getBucket(),
                'Key' => $path,
                'Body' => $contents
            ]);
        } catch (ObsException $e) {
            //return false;
        }

        //return $this->normalizeResponse($object);
    }

    /**
     * @param string $path
     * @param string $contents
     * @param Config $config
     * @return array|bool|false
     */
    public function update($path, $contents, Config $config)
    {
        return $this->write($path, $contents, $config);
    }

    /**
     * @param string $path
     * @param resource $resource
     * @param Config $config
     * @return array|bool|false
     */
    public function updateStream($path, $resource, Config $config)
    {
        return $this->writeStream($path, $resource, $config);
    }

    /**
     * @param string $path
     * @param string $newpath
     * @return bool
     */
    public function rename($path, $newpath)
    {
        $config = new Config([]);
        if ($this->copy($path, $newpath, $config) && $this->delete($path)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $path
     * @param string $newpath
     * @return bool
     */
    public function copy(string $source, string $destination, Config $config): void
    {
        $path = $this->applyPathPrefix($source);
        $newpath = $this->applyPathPrefix($destination);

        try {
            $object = $this->client->deleteObject([
                'Bucket' => $this->getBucket(),
                'Key' => $newpath,
                'CopySource' => $this->getBucket() . '/' . $path
            ]);
        } catch (ObsException $e) {
            //return false;
        }

        //return true;
    }

    /**
     * @param string $path
     * @return bool
     */
    public function delete($path): void
    {
        $path = $this->applyPathPrefix($path);

        try {
            $object = $this->client->deleteObject([
                'Bucket' => $this->getBucket(),
                'Key' => $path
            ]);
        } catch (ObsException $e) {
            //return false;
        }

        //return true;
    }

    /**
     * @param string $dirname
     * @return bool
     */
    public function deleteDir($dirname)
    {
        return $this->delete($dirname);
    }

    /**
     * @param string $dirname
     * @param Config $config
     * @return array|bool|false
     */
    public function createDir($dirname, Config $config)
    {
        $path = $this->applyPathPrefix($dirname);

        try {
            $object = $this->client->putObject([
                'Bucket' => $this->getBucket(),
                'Key' => $path
            ]);
        } catch (ObsException $e) {
            return false;
        }

        return $this->normalizeResponse($object);
    }

    /**
     * @param string $path
     * @return array|bool|false|null
     */
    public function has($path)
    {
        return $this->getMetadata($path);
    }

    /**
     * @param string $path
     * @return array|bool|false
     */
    public function read($path): string
    {
        $path = $this->applyPathPrefix($path);

        try {
            $object = $this->client->getObject([
                'Bucket' => $this->getBucket(),
                'Key' => $path
            ]);
        } catch (ObsException $e) {
            return false;
        }

        $object['contents'] = (string)$object['Body'];
        unset($object['Body']);

        return $this->normalizeResponse($object);
    }

    /**
     * @param string $path
     * @return array|bool|false
     */
    public function readStream($path)
    {
        $path = $this->applyPathPrefix($path);

        try {
            $object = $this->client->getObject([
                'Bucket' => $this->getBucket(),
                'Key' => $path,
                'SaveAsStream' => true
            ]);
        } catch (ObsException $e) {
            return false;
        }

        $object['stream'] = $object['Body'];
        unset($object['Body']);

        return $this->normalizeResponse($object);
    }

    /**
     * @param string $directory
     * @param bool $recursive
     * @return array|bool
     */
    public function listContents($directory = '', $recursive = false): iterable
    {
        $path = $this->applyPathPrefix($directory);

        try {
            $object = $this->client->listObjects([
                'Bucket' => $this->getBucket(),
                'MaxKeys' => 1000,
                'Prefix' => $directory,
                'Marker' => null
            ]);
        } catch (ObsException $e) {
            return false;
        }

        $contents = $object["Contents"];

        if (!count($contents)) {
            return [];
        }

        return array_map(function ($entry) {
            $path = $this->removePathPrefix($entry['Key']);
            return $this->normalizeResponse($entry, $path);
        }, $contents);
    }

    /**
     * @param string $path
     * @return array|bool|false
     */
    public function getMetadata($path)
    {
        $path = $this->applyPathPrefix($path);

        try {
            $object = $this->client->getObjectMetadata([
                'Bucket' => $this->getBucket(),
                'Key' => $path
            ]);
        } catch (ObsException $e) {
            return false;
        }

        return $this->normalizeResponse($object);
    }

    /**
     * @param string $path
     * @return array|bool|false
     */
    public function getSize($path)
    {
        $object = $this->getMetadata($path);
        $object['size'] = $object['ContentLength'];

        return $object;
    }

    /**
     * @param string $path
     * @return array|bool|false
     */
    public function getMimetype($path)
    {
        $object = $this->getMetadata($path);
        $object['mimetype'] = $object['ContentType'];

        return $object;
    }

    /**
     * @param string $path
     * @return array|bool|false
     */
    public function getTimestamp($path)
    {
        $object = $this->getMetadata($path);

        return $object;
    }

    /**
     * @param $path
     * @return string
     */
    public function getUrl($path)
    {
        return ($this->ssl ? 'https://' : 'http://')
            . ($this->cdnDomain == '' ? $this->getBucket() . '.' . $this->endpoint : $this->cdnDomain)
            . '/' . ltrim($path, '/');
    }

    /**
     * @param $object
     * @return array
     */
    public function normalizeResponse($object): array
    {
        //$path = ltrim($this->removePathPrefix($object['Key']), '/');

        $result = ['path' => '/'];

        if (isset($object['LastModified'])) {
            $result['timestamp'] = strtotime($object['LastModified']);
        }

        if (isset($object['Size'])) {
            $result['size'] = $object['Size'];
            $result['bytes'] = $object['Size'];
        }

        $type = (substr($result['path'], -1) === '/' ? 'dir' : 'file');

        $result['type'] = $type;

        return $result;
    }

    public function fileExists(string $path): bool
    {
        return false;
    }

    public function directoryExists(string $path): bool
    {
        return false;
    }

    public function deleteDirectory($directory): void
    {
        //
    }

    public function createDirectory(string $path, Config $config): void
    {
        //
    }

    public function setVisibility(string $path, string $visibility): void
    {
        //
    }

    public function visibility(string $path): FileAttributes
    {
        return new FileAttributes(str_replace('\\', '/', $path));
    }

    public function mimeType(string $path): FileAttributes
    {
        return new FileAttributes(str_replace('\\', '/', $path));
    }

    public function lastModified(string $path): FileAttributes
    {
        return new FileAttributes(str_replace('\\', '/', $path));
    }

    public function fileSize(string $path): FileAttributes
    {
        return new FileAttributes(str_replace('\\', '/', $path));
    }

    public function move(string $source, string $destination, Config $config): void
    {
        //
    }

    public function exists($path)
    {
        //
    }

    public function get($path)
    {
        //
    }

    public function put($path, $contents, $options = [])
    {
        //dd(is_resource($contents), get_resource_type($contents), $this->client);
        try {
            $object = $this->client->putObject([
                'Bucket' => $this->getBucket(),
                'Key' => $path,
                'SourceFile' => $contents
            ]);
            return $this->normalizeResponse($object);
        } catch (ObsException $e) {
            //return false;
        }
    }

    public function getVisibility($path)
    {
        //
    }

    public function prepend($path, $data)
    {
        //
    }

    public function append($path, $data)
    {
        //
    }

    public function size($path)
    {
        //
    }

    public function files($directory = null, $recursive = false)
    {
        //
    }

    public function allFiles($directory = null)
    {
        //
    }

    public function directories($directory = null, $recursive = false)
    {
        //
    }

    public function allDirectories($directory = null)
    {
        //
    }

    public function makeDirectory($path)
    {
        //
    }

    public function url($path)
    {
        return $this->cdnDomain . '/' . $path;
    }

    public function path($path)
    {
        return $path;
    }
}

<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\StorageUpload;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\AwsS3V3\PortableVisibilityConverter;
use League\Flysystem\Filesystem;
use League\Flysystem\Visibility;

final class S3 implements StorageUploadInterface
{
    private Filesystem $filesystem;
    private S3Client $client;
    private AwsS3V3Adapter $adapter;
    private string $prefix;

    public function __construct(
        private string $bucket,
        string $prefix = '',
        array $clientOptions = [],
        private array $config = []
    ) {
        $this->prefix = (empty($prefix)) ? '' : rtrim($prefix, '/') . '/';
        $this->client = new S3Client($clientOptions);
        $this->adapter = new AwsS3V3Adapter(
            $this->client,
            $bucket,
            $this->prefix,
            new PortableVisibilityConverter(
                Visibility::PUBLIC // or ::PRIVATE
            )
        );

        $this->filesystem = new Filesystem($this->adapter, config: $this->config);
    }

    public function getFileSystem(): Filesystem
    {
        return $this->filesystem;
    }

    public function getUrl(string $path): string
    {
        return $this->client->getObjectUrl($this->bucket, $this->prefix . $path);
    }
}

<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\StorageUpload;

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;

final class Local implements StorageUploadInterface
{
    private string $rootDirectory;
    private string $publicUrl;

    public function __construct(
        string $rootDirectory,
        string $publicUrl,
        private array $permissionMap = [],
        private array $config = []
    ) {
        $this->rootDirectory = getenv('ROOT_PATH') . rtrim($rootDirectory, '/') . '/';
        $this->publicUrl = rtrim($publicUrl, '/') . '/';
    }

    public function getFileSystem(): Filesystem
    {
        return new Filesystem(
            new LocalFilesystemAdapter(
                $this->rootDirectory,
                PortableVisibilityConverter::fromArray(
                    $this->permissionMap
                ),
            ),
            config: $this->config
        );
    }

    public function getUrl(string $path): string
    {
        return $this->publicUrl . $path;
    }
}

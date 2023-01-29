<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\StorageUpload;

use League\Flysystem\Filesystem;

interface StorageUploadInterface
{
    public function getFileSystem(): Filesystem;

    public function getUrl(string $path): string;
}

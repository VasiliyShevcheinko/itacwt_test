<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Filesystem\Reader;

interface FileReaderInterface
{
    public function read(string $filePath): iterable;
}

<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Filesystem;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;

final class FileManager
{
    public const DATA_DIR = '/data/';
    public const DATA_FIXTURE_DIR = '/src/DataFixtures/data/';

    public function __construct(private readonly string $projectDir)
    {
    }

    public function makeFullDataFilePath(string $shotFileName): string
    {
        $localFile = $this->projectDir.self::DATA_DIR.$shotFileName;

        if (!file_exists($localFile)) {
            throw new FileNotFoundException(sprintf('Файл %s не найден', $localFile));
        }

        return $localFile;
    }

    public function makeFullDataFixtureFilePath(string $shotFileName): string
    {
        $localFile = $this->projectDir.self::DATA_FIXTURE_DIR.$shotFileName;

        if (!file_exists($localFile)) {
            throw new FileNotFoundException(sprintf('Файл %s не найден', $localFile));
        }

        return $localFile;
    }
}

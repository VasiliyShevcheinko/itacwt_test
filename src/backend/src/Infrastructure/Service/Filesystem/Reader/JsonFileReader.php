<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Filesystem\Reader;

final class JsonFileReader implements FileReaderInterface
{
    /**
     * @return array<string>
     */
    public function read(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException(sprintf('Файл %s не существует.', $filePath));
        }

        if (false === $fileContent = file_get_contents($filePath)) {
            throw new \RuntimeException(sprintf('Не удалось загрузить данные из файла %s.', $filePath));
        }

        try {
            $fileData = json_decode($fileContent, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new \RuntimeException(sprintf('Не удалось декодировать данные файла %s.', $filePath));
        }

        return $fileData;
    }
}

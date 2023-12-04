<?php

namespace App\DataFixtures\Fixtures;

use App\Entity\Country as CountryEntity;
use App\Infrastructure\Service\Filesystem\FileManager;
use App\Infrastructure\Service\Filesystem\Reader\FileReaderInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class Country extends Fixture
{
    public const DATA_FILE = 'country.json';

    public function __construct(
        private readonly FileManager $fileManager,
        private readonly FileReaderInterface $fileReader
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $filePath = $this->fileManager->makeFullDataFixtureFilePath(self::DATA_FILE);
        $countries = $this->fileReader->read($filePath);

        $persistCounter = 0;
        foreach ($countries as $country) {
            $newCountry = new CountryEntity($country['country'], $country['charCode']);
            $manager->persist($newCountry);
            ++$persistCounter;
            if ($persistCounter > 100) {
                $persistCounter = 0;
                $manager->flush();
                $manager->clear();
            }
        }

        $manager->flush();
    }
}

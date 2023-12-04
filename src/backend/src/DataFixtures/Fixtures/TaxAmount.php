<?php

namespace App\DataFixtures\Fixtures;

use App\Entity\TaxAmount as TaxAmountEntity;
use App\Infrastructure\Service\Filesystem\FileManager;
use App\Infrastructure\Service\Filesystem\Reader\FileReaderInterface;
use App\Repository\CountryRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TaxAmount extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public const DATA_FILE = 'tax_amount.json';

    public function __construct(
        private readonly FileManager $fileManager,
        private readonly FileReaderInterface $fileReader,
        private readonly CountryRepository $countryRepository,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $filePath = $this->fileManager->makeFullDataFixtureFilePath(self::DATA_FILE);
        $taxAmounts = $this->fileReader->read($filePath);

        $persistCounter = 0;
        foreach ($taxAmounts as $taxAmount) {
            $countryName = $taxAmount['country'];
            $countryEntity = $this->countryRepository->findOneBy(['name' => $countryName]);

            if (null === $countryEntity) {
                continue;
            }

            $newTaxAmount = new TaxAmountEntity(
                $countryEntity,
                $taxAmount['amount']
            );

            $manager->persist($newTaxAmount);
            ++$persistCounter;
            if ($persistCounter > 100) {
                $persistCounter = 0;
                $manager->flush();
                $manager->clear();
            }
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['tax_amount'];
    }

    public function getDependencies(): array
    {
        return [
            Country::class,
        ];
    }
}

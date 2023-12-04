<?php

namespace App\DataFixtures\Fixtures;

use App\Entity\Product as ProductEntity;
use App\Infrastructure\Service\Filesystem\FileManager;
use App\Infrastructure\Service\Filesystem\Reader\FileReaderInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class Product extends Fixture implements FixtureGroupInterface
{
    public const DATA_FILE = 'products.json';

    public function __construct(
        private readonly FileManager $fileManager,
        private readonly FileReaderInterface $fileReader
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $filePath = $this->fileManager->makeFullDataFixtureFilePath(self::DATA_FILE);
        $products = $this->fileReader->read($filePath);

        $persistCounter = 0;
        foreach ($products as $product) {
            $newProduct = new ProductEntity(
                $product['name'],
                $product['price'],
                $product['count']
            );
            $manager->persist($newProduct);
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
        return ['products'];
    }
}

<?php

namespace App\DataFixtures\Fixtures;

use App\Entity\Coupon as CouponEntity;
use App\Enum\CouponType;
use App\Infrastructure\Service\Filesystem\FileManager;
use App\Infrastructure\Service\Filesystem\Reader\FileReaderInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class Coupon extends Fixture implements FixtureGroupInterface
{
    public const DATA_FILE = 'coupons.json';

    public function __construct(
        private readonly FileManager $fileManager,
        private readonly FileReaderInterface $fileReader
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $filePath = $this->fileManager->makeFullDataFixtureFilePath(self::DATA_FILE);
        $coupons = $this->fileReader->read($filePath);

        $persistCounter = 0;
        foreach ($coupons as $coupon) {
            $newCoupon = new CouponEntity(
                $coupon['code'],
                CouponType::from($coupon['type']),
                $coupon['discount']
            );
            $manager->persist($newCoupon);
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
        return ['coupons'];
    }
}

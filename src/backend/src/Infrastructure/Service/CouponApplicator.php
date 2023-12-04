<?php

declare(strict_types=1);

namespace App\Infrastructure\Service;

use App\Enum\CouponType;
use App\Infrastructure\Exception\EntityNotFoundException;
use App\Repository\CouponRepository;

class CouponApplicator
{
    public function __construct(
        private readonly CouponRepository $couponRepository
    ) {
    }

    /**
     * @throws EntityNotFoundException
     */
    public function apply(string $couponCode, float $price): float
    {
        $coupon = $this->couponRepository->findByCodeOrFail($couponCode);

        return match ($coupon->getType()) {
            CouponType::fixed => $this->applyFixed($coupon->getDiscount(), $price),
            CouponType::percent => $this->applyPercent($coupon->getDiscount(), $price),
        };
    }

    private function applyFixed(float $discount, float $price): float
    {
        $result = $price - $discount;
        if ($price < $discount) {
            $result = 0;
        }

        return $result;
    }

    private function applyPercent(float $discount, float $price): float
    {
        return $price * (1 - $discount * 0.01);
    }
}

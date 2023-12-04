<?php

declare(strict_types=1);

namespace App\Infrastructure\Service;

use App\Infrastructure\Exception\EntityNotFoundException;
use App\Infrastructure\Service\Tax\CountryDefiner\CountryCodeExtractorInterface;
use App\Infrastructure\Service\Tax\TaxCalculator;

final class PriceCalculator
{
    public function __construct(
        private readonly CouponApplicator $couponApplicator,
        private readonly TaxCalculator $taxCalculator,
        private readonly CountryCodeExtractorInterface $countryCodeExtractor,
    ) {
    }

    /**
     * @throws EntityNotFoundException
     */
    public function calculate(float $price, string $taxNumber, ?string $couponCode): float
    {
        if (null !== $couponCode) {
            $price = $this->couponApplicator->apply($couponCode, $price);
        }

        $countryCode = $this->countryCodeExtractor->extract($taxNumber);

        return $this->taxCalculator->calculate($price, $countryCode);
    }
}

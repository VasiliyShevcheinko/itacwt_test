<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Tax;

use App\Infrastructure\Exception\EntityNotFoundException;
use App\Repository\CountryRepository;

final class TaxCalculator
{
    public function __construct(
        private CountryRepository $countryRepository,
    ) {
    }

    /**
     * @throws EntityNotFoundException
     */
    public function calculate(float $price, string $countryCode): float
    {
        $country = $this->countryRepository->findByCodeOrFail($countryCode);
        $taxAmount = $country->getTaxAmount()?->getAmount();

        return $price * (1 - $taxAmount * 0.01);
    }
}

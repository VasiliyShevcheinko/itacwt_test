<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Tax\FormatToRegConverter\ConvertStrategy;

use App\Enum\Country;

abstract class ConvertStrategyAbstract
{
    /**
     * @var array<Country>
     */
    protected array $supportCountries;

    abstract public function convert(string $taxNumberFormat): string;

    public function support(Country $country): bool
    {
        return in_array($country, $this->supportCountries, true);
    }
}

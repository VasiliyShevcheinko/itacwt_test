<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Tax\FormatToRegConverter\ConvertStrategy;

use App\Enum\Country;

final class ConvertStrategy1 extends ConvertStrategyAbstract
{
    /**
     * @var array<Country>
     */
    protected array $supportCountries = [
        Country::Germany,
        Country::Greece,
        Country::Italy,
    ];

    private const PATTERN = ['/X/'];
    private const REPLACEMENT = ['[0-9]'];

    public function convert(string $taxNumberFormat): string
    {
        return '/^'.preg_replace(self::PATTERN, self::REPLACEMENT, $taxNumberFormat).'$/i';
    }
}

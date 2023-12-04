<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Tax\FormatToRegConverter\ConvertStrategy;

use App\Enum\Country;

final class ConvertStrategy2 extends ConvertStrategyAbstract
{
    /**
     * @var array<Country>
     */
    protected array $supportCountries = [
        Country::France,
    ];
    private const PATTERN = ['/X/', '/Y/'];
    private const REPLACEMENT = ['[0-9]', '[A-Z]'];

    public function convert(string $taxNumberFormat): string
    {
        return '/^'.preg_replace(self::PATTERN, self::REPLACEMENT, $taxNumberFormat).'$/i';
    }
}

<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Tax\CountryDefiner;

use App\Infrastructure\Service\Tax\CountryDefiner\Exception\CountryCodeExtractException;

final class CountryCodeExtractor implements CountryCodeExtractorInterface
{
    /**
     * @throws CountryCodeExtractException
     */
    public function extract(string $taxNumber): string
    {
        $pattern = '/^(?<countryCode>[A-Z]{2})/i';

        if (1 !== preg_match($pattern, $taxNumber, $match)) {
            throw new CountryCodeExtractException();
        }

        return $match['countryCode'];
    }
}

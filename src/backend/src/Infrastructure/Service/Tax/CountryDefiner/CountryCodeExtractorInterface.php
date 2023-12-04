<?php

namespace App\Infrastructure\Service\Tax\CountryDefiner;

interface CountryCodeExtractorInterface
{
    public function extract(string $taxNumber): string;
}

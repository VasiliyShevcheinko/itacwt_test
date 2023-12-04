<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Tax\FormatToRegConverter;

use App\Enum\Country;
use App\Infrastructure\Service\Tax\FormatToRegConverter\ConvertStrategy\ConvertStrategyAbstract;
use App\Infrastructure\Service\Tax\FormatToRegConverter\Exception\ConvertException;

final class FormatToRegConverter
{
    /**
     * @param ConvertStrategyAbstract[] $handlers
     */
    public function __construct(private readonly iterable $handlers)
    {
    }

    /**
     * @throws ConvertException
     */
    public function convert(string $taxNumber, Country $country): string
    {
        foreach ($this->handlers as $handler) {
            if ($handler->support($country)) {
                return $handler->convert($taxNumber);
            }
        }

        throw new ConvertException(sprintf('Для страны %s нет конвертера', $country->name));
    }
}

<?php

declare(strict_types=1);

namespace App\ResponseModel;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
final class PriceModel
{
    public function __construct(public readonly float $price)
    {
    }
}

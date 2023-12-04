<?php

declare(strict_types=1);

namespace App\Infrastructure\Exception;

interface EnumLabel extends \UnitEnum
{
    /**
     * Name or short description.
     */
    public function label(): string;
}

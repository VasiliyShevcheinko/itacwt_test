<?php

declare(strict_types=1);

namespace App\Infrastructure\Validator\Constraints;

use App\Infrastructure\Validator\TaxNumberFormatValidator;
use Symfony\Component\Validator\Constraint;

class TaxNumberFormatConstraint extends Constraint
{
    public string $message = 'Неправильный формат налогового номера';
    public string $mode = '';

    public function validatedBy(): string
    {
        return TaxNumberFormatValidator::class;
    }
}

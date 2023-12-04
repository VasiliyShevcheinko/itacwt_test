<?php

declare(strict_types=1);

namespace App\Infrastructure\Validator\Constraints;

use App\Infrastructure\Validator\PaymentProcessorValidator;
use Symfony\Component\Validator\Constraint;

class PaymentProcessorConstraint extends Constraint
{
    public function validatedBy(): string
    {
        return PaymentProcessorValidator::class;
    }
}

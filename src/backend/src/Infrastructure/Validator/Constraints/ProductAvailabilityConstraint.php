<?php

declare(strict_types=1);

namespace App\Infrastructure\Validator\Constraints;

use App\Infrastructure\Validator\ProductAvailabilityValidator;
use Symfony\Component\Validator\Constraint;

class ProductAvailabilityConstraint extends Constraint
{
    public function validatedBy(): string
    {
        return ProductAvailabilityValidator::class;
    }
}

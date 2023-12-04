<?php

declare(strict_types=1);

namespace App\Infrastructure\Validator\Constraints;

use App\Infrastructure\Validator\ProductExistenceValidator;
use Symfony\Component\Validator\Constraint;

class ProductExistenceConstraint extends Constraint
{
    public function validatedBy(): string
    {
        return ProductExistenceValidator::class;
    }
}

<?php

declare(strict_types=1);

namespace App\Infrastructure\Validator\Constraints;

use App\Infrastructure\Validator\CouponCodeValidator;
use Symfony\Component\Validator\Constraint;

class CouponCodeConstraint extends Constraint
{
    public function validatedBy(): string
    {
        return CouponCodeValidator::class;
    }
}

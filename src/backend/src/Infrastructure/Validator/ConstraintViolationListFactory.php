<?php

declare(strict_types=1);

namespace App\Infrastructure\Validator;

use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

final class ConstraintViolationListFactory
{
    public static function createSimple(
        string $propertyPath,
        string $message,
        mixed $invalidValue = null,
    ): ConstraintViolationList {
        $violationList = new ConstraintViolationList();
        $violationList->add(new ConstraintViolation(
            $message,
            '',
            [],
            null,
            $propertyPath,
            $invalidValue,
        ));

        return $violationList;
    }
}

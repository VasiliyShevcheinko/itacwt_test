<?php

declare(strict_types=1);

namespace App\Infrastructure\Validator;

use App\Infrastructure\Exception\ValidationException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ValidationWithException
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param Constraint[]|null $constraints
     * @param array<string>     $groups
     *
     * @throws ValidationException
     */
    public function process(mixed $value, array $constraints = null, array $groups = []): void
    {
        $violationList = $this->validator->validate($value, $constraints, $groups);

        if ($violationList->count() > 0) {
            throw new ValidationException($violationList);
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Infrastructure\Response\Model;

use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Validator\ConstraintViolationInterface;

#[Immutable]
final class ErrorsFieldModelResponse
{
    public function __construct(
        public string $field,
        public string $message,
    ) {
    }

    public static function fromViolation(ConstraintViolationInterface $violation): self
    {
        return new self(
            $violation->getPropertyPath() ?: '_root_',
            (string) $violation->getMessage(),
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Infrastructure\Response\Model;

use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Validator\ConstraintViolationListInterface;

#[Immutable]
final class ErrorsModelResponse
{
    public function __construct(
        public string $type,
        public string $message,
        /**
         * @var ErrorsFieldModelResponse[]
         */
        public array $errors,
    ) {
    }

    public static function fromViolationList(
        string $type,
        string $message,
        ConstraintViolationListInterface $violationList,
    ): self {
        $errors = [];
        foreach ($violationList as $violation) {
            $errors[] = ErrorsFieldModelResponse::fromViolation($violation);
        }

        return new self($type, $message, $errors);
    }
}

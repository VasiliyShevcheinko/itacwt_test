<?php

declare(strict_types=1);

namespace App\Infrastructure\Exception;

use App\Infrastructure\Validator\ConstraintViolationListFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class ValidationException extends ApiException implements ValidationExceptionInterface
{
    private ConstraintViolationListInterface $errors;

    public function __construct(ConstraintViolationListInterface $errors, string $message = '', \Throwable $previous = null)
    {
        $this->errors = $errors;
        parent::__construct($message, ExceptionType::validationFields, $previous);
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function getErrors(): ConstraintViolationListInterface
    {
        return $this->errors;
    }

    public function getShowMessage(): string
    {
        return 'Ошибка валидации данных';
    }

    public static function oneField(string $propertyPath, string $message): self
    {
        return new self(
            ConstraintViolationListFactory::createSimple(
                $propertyPath,
                $message,
            ),
        );
    }
}

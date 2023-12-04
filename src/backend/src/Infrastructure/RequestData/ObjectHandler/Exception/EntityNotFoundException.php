<?php

declare(strict_types=1);

namespace App\Infrastructure\RequestData\ObjectHandler\Exception;

use Omasn\ObjectHandler\Exception\ObjectHandlerException;
use Omasn\ObjectHandler\HandleProperty;

final class EntityNotFoundException extends ObjectHandlerException
{
    public function __construct(
        HandleProperty $property,
        int $code = 0,
        \Throwable $previous = null,
    ) {
        parent::__construct($property, 'Entity not found', $code, $previous);
    }
}

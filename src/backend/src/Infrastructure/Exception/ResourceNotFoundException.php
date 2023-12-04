<?php

declare(strict_types=1);

namespace App\Infrastructure\Exception;

use Symfony\Component\HttpFoundation\Response;

final class ResourceNotFoundException extends ApiException
{
    public function __construct(string $message = '', \Throwable $previous = null)
    {
        parent::__construct($message, ExceptionType::notFound, $previous);
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}

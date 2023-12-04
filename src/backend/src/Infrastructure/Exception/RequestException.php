<?php

declare(strict_types=1);

namespace App\Infrastructure\Exception;

use Symfony\Component\HttpFoundation\Response;

final class RequestException extends ApiException
{
    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public static function parametersIncorrect(\Throwable $e = null): self
    {
        return new self('Параметры запроса переданы не корректно', null, $e);
    }
}

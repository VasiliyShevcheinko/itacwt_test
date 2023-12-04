<?php

declare(strict_types=1);

namespace App\Infrastructure\Exception;

use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

abstract class ApiException extends \Exception implements HttpExceptionInterface, ShowMessageInterface
{
    private ?ExceptionType $type;

    public function __construct(string $message = '', ExceptionType $type = null, \Throwable $previous = null)
    {
        $this->type = $type;
        parent::__construct($message, $this->getStatusCode(), $previous);
    }

    #[ArrayShape(['Content-Type' => 'string'])]
    public function getHeaders(): array
    {
        return ['Content-Type' => 'application/problem+json'];
    }

    public function getType(): ?ExceptionType
    {
        return $this->type;
    }

    public function getShowMessage(): string
    {
        return $this->message;
    }
}

<?php

declare(strict_types=1);

namespace App\Infrastructure\Exception;

use Symfony\Component\HttpFoundation\Response;

final class EntityNotFoundException extends ApiException
{
    private string $entityClass;

    public function __construct(string $entityClass, string $message = '', \Throwable $previous = null)
    {
        if (!class_exists($entityClass)) {
            throw new \InvalidArgumentException('Can\'t  initiate Entity not found exception: '.$entityClass.' is not a class name.');
        }
        $this->entityClass = $entityClass;
        parent::__construct($message, ExceptionType::badRequest, $previous);
    }

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function getShowMessage(): string
    {
        return sprintf('Объект сущности %s не найден', $this->getEntityClass());
    }
}

<?php

declare(strict_types=1);

namespace App\Infrastructure\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

interface ValidationExceptionInterface
{
    public function getErrors(): ConstraintViolationListInterface;
}

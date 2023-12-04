<?php

declare(strict_types=1);

namespace App\Infrastructure\Response\Model;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
final class ErrorModelResponse
{
    public function __construct(
        public string $type,
        public string $message,
    ) {
    }
}

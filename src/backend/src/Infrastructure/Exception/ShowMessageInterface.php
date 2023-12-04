<?php

declare(strict_types=1);

namespace App\Infrastructure\Exception;

interface ShowMessageInterface extends \Throwable
{
    public function getShowMessage(): string;
}

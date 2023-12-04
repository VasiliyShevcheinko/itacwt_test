<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Payment\PaymentStrategy;

use App\Infrastructure\Service\Payment\Exception\ServicePaymentError;

abstract class PaymentAbstract
{
    protected string $supportPaymentService;

    /**
     * @throws ServicePaymentError
     */
    abstract public function pay(int|float $price): void;

    public function support(string $paymentServiceName): bool
    {
        return $this->supportPaymentService === $paymentServiceName;
    }
}

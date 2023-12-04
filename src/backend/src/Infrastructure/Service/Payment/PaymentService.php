<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Payment;

use App\Infrastructure\Service\Payment\Exception\ServicePaymentNotFound;
use App\Infrastructure\Service\Payment\PaymentStrategy\PaymentAbstract;

final class PaymentService
{
    /**
     * @param PaymentAbstract[] $handlers
     */
    public function __construct(private readonly iterable $handlers)
    {
    }

    /**
     * @throws ServicePaymentNotFound|Exception\ServicePaymentError
     */
    public function pay(float|int $price, string $serviceName): void
    {
        foreach ($this->handlers as $handler) {
            if ($handler->support($serviceName)) {
                $handler->pay($price);

                return;
            }
        }
        throw new ServicePaymentNotFound();
    }

    public function support(string $serviceName): bool
    {
        foreach ($this->handlers as $handler) {
            if ($handler->support($serviceName)) {
                return true;
            }
        }

        return false;
    }
}

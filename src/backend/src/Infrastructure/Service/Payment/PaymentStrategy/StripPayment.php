<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Payment\PaymentStrategy;

use App\Infrastructure\Service\Payment\Exception\ServicePaymentError;
use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;

class StripPayment extends PaymentAbstract
{
    protected string $supportPaymentService = 'strip';

    /**
     * @throws ServicePaymentError
     */
    public function pay(float|int $price): void
    {
        $paymentProcessor = new StripePaymentProcessor();
        if (!$paymentProcessor->processPayment((float) $price)) {
            throw new ServicePaymentError();
        }
    }
}

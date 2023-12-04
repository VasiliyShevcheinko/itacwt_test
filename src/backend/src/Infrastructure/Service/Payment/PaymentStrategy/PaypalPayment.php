<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Payment\PaymentStrategy;

use App\Infrastructure\Service\Payment\Exception\ServicePaymentError;
use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;

class PaypalPayment extends PaymentAbstract
{
    protected string $supportPaymentService = 'paypal';

    /**
     * @throws ServicePaymentError
     */
    public function pay(float|int $price): void
    {
        $paymentProcessor = new PaypalPaymentProcessor();

        $numberOfCents = (int) ($price * 100);
        try {
            $paymentProcessor->pay($numberOfCents);
        } catch (\Exception $e) {
            throw new ServicePaymentError();
        }
    }
}

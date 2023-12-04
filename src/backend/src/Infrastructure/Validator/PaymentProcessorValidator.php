<?php

declare(strict_types=1);

namespace App\Infrastructure\Validator;

use App\Infrastructure\Service\Payment\PaymentService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PaymentProcessorValidator extends ConstraintValidator
{
    public function __construct(
        private PaymentService $paymentService
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$this->paymentService->support($value)) {
            $this->context->buildViolation(sprintf('Сервиса оплаты %s нет', $value))
                ->addViolation();
        }
    }
}

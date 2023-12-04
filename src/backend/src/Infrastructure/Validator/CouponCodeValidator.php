<?php

declare(strict_types=1);

namespace App\Infrastructure\Validator;

use App\Repository\CouponRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CouponCodeValidator extends ConstraintValidator
{
    public function __construct(
        private CouponRepository $couponRepository
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (null === $value) {
            return;
        }

        $coupon = $this->couponRepository->findOneBy(['code' => $value]);

        if (null === $coupon) {
            $this->context->buildViolation(sprintf('Купон %s не найден', $value))
                ->addViolation();

            return;
        }

        if (!$coupon->isActive()) {
            $this->context->buildViolation(sprintf('Купон %s не активен', $value))
                ->addViolation();
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Infrastructure\Validator;

use App\Repository\ProductRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ProductAvailabilityValidator extends ConstraintValidator
{
    public function __construct(private readonly ProductRepository $productRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        $product = $this->productRepository->find($value);
        if (null === $product) {
            return;
        }
        if (0 === $product->getCount()) {
            $this->context->buildViolation(sprintf('Продукта с идентификатором %s нет в наличии', $value))
                ->addViolation();
        }
    }
}

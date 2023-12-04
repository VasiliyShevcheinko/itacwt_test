<?php

declare(strict_types=1);

namespace App\Infrastructure\Validator;

use App\Repository\ProductRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ProductExistenceValidator extends ConstraintValidator
{
    public function __construct(private readonly ProductRepository $productRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (null === $this->productRepository->find($value)) {
            $this->context->buildViolation(sprintf('Продукта с идентификатором %s нет', $value))
                ->addViolation();
        }
    }
}

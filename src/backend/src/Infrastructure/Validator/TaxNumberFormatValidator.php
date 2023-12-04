<?php

declare(strict_types=1);

namespace App\Infrastructure\Validator;

use App\Infrastructure\Service\Tax\CountryDefiner\CountryCodeExtractorInterface;
use App\Infrastructure\Service\Tax\CountryDefiner\Exception\CountryCodeExtractException;
use App\Repository\CountryRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TaxNumberFormatValidator extends ConstraintValidator
{
    public function __construct(
        private readonly CountryCodeExtractorInterface $countryCodeExtractor,
        private CountryRepository $countryRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        try {
            $countryCode = $this->countryCodeExtractor->extract($value);
        } catch (CountryCodeExtractException $e) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();

            return;
        }

        $countryEntity = $this->countryRepository->findOneBy(['charCode' => $countryCode]);
        if (null === $countryEntity) {
            $this->context->buildViolation(sprintf('Страны с кодом %s нет', $countryCode))
                ->addViolation();

            return;
        }

        $taxNumberFormat = $countryEntity->getTaxNumberFormat();

        $pattern = $taxNumberFormat?->getPattern();

        if ('' === $pattern || null === $pattern) {
            throw new \InvalidArgumentException('Pattern is empty');
        }

        if (1 !== preg_match($pattern, $value)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}

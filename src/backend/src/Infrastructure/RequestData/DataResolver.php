<?php

declare(strict_types=1);

namespace App\Infrastructure\RequestData;

use App\Infrastructure\DataTransfer\DataTransferInterface;
use App\Infrastructure\Exception\RequestException;
use App\Infrastructure\Exception\ValidationException;
use App\Infrastructure\Validator\ValidationWithException;
use Omasn\ObjectHandler\Exception\HandlerException;
use Omasn\ObjectHandler\Exception\RequireArgumentException;
use Omasn\ObjectHandler\Exception\ViolationListException;
use Omasn\ObjectHandler\HandleContext;
use Omasn\ObjectHandler\HandleProperty;
use Omasn\ObjectHandler\ObjectHandlerInterface;
use Omasn\ObjectHandler\ViolationFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class DataResolver
{
    public function __construct(
        private readonly ObjectHandlerInterface $handler,
        private readonly ValidatorInterface $validator,
        private readonly ValidationWithException $exceptionValidator,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * @param class-string $class
     *
     * @throws ValidationException
     * @throws RequestException
     * @throws \ReflectionException
     */
    public function createData(string $class, array $handleData, string $method): DataTransferInterface
    {
        if (!is_subclass_of($class, DataTransferInterface::class)) {
            throw new \LogicException(sprintf('Factory data not implement %s', DataTransferInterface::class));
        }

        if (Request::METHOD_PUT === $method || Request::METHOD_PATCH === $method) {
            $reflClass = new \ReflectionClass($class);
            foreach ($reflClass->getProperties() as $property) {
                if ($property->hasDefaultValue()) {
                    throw new \RuntimeException(sprintf('For methods PUT and PATCH default values of the "%s::%s" not allowed', $reflClass->getName(), $property->getName()));
                }
            }
        }

        $violationList = new ConstraintViolationList();
        $context = $this->createValidationContext($method, $violationList);

        try {
            $object = $this->handler->handle($class, $handleData, $context);
        } catch (HandlerException|ViolationListException|RequireArgumentException $e) {
            $this->throwIfViolationsCount($violationList);
            if ($e instanceof ViolationListException || $e instanceof RequireArgumentException) {
                $vList = new ConstraintViolationList();

                /** @var ConstraintViolation $violation */
                foreach ($e->getViolationList() as $violation) {
                    $vList->add(new ConstraintViolation(
                        $this->translator->trans((string) $violation->getMessage(), domain: 'validators'),
                        $violation->getMessageTemplate(),
                        $violation->getParameters(),
                        $violation->getRoot(),
                        $violation->getPropertyPath(),
                        $violation->getInvalidValue(),
                    ));
                }
                $this->throwIfViolationsCount($vList);
            }

            throw RequestException::parametersIncorrect($e);
        }

        $this->throwIfViolationsCount($violationList);

        $this->exceptionValidator->process($object, null, ['after_handle']);

        return $object;
    }

    /**
     * @throws ValidationException
     */
    private function throwIfViolationsCount(ConstraintViolationListInterface $violationList): void
    {
        if ($violationList->count() > 0) {
            throw new ValidationException($violationList);
        }
    }

    private function createValidationContext(
        string $method,
        ConstraintViolationList $violationList,
    ): HandleContext {
        return new HandleContext(
            Request::METHOD_PATCH === $method,
            function (HandleProperty $handleProperty, ?string $class) use ($violationList) {
                if (null === $class) {
                    return true;
                }

                $list = $this->validator->validatePropertyValue(
                    $class,
                    $handleProperty->getPropertyPath(),
                    $handleProperty->getInitialValue(),
                );

                if ($list->count() > 0) {
                    $violFactory = new ViolationFactory();
                    foreach ($list as $constraint) {
                        $violationList->add($violFactory->fromViolationParent(
                            $constraint,
                            $handleProperty->getPropertyPath(),
                        ));
                    }

                    return false;
                }

                return true;
            },
        );
    }
}

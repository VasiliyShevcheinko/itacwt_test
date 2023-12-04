<?php

declare(strict_types=1);

namespace App\Infrastructure\RequestData;

use App\Infrastructure\DataTransfer\DataTransferInterface;
use App\Infrastructure\Exception\RequestException;
use App\Infrastructure\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class RequestDataResolver implements ArgumentValueResolverInterface
{
    private DataResolver $dataResolver;

    public function __construct(DataResolver $dataResolver)
    {
        $this->dataResolver = $dataResolver;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        if (null === $classString = $argument->getType()) {
            return false;
        }

        return class_exists($classString)
            && is_subclass_of($classString, DataTransferInterface::class);
    }

    /**
     * Creating new instance of custom request DTO.
     *
     * @throws RequestException
     * @throws ValidationException
     * @throws \ReflectionException
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable|\Generator
    {
        $type = $argument->getType();
        if (null === $type || !class_exists($type)) {
            throw new \RuntimeException('Incorrect argument type');
        }

        yield $this->dataResolver->createData(
            $type,
            $this->requestToHandleData($request),
            $request->getMethod(),
        );
    }

    /**
     * @throws RequestException
     */
    public function requestToHandleData(Request $request): array
    {
        return array_merge_recursive(
            $request->query->all(),
            $this->getBodyData($request),
            $request->files->all(),
            $request->attributes->get('_route_params', []),
        );
    }

    /**
     * @throws RequestException
     */
    protected function getBodyData(Request $request): array
    {
        if (str_contains((string) $request->headers->get('Content-Type'), 'application/json')) {
            if ('' === $content = $request->getContent()) {
                return [];
            }

            try {
                /* @psalm-suppress PossiblyInvalidArgument */
                return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                throw RequestException::parametersIncorrect();
            }
        }

        return $request->request->all();
    }
}

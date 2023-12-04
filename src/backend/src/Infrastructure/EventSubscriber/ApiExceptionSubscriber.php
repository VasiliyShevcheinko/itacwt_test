<?php

declare(strict_types=1);

namespace App\Infrastructure\EventSubscriber;

use App\Infrastructure\Exception\ApiException;
use App\Infrastructure\Exception\EntityNotFoundException;
use App\Infrastructure\Exception\ExceptionType;
use App\Infrastructure\Exception\ResourceNotFoundException;
use App\Infrastructure\Exception\ShowMessageInterface;
use App\Infrastructure\Exception\ValidationExceptionInterface;
use App\Infrastructure\Response\Model\ErrorModelResponse;
use App\Infrastructure\Response\Model\ErrorsModelResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class ApiExceptionSubscriber implements EventSubscriberInterface
{
    private const INTERNAL_CODE = Response::HTTP_INTERNAL_SERVER_ERROR;

    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    /**
     * @throws \Throwable
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $e = $event->getThrowable();

        // define
        $statusCode = $this->getStatusCodeFromThrowable($e);
        $errorType = $e instanceof ApiException ? $e->getType() : null;
        if (null === $errorType) {
            $errorType = $this->getTypeForStatusCode($statusCode);
        }
        $type = $errorType->value;
        $message = $e instanceof ShowMessageInterface ? $e->getShowMessage() : $errorType->label();

        // create model
        if ($e instanceof ValidationExceptionInterface) {
            $errorModel = ErrorsModelResponse::fromViolationList(
                $type,
                $message,
                $e->getErrors(),
            );
        } else {
            $errorModel = new ErrorModelResponse(
                $type,
                $message,
            );
        }

        $responseBody = (array) $errorModel;
        if (null !== $previous = $e->getPrevious()) {
            $responseBody['previous'] = (string) $previous;
        }
        // create response
        $response = new JsonResponse($responseBody);
        $response->headers->set('Content-Type', 'application/problem+json');

        // add headers
        if ($e instanceof HttpExceptionInterface) {
            foreach ($e->getHeaders() as $key => $header) {
                $response->headers->set($key, $header);
            }
        }

        // log
        $this->logKernelException($event);

        $response->setStatusCode($statusCode);
        $event->setResponse($response);
    }

    public function logKernelException(ExceptionEvent $event): void
    {
        $e = FlattenException::createFromThrowable($event->getThrowable());

        $this->logException($event->getThrowable(), sprintf(
            'Uncaught PHP Exception %s: "%s" at %s line %s',
            $e->getClass(),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
        ));
    }

    /**
     * Logs an exception.
     */
    protected function logException(\Throwable $exception, string $message): void
    {
        if (!$exception instanceof HttpExceptionInterface) {
            $this->logger->critical($message, ['exception' => $exception]);
        } else {
            $this->logger->error($message, ['exception' => $exception]);
        }
    }

    private function getStatusCodeFromThrowable(\Throwable $e): int
    {
        if ($e instanceof EntityNotFoundException || $e instanceof ResourceNotFoundException) {
            return Response::HTTP_NOT_FOUND;
        }

        if ($e instanceof HttpExceptionInterface) {
            return $e->getStatusCode();
        }

        return self::INTERNAL_CODE;
    }

    private function getTypeForStatusCode(int $statusCode): ExceptionType
    {
        return match ($statusCode) {
            Response::HTTP_NOT_FOUND => ExceptionType::notFound,
            Response::HTTP_BAD_REQUEST => ExceptionType::badRequest,
            Response::HTTP_FORBIDDEN => ExceptionType::accessDenied,
            Response::HTTP_METHOD_NOT_ALLOWED => ExceptionType::methodNotAllowed,
            Response::HTTP_TOO_MANY_REQUESTS => ExceptionType::tooManyRequests,
            Response::HTTP_UNAUTHORIZED => ExceptionType::unauthorized,
            default => ExceptionType::unknown,
        };
    }
}

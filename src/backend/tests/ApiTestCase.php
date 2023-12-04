<?php

declare(strict_types=1);

namespace App\Tests;

use App\Infrastructure\Exception\ExceptionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class ApiTestCase extends WebTestCase
{
    /**
     * @deprecated replace this base assert in test case
     */
    protected static function assertBasicallyResponseStatus(KernelBrowser $client): void
    {
        self::assertNotContains(
            $client->getResponse()->getStatusCode(),
            [
                Response::HTTP_NOT_FOUND,
                Response::HTTP_FORBIDDEN,
                Response::HTTP_METHOD_NOT_ALLOWED,
                Response::HTTP_INTERNAL_SERVER_ERROR,
            ]
        );
    }

    protected static function assertResponseHasData(array $responseContentDecoded): void
    {
        static::assertArrayHasKey('data', $responseContentDecoded);
        static::assertIsArray($responseContentDecoded['data']);
    }

    /**
     * Asserts that there was a bad request error.
     */
    protected static function assertIsBadRequest(KernelBrowser $client): void
    {
        $response = self::extractResponseDecodedContent($client);

        self::assertSame(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
        self::assertSame($response['type'], ExceptionType::badRequest->value);
        self::assertNotEmpty($response['message']);
    }

    protected static function extractResponseContent(KernelBrowser $client): string
    {
        $response = $client->getResponse();
        if ($response instanceof JsonResponse) {
            $response->setEncodingOptions(JSON_UNESCAPED_UNICODE);
        }

        return (string) $response->getContent();
    }

    /**
     * @return array{data: array, errors: array}
     */
    protected static function extractResponseDecodedContent(KernelBrowser $client): array
    {
        try {
            return json_decode(self::extractResponseContent($client), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new \RuntimeException('Bad JSON response content');
        }
    }

    protected static function em(): EntityManagerInterface
    {
        return self::getContainer()->get('doctrine')->getManager();
    }

    protected static function persistFlush(object $entity): void
    {
        $em = self::em();
        $em->persist($entity);
        $em->flush();
    }
}

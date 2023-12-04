<?php

namespace Controller;

use App\Entity\Coupon;
use App\Tests\ApiTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;

class PriceControllerTest extends ApiTestCase
{
    protected function setUp(): void
    {
        $application = new Application(self::bootKernel());
        $application->setAutoExit(false);
        self::ensureKernelShutdown();

        parent::setUp();
    }

    /**
     * @dataProvider successData()
     */
    public function testPurchaseSuccess(
        int $productId,
        string $taxNumber,
        string $paymentProcessor,
        string $couponCode = null,
    ): void {
        $client = self::createClient();
        $client->jsonRequest('POST', '/calculate-price', [
            'product' => $productId,
            'taxNumber' => $taxNumber,
            'couponCode' => $couponCode,
        ]);

        self::assertResponseStatusCodeSame(200);
    }

    /**
     * @dataProvider badRequestData()
     */
    public function testPurchaseBadRequest(
        int $productId,
        string $taxNumber,
        string $errorMessage,
        string $couponCode = null,
    ): void {
        $client = self::createClient();
        $client->jsonRequest('POST', '/purchase', [
            'product' => $productId,
            'taxNumber' => $taxNumber,
            'couponCode' => $couponCode,
        ]);

        $content = self::extractResponseContent($client);
        self::assertResponseStatusCodeSame(400);
        $this->assertJsonStringEqualsJsonString($errorMessage, $content);
    }

    public static function successData(): array
    {
        $coupon = self::em()->getRepository(Coupon::class)->find(1);

        return [
            [
                'productId' => 1,
                'taxNumber' => 'IT12345678910',
                'couponCode' => $coupon->getCode(),
            ],
            [
                'productId' => 1,
                'taxNumber' => 'DE123456789',
                'couponCode' => $coupon->getCode(),
            ],
            [
                'productId' => 1,
                'taxNumber' => 'DE123456789',
                'paymentProcessor' => 'strip',
            ],
        ];
    }

    public static function badRequestData(): array
    {
        $coupon = self::em()->getRepository(Coupon::class)->find(1);
        $taxNumber = 'DE123456789';
        $paymentProcessors = ['paypal', 'strip'];

        $errorMessage = '{
            "type":"validation_fields","message":"Ошибка валидации данных",
            "errors":[{
            "field":"%s",
            "message":"%s"
            }]
        }';

        return [
            [
                'productId' => 1000,
                'taxNumber' => $taxNumber,
                'errorMessage' => sprintf($errorMessage, 'product', 'Продукта с идентификатором 1000 нет'),
                'couponCode' => $coupon->getCode(),
            ],
            [
                'productId' => 1000,
                'taxNumber' => $taxNumber,
                'errorMessage' => sprintf($errorMessage, 'product', 'Продукта с идентификатором 1000 нет'),
                'couponCode' => $coupon->getCode(),
            ],
            [
                'productId' => 1,
                'taxNumber' => '!!!',
                'errorMessage' => sprintf($errorMessage, 'taxNumber', 'Неправильный формат налогового номера'),
                'couponCode' => $coupon->getCode(),
            ],
            [
                'productId' => 1,
                'taxNumber' => $taxNumber,
                'errorMessage' => sprintf($errorMessage, 'couponCode', 'Купон !!! не найден'),
                'couponCode' => '!!!',
            ],
        ];
    }
}

<?php

namespace Controller;

use App\Entity\Coupon;
use App\Tests\ApiTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;

class PurchaseControllerTest extends ApiTestCase
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
        $client->jsonRequest('POST', '/purchase', [
            'product' => $productId,
            'taxNumber' => $taxNumber,
            'couponCode' => $couponCode,
            'paymentProcessor' => $paymentProcessor,
        ]);

        self::assertResponseStatusCodeSame(200);
    }

    /**
     * @dataProvider badRequestData()
     */
    public function testPurchaseBadRequest(
        int $productId,
        string $taxNumber,
        string $paymentProcessor,
        string $errorMessage,
        string $couponCode = null,
    ): void {
        $client = self::createClient();
        $client->jsonRequest('POST', '/purchase', [
            'product' => $productId,
            'taxNumber' => $taxNumber,
            'couponCode' => $couponCode,
            'paymentProcessor' => $paymentProcessor,
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
                'paymentProcessor' => 'paypal',
                'couponCode' => $coupon->getCode(),
            ],
            [
                'productId' => 1,
                'taxNumber' => 'DE123456789',
                'paymentProcessor' => 'strip',
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
                'paymentProcessor' => $paymentProcessors[0],
                'errorMessage' => sprintf($errorMessage, 'product', 'Продукта с идентификатором 1000 нет'),
                'couponCode' => $coupon->getCode(),
            ],
            [
                'productId' => 1,
                'taxNumber' => '!!!',
                'paymentProcessor' => $paymentProcessors[0],
                'errorMessage' => sprintf($errorMessage, 'taxNumber', 'Неправильный формат налогового номера'),
                'couponCode' => $coupon->getCode(),
            ],
            [
                'productId' => 1,
                'taxNumber' => $taxNumber,
                'paymentProcessor' => '!!!',
                'errorMessage' => sprintf($errorMessage, 'paymentProcessor', 'Сервиса оплаты !!! нет'),
                'couponCode' => $coupon->getCode(),
            ],
            [
                'productId' => 1,
                'taxNumber' => $taxNumber,
                'paymentProcessor' => $paymentProcessors[0],
                'errorMessage' => sprintf($errorMessage, 'couponCode', 'Купон !!! не найден'),
                'couponCode' => '!!!',
            ],
        ];
    }
}

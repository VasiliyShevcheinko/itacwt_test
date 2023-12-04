<?php

declare(strict_types=1);

namespace App\UseCase;

use App\Infrastructure\Exception\EntityNotFoundException;
use App\Infrastructure\Exception\PaymentException;
use App\Infrastructure\Exception\RuntimeException;
use App\Infrastructure\Service\Payment\Exception\ServicePaymentError;
use App\Infrastructure\Service\Payment\Exception\ServicePaymentNotFound;
use App\Infrastructure\Service\Payment\PaymentService;
use App\Infrastructure\Service\PriceCalculator;
use App\Repository\ProductRepository;
use App\RequestData\PurchaseData;
use Doctrine\ORM\EntityManagerInterface;

final class Purchase
{
    public function __construct(
        protected readonly PaymentService $paymentService,
        private readonly ProductRepository $productRepository,
        private readonly PriceCalculator $priceCalculator,
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @throws RuntimeException
     * @throws PaymentException
     */
    public function process(PurchaseData $data): void
    {
        try {
            $product = $this->productRepository->findOrFail($data->product);
            $price = $this->priceCalculator->calculate($product->getPrice(), $data->taxNumber, $data->couponCode);
        } catch (EntityNotFoundException $e) {
            throw new RuntimeException('Ошибка разработчика');
        }

        try {
            $this->paymentService->pay($price, $data->paymentProcessor);
        } catch (ServicePaymentError $e) {
            throw new PaymentException('Ошибка оплаты');
        } catch (ServicePaymentNotFound $e) {
            throw new RuntimeException('Ошибка разработчика');
        }

        $product->setCount($product->getCount() - 1);
        $this->em->flush();
    }
}

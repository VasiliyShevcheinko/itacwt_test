<?php

declare(strict_types=1);

namespace App\UseCase;

use App\Infrastructure\Exception\EntityNotFoundException;
use App\Infrastructure\Exception\RuntimeException;
use App\Infrastructure\Service\PriceCalculator;
use App\Repository\ProductRepository;
use App\RequestData\CalculatePriceData;
use App\ResponseModel\PriceModel;

final class CalculatePrice
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly PriceCalculator $priceCalculator,
    ) {
    }

    /**
     * @throws RuntimeException
     */
    public function process(CalculatePriceData $data): PriceModel
    {
        try {
            $product = $this->productRepository->findOrFail($data->product);
            $price = $this->priceCalculator->calculate($product->getPrice(), $data->taxNumber, $data->couponCode);
        } catch (EntityNotFoundException $e) {
            throw new RuntimeException('Ошибка разработчика');
        }

        return new PriceModel($price);
    }
}

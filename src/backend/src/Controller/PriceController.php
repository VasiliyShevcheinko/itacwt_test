<?php

namespace App\Controller;

use App\Infrastructure\Exception\RuntimeException;
use App\RequestData\CalculatePriceData;
use App\UseCase\CalculatePrice;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/calculate-price', name: 'calculate_price', methods: [Request::METHOD_POST])]
class PriceController extends AbstractController
{
    /**
     * @throws RuntimeException
     */
    public function __invoke(CalculatePriceData $data, CalculatePrice $useCase): JsonResponse
    {
        $response = $useCase->process($data);

        return $this->json($response);
    }
}

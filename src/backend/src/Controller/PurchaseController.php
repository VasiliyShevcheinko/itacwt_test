<?php

namespace App\Controller;

use App\Infrastructure\Exception\PaymentException;
use App\Infrastructure\Exception\RuntimeException;
use App\RequestData\PurchaseData;
use App\UseCase\Purchase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/purchase', name: 'purchase', methods: [Request::METHOD_POST])]
class PurchaseController extends AbstractController
{
    /**
     * @throws RuntimeException
     * @throws PaymentException
     */
    public function __invoke(PurchaseData $data, Purchase $useCase): Response
    {
        $useCase->process($data);

        return new Response();
    }
}

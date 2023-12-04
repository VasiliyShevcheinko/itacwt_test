<?php

declare(strict_types=1);

namespace App\RequestData;

use App\Infrastructure\DataTransfer\DataTransferInterface;

/**
 * @psalm-immutable
 */
final class PurchaseData implements DataTransferInterface
{
    public function __construct(
        public int $product,
        public string $taxNumber,
        public ?string $couponCode,
        public string $paymentProcessor,
    ) {
    }
}

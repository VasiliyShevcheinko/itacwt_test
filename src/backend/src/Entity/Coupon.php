<?php

namespace App\Entity;

use App\Enum\CouponType;
use App\Repository\CouponRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CouponRepository::class)]
class Coupon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column(type: Types::STRING, length: 32, unique: true)]
    private string $code;
    #[ORM\Column(type: Types::STRING, enumType: CouponType::class)]
    private CouponType $type;
    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    private bool $active;
    #[ORM\Column(type: Types::FLOAT)]
    private float $discount;

    public function __construct(string $code, CouponType $type, float $discount, bool $active = true)
    {
        $this->code = $code;
        $this->type = $type;
        $this->active = $active;
        $this->discount = $discount;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getType(): CouponType
    {
        return $this->type;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getDiscount(): float
    {
        return $this->discount;
    }

    public function setDiscount(float $discount): void
    {
        $this->discount = $discount;
    }
}

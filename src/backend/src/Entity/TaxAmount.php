<?php

namespace App\Entity;

use App\Repository\TaxAmountRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaxAmountRepository::class)]
class TaxAmount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'taxAmount', targetEntity: Country::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Country $country;

    #[ORM\Column(type: Types::FLOAT)]
    private float $amount;

    public function __construct(Country $country, float $amount)
    {
        $this->country = $country;
        $this->amount = $amount;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCountry(): Country
    {
        return $this->country;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }
}

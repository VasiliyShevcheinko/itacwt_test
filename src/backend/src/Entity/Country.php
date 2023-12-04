<?php

namespace App\Entity;

use App\Repository\CountryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CountryRepository::class)]
class Country
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 64, unique: true)]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 32, unique: true)]
    private string $charCode;

    #[ORM\OneToOne(mappedBy: 'country', targetEntity: TaxNumberFormat::class)]
    private ?TaxNumberFormat $taxNumberFormat = null;

    #[ORM\OneToOne(mappedBy: 'country', targetEntity: TaxAmount::class)]
    private ?TaxAmount $taxAmount = null;

    public function __construct(string $name, string $charCode)
    {
        $this->name = $name;
        $this->charCode = $charCode;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCharCode(): string
    {
        return $this->charCode;
    }

    public function getTaxNumberFormat(): TaxNumberFormat|null
    {
        return $this->taxNumberFormat;
    }

    public function getTaxAmount(): TaxAmount|null
    {
        return $this->taxAmount;
    }
}

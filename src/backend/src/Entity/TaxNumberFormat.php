<?php

namespace App\Entity;

use App\Repository\TaxNumberFormatRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TaxNumberFormatRepository::class)]
class TaxNumberFormat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'taxNumberFormat', targetEntity: Country::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Country $country;

    #[Assert\NotBlank]
    #[ORM\Column(type: Types::STRING, length: 64, unique: true)]
    private string $format;

    #[ORM\Column(type: Types::STRING, length: 64)]
    private string $pattern;

    #[ORM\Column(type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeImmutable $createAt;

    public function __construct(string $format, string $pattern, Country $country)
    {
        $this->format = $format;
        $this->pattern = $pattern;
        $this->country = $country;
        $this->createAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCountry(): Country
    {
        return $this->country;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function setFormat(string $format): void
    {
        $this->format = $format;
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }

    public function setPattern(string $pattern): void
    {
        $this->pattern = $pattern;
    }

    public function getCreateAt(): ?\DateTimeImmutable
    {
        return $this->createAt;
    }
}

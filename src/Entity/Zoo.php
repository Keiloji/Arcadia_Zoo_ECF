<?php

namespace App\Entity;

use App\Repository\ZooRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ZooRepository::class)]
class Zoo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 32)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $amOpeningTime = [];

    #[ORM\Column(type: Types::ARRAY)]
    private array $pmOpeningTime = [];

    #[ORM\Column]
    private ?int $maxGuest = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $CreatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getAmOpeningTime(): array
    {
        return $this->amOpeningTime;
    }

    public function setAmOpeningTime(array $amOpeningTime): static
    {
        $this->amOpeningTime = $amOpeningTime;

        return $this;
    }

    public function getPmOpeningTime(): array
    {
        return $this->pmOpeningTime;
    }

    public function setPmOpeningTime(array $pmOpeningTime): static
    {
        $this->pmOpeningTime = $pmOpeningTime;

        return $this;
    }

    public function getMaxGuest(): ?int
    {
        return $this->maxGuest;
    }

    public function setMaxGuest(int $maxGuest): static
    {
        $this->maxGuest = $maxGuest;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->CreatedAt;
    }

    public function setCreatedAt(\DateTimeImmutable $CreatedAt): static
    {
        $this->CreatedAt = $CreatedAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
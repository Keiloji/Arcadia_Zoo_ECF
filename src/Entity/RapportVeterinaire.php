<?php

namespace App\Entity;

use App\Repository\RapportVeterinaireRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RapportVeterinaireRepository::class)]
class RapportVeterinaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $rapportVeterinaireId = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 50)]
    private ?string $detail = null;

    #[ORM\ManyToOne(inversedBy: 'rapportVeterinaire')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $utilisateur = null; // Changement de Utilisateur à User

    #[ORM\ManyToOne(inversedBy: 'rapportVeterinaire')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Animal $animal = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRapportVeterinaireId(): ?int
    {
        return $this->rapportVeterinaireId;
    }

    public function setRapportVeterinaireId(int $rapportVeterinaireId): static
    {
        $this->rapportVeterinaireId = $rapportVeterinaireId;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getDetail(): ?string
    {
        return $this->detail;
    }

    public function setDetail(string $detail): static
    {
        $this->detail = $detail;

        return $this;
    }

    public function getUtilisateur(): ?User 
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?User $utilisateur): static 
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getAnimal(): ?Animal
    {
        return $this->animal;
    }

    public function setAnimal(?Animal $animal): static
    {
        $this->animal = $animal;

        return $this;
    }
}

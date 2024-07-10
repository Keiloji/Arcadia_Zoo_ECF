<?php

namespace App\Entity;

use App\Repository\AnimalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnimalRepository::class)]
class Animal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $animalId = null;

    #[ORM\Column(length: 50)]
    private ?string $prenom = null;

    #[ORM\Column(length: 50)]
    private ?string $etat = null;

    /**
     * @var Collection<int, rapportVeterinaire>
     */
    #[ORM\OneToMany(targetEntity: rapportVeterinaire::class, mappedBy: 'animal')]
    private Collection $rapportVeterinaire;

    #[ORM\ManyToOne(inversedBy: 'animal')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Race $race = null;

    #[ORM\ManyToOne(inversedBy: 'animal')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Habitat $habitat = null;

    public function __construct()
    {
        $this->rapportVeterinaire = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnimalId(): ?int
    {
        return $this->animalId;
    }

    public function setAnimalId(int $animalId): static
    {
        $this->animalId = $animalId;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * @return Collection<int, rapportVeterinaire>
     */
    public function getRapportVeterinaire(): Collection
    {
        return $this->rapportVeterinaire;
    }

    public function addRapportVeterinaire(rapportVeterinaire $rapportVeterinaire): static
    {
        if (!$this->rapportVeterinaire->contains($rapportVeterinaire)) {
            $this->rapportVeterinaire->add($rapportVeterinaire);
            $rapportVeterinaire->setAnimal($this);
        }

        return $this;
    }

    public function removeRapportVeterinaire(rapportVeterinaire $rapportVeterinaire): static
    {
        if ($this->rapportVeterinaire->removeElement($rapportVeterinaire)) {
            // set the owning side to null (unless already changed)
            if ($rapportVeterinaire->getAnimal() === $this) {
                $rapportVeterinaire->setAnimal(null);
            }
        }

        return $this;
    }

    public function getRace(): ?Race
    {
        return $this->race;
    }

    public function setRace(?Race $race): static
    {
        $this->race = $race;

        return $this;
    }

    public function getHabitat(): ?Habitat
    {
        return $this->habitat;
    }

    public function setHabitat(?Habitat $habitat): static
    {
        $this->habitat = $habitat;

        return $this;
    }
}

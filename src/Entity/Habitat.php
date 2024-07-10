<?php

namespace App\Entity;

use App\Repository\HabitatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HabitatRepository::class)]
class Habitat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $habitatId = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 50)]
    private ?string $commentaireHabitat = null;

    /**
     * @var Collection<int, animal>
     */
    #[ORM\OneToMany(targetEntity: animal::class, mappedBy: 'habitat', orphanRemoval: true)]
    private Collection $animal;

    /**
     * @var Collection<int, image>
     */
    #[ORM\ManyToMany(targetEntity: image::class, inversedBy: 'habitats')]
    private Collection $image;

    public function __construct()
    {
        $this->animal = new ArrayCollection();
        $this->image = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHabitatId(): ?int
    {
        return $this->habitatId;
    }

    public function setHabitatId(int $habitatId): static
    {
        $this->habitatId = $habitatId;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCommentaireHabitat(): ?string
    {
        return $this->commentaireHabitat;
    }

    public function setCommentaireHabitat(string $commentaireHabitat): static
    {
        $this->commentaireHabitat = $commentaireHabitat;

        return $this;
    }

    /**
     * @return Collection<int, animal>
     */
    public function getAnimal(): Collection
    {
        return $this->animal;
    }

    public function addAnimal(animal $animal): static
    {
        if (!$this->animal->contains($animal)) {
            $this->animal->add($animal);
            $animal->setHabitat($this);
        }

        return $this;
    }

    public function removeAnimal(animal $animal): static
    {
        if ($this->animal->removeElement($animal)) {
            // set the owning side to null (unless already changed)
            if ($animal->getHabitat() === $this) {
                $animal->setHabitat(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, image>
     */
    public function getImage(): Collection
    {
        return $this->image;
    }

    public function addImage(image $image): static
    {
        if (!$this->image->contains($image)) {
            $this->image->add($image);
        }

        return $this;
    }

    public function removeImage(image $image): static
    {
        $this->image->removeElement($image);

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $imageId = null;

    #[ORM\Column(type: Types::BLOB)]
    private $imageData;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avis = null;

    /**
     * @var Collection<int, Habitat>
     */
    #[ORM\ManyToMany(targetEntity: Habitat::class, mappedBy: 'image')]
    private Collection $habitats;

    public function __construct()
    {
        $this->habitats = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImageId(): ?int
    {
        return $this->imageId;
    }

    public function setImageId(int $imageId): static
    {
        $this->imageId = $imageId;

        return $this;
    }

    public function getImageData()
    {
        return $this->imageData;
    }

    public function setImageData($imageData): static
    {
        $this->imageData = $imageData;

        return $this;
    }

    public function getAvis(): ?string
    {
        return $this->avis;
    }

    public function setAvis(?string $avis): static
    {
        $this->avis = $avis;

        return $this;
    }

    /**
     * @return Collection<int, Habitat>
     */
    public function getHabitats(): Collection
    {
        return $this->habitats;
    }

    public function addHabitat(Habitat $habitat): static
    {
        if (!$this->habitats->contains($habitat)) {
            $this->habitats->add($habitat);
            $habitat->addImage($this);
        }

        return $this;
    }

    public function removeHabitat(Habitat $habitat): static
    {
        if ($this->habitats->removeElement($habitat)) {
            $habitat->removeImage($this);
        }

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
class Utilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $username = null;

    #[ORM\Column(length: 50)]
    private ?string $password = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(length: 50)]
    private ?string $prenom = null;

    #[ORM\ManyToOne(inversedBy: 'utilisateurs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?role $role = null;

    /**
     * @var Collection<int, rapportVeterinaire>
     */
    #[ORM\OneToMany(targetEntity: rapportVeterinaire::class, mappedBy: 'utilisateur')]
    private Collection $rapport_veterinaire;

    public function __construct()
    {
        $this->rapport_veterinaire = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getRole(): ?role
    {
        return $this->role;
    }

    public function setRole(?role $role): static
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return Collection<int, rapportVeterinaire>
     */
    public function getRapportVeterinaire(): Collection
    {
        return $this->rapport_veterinaire;
    }

    public function addRapportVeterinaire(rapportVeterinaire $rapportVeterinaire): static
    {
        if (!$this->rapport_veterinaire->contains($rapportVeterinaire)) {
            $this->rapport_veterinaire->add($rapportVeterinaire);
            $rapportVeterinaire->setUtilisateur($this);
        }

        return $this;
    }

    public function removeRapportVeterinaire(rapportVeterinaire $rapportVeterinaire): static
    {
        if ($this->rapport_veterinaire->removeElement($rapportVeterinaire)) {
            // set the owning side to null (unless already changed)
            if ($rapportVeterinaire->getUtilisateur() === $this) {
                $rapportVeterinaire->setUtilisateur(null);
            }
        }

        return $this;
    }
}

<?php
namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null; // Ajout de la propriété firstName

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;  // Ajout de la propriété lastName

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 255)]
    private ?string $apiToken;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private ?role $role = null;

    /**
     * @var Collection<int, rapportVeterinaire>
     */
    #[ORM\OneToMany(targetEntity: rapportVeterinaire::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $rapportVeterinaire;

    /** @throws \Exception */
    public function __construct()
    {    
        $this->apiToken = bin2hex(random_bytes(length: 20));
        $this->rapportVeterinaire = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string  // Getter pour lastName
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static  // Setter pour lastName
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // Si vous stockez des données temporaires et sensibles sur l'utilisateur, effacez-les ici
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
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

    public function getApiToken(): ?string
    {
        return $this->apiToken;
    }

    public function setApiToken(string $apiToken): static
    {
        $this->apiToken = $apiToken;
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
        return $this->rapportVeterinaire;
    }

    public function addRapportVeterinaire(rapportVeterinaire $rapportVeterinaire): static
    {
        if (!$this->rapportVeterinaire->contains($rapportVeterinaire)) {
            $this->rapportVeterinaire->add($rapportVeterinaire);
            $rapportVeterinaire->setUtilisateur($this);
        }
        return $this;
    }

    public function removeRapportVeterinaire(rapportVeterinaire $rapportVeterinaire): static
    {
        if ($this->rapportVeterinaire->removeElement($rapportVeterinaire)) {
            // Set the owning side to null (unless already changed)
            if ($rapportVeterinaire->getUtilisateur() === $this) {
                $rapportVeterinaire->setUtilisateur(null);
            }
        }
        return $this;
    }
}

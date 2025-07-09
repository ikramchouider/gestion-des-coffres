<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Un compte existe déjà avec cet email.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: "L'email est obligatoire.")]
    #[Assert\Email(message: "L'email {{ value }} n'est pas valide.")]
    #[Assert\Length(max: 180, maxMessage: "L'email ne peut pas dépasser {{ limit }} caractères.")]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank(message: "Le mot de passe est obligatoire.", groups: ["registration"])]
    #[Assert\Length(
        min: 8,
        max: 255,
        minMessage: "Le mot de passe doit faire au moins {{ limit }} caractères.",
        maxMessage: "Le mot de passe ne peut pas dépasser {{ limit }} caractères.",
        groups: ["registration"]
    )]
    private ?string $password = null;

    /**
     * @var Collection<int, Coffre>
     */
    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Coffre::class, orphanRemoval: true)]
    private Collection $coffres;

    /**
     * @var Collection<int, SecretCodeHistory>
     */
    #[ORM\OneToMany(mappedBy: 'generatedBy', targetEntity: SecretCodeHistory::class)]
    private Collection $secretCodeHistories;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->coffres = new ArrayCollection();
        $this->secretCodeHistories = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
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
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        $this->updatedAt = new \DateTimeImmutable();

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
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    /**
     * @return Collection<int, Coffre>
     */
    public function getCoffres(): Collection
    {
        return $this->coffres;
    }

    public function addCoffre(Coffre $coffre): static
    {
        if (!$this->coffres->contains($coffre)) {
            $this->coffres->add($coffre);
            $coffre->setOwner($this);
        }

        return $this;
    }

    public function removeCoffre(Coffre $coffre): static
    {
        if ($this->coffres->removeElement($coffre)) {
            if ($coffre->getOwner() === $this) {
                $coffre->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SecretCodeHistory>
     */
    public function getSecretCodeHistories(): Collection
    {
        return $this->secretCodeHistories;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function eraseCredentials(): void
    {
        // Efface les données sensibles temporaires
    }

    public function __serialize(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'password' => $this->password,
            'roles' => $this->roles
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->id = $data['id'];
        $this->email = $data['email'];
        $this->password = $data['password'];
        $this->roles = $data['roles'];
    }
}
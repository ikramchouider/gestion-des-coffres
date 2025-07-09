<?php

namespace App\Entity;

use App\Repository\CoffreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CoffreRepository::class)]
#[UniqueEntity(fields: ['currentSecretCode'], message: 'Un coffre avec ce code secret existe déjà.')]
#[UniqueEntity(fields: ['name', 'owner'], message: 'Vous avez déjà un coffre avec ce nom.')]
class Coffre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom du coffre est obligatoire.')]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'Le nom doit faire au moins {{ limit }} caractères.',
        maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $name = null;

    #[ORM\Column(length: 36, unique: true)]
    private ?string $currentSecretCode = null;

    #[ORM\ManyToOne(inversedBy: 'coffres')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $owner = null;

    #[ORM\OneToMany(
        mappedBy: 'coffre', 
        targetEntity: SecretCodeHistory::class, 
        cascade: ['persist'],
        orphanRemoval: true
    )]
    #[ORM\OrderBy(['generatedAt' => 'DESC'])]
    private Collection $secretCodeHistories;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->secretCodeHistories = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

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
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getCurrentSecretCode(): ?string
    {
        return $this->currentSecretCode;
    }

    public function setCurrentSecretCode(string $currentSecretCode): static
    {
        $this->currentSecretCode = $currentSecretCode;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @return Collection<int, SecretCodeHistory>
     */
    public function getSecretCodeHistories(): Collection
    {
        return $this->secretCodeHistories;
    }

    public function addSecretCodeHistory(SecretCodeHistory $secretCodeHistory): static
    {
        if (!$this->secretCodeHistories->contains($secretCodeHistory)) {
            $this->secretCodeHistories->add($secretCodeHistory);
            $secretCodeHistory->setCoffre($this);
        }

        return $this;
    }

    public function removeSecretCodeHistory(SecretCodeHistory $secretCodeHistory): static
    {
        if ($this->secretCodeHistories->removeElement($secretCodeHistory)) {
            if ($secretCodeHistory->getCoffre() === $this) {
                $secretCodeHistory->setCoffre(null);
            }
        }

        return $this;
    }

    // Méthode utilitaire pour la génération de code
    public function generateNewSecretCode(SecretCodeGenerator $generator): string
    {
        return $generator->generateHexCode(36);
    }
}
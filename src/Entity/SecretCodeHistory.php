<?php

namespace App\Entity;

use App\Repository\SecretCodeHistoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SecretCodeHistoryRepository::class)]
class SecretCodeHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 36)]
    private ?string $secretCode = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $generatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'secretCodeHistories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Coffre $coffre = null;

    #[ORM\ManyToOne(inversedBy: 'secretCodeHistories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $generatedBy = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSecretCode(): ?string
    {
        return $this->secretCode;
    }

    public function setSecretCode(string $secretCode): static
    {
        $this->secretCode = $secretCode;

        return $this;
    }

    public function getGeneratedAt(): ?\DateTimeImmutable
    {
        return $this->generatedAt;
    }

    public function setGeneratedAt(\DateTimeImmutable $generatedAt): static
    {
        $this->generatedAt = $generatedAt;

        return $this;
    }

    public function getCoffre(): ?Coffre
    {
        return $this->coffre;
    }

    public function setCoffre(?Coffre $coffre): static
    {
        $this->coffre = $coffre;

        return $this;
    }

    public function getGeneratedBy(): ?User
    {
        return $this->generatedBy;
    }

    public function setGeneratedBy(?User $generatedBy): static
    {
        $this->generatedBy = $generatedBy;

        return $this;
    }
}

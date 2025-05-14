<?php

namespace App\Entity;

use App\Repository\SavedSearchRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SavedSearchRepository::class)]
#[ORM\HasLifecycleCallbacks]
class SavedSearch
{
    // ──────── Primary key ──────────────────────────────────────────────
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['search:read'])]
    private ?int $id = null;

    // ──────── Domain fields ────────────────────────────────────────────
    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Le nom ne peut pas être vide.')]
    #[Groups(['search:read'])]
    private ?string $name = null;

    #[ORM\Column(type: 'json')]
    #[Assert\NotNull(message: 'Les filtres sont obligatoires.')]
    #[Groups(['search:read'])]
    private array $filters = [];

    #[ORM\Column]
    #[Groups(['search:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    // ──────── Relation ────────────────────────────────────────────────
    #[ORM\ManyToOne(inversedBy: 'savedSearches')]
    #[ORM\JoinColumn(nullable: false)]
    #[Ignore]                    
    private ?User $owner = null;

    // ──────── Getters / setters ───────────────────────────────────────
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function setFilters(array $filters): static
    {
        $this->filters = $filters;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
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

    // ──────── Lifecycle callbacks ─────────────────────────────────────
    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}

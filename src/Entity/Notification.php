<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
#[ORM\Table(indexes: [
    new ORM\Index(columns: ['user_id', 'is_read'])
])]
#[ORM\HasLifecycleCallbacks]
class Notification
{
    /* ──────── Primary key ────────────────────────────────────────── */
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    #[Groups(['notif:read'])]
    private ?int $id = null;

    /* ──────── Payload ────────────────────────────────────────────── */
    #[ORM\Column(length: 255)]
    #[Groups(['notif:read'])]
    private string $message;

    #[ORM\Column(length: 30, nullable: true)]
    #[Groups(['notif:read'])]
    private ?string $type = null;         

    #[ORM\Column]
    #[Groups(['notif:read'])]
    private int $targetId;                

    #[ORM\Column(options: ['default' => false])]
    #[Groups(['notif:read'])]
    private bool $isRead = false;

    #[ORM\Column]
    #[Groups(['notif:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    /* ──────── Relation ───────────────────────────────────────────── */
    #[ORM\ManyToOne(inversedBy: 'notifications')]
    #[ORM\JoinColumn(nullable: false)]
    #[Ignore]                             
    private ?User $user = null;

    /* ──────── Getters / setters ──────────────────────────────────── */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getTargetId(): int
    {
        return $this->targetId;
    }

    public function setTargetId(int $targetId): self
    {
        $this->targetId = $targetId;
        return $this;
    }

    public function isRead(): bool
    {
        return $this->isRead;
    }

    public function markRead(): self
    {
        $this->isRead = true;
        return $this;
    }

    public function setIsRead(bool $isRead): self
    {
        $this->isRead = $isRead;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    /* ──────── Lifecycle callbacks ───────────────────────────────── */
    #[ORM\PrePersist]
    public function stamp(): void
    {
        $this->createdAt ??= new \DateTimeImmutable();
    }
}

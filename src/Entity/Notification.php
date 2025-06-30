<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\NotificationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification
{
    use TimestampableTrait;

    public const TYPE_EXPIRATION_TODAY = 'expiration_today';
    public const TYPE_EXPIRATION_TOMORROW = 'expiration_tomorrow';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['notification:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['notification:read'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['notification:read'])]
    private ?string $message = null;

    #[ORM\Column(length: 50)]
    #[Groups(['notification:read'])]
    private ?string $type = null;

    #[ORM\Column]
    #[Groups(['notification:read'])]
    private ?bool $isRead = false;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['notification:read'])]
    private ?Product $product = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['notification:read'])]
    private ?\DateTimeInterface $notifiedAt = null;

    public function __construct()
    {
        $this->notifiedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function isRead(): ?bool
    {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead): static
    {
        $this->isRead = $isRead;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;
        return $this;
    }

    public function getNotifiedAt(): ?\DateTimeInterface
    {
        return $this->notifiedAt;
    }

    public function setNotifiedAt(\DateTimeInterface $notifiedAt): static
    {
        $this->notifiedAt = $notifiedAt;
        return $this;
    }
}
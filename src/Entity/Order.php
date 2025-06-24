<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class  Order
{
    use TimestampableTrait;

    public const STATUS_RESERVED = 'reserved';
    public const STATUS_PAID = 'paid';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_AWAITING_PICKUP = 'awaiting_pickup';
    public const STATUS_PICKED_UP = 'picked_up';
    public const STATUS_COMPLETED = 'completed';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['order:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[Groups(['order:read'])]
    private ?User $buyer = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[Groups(['order:read'])]
    private ?User $seller = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[Groups(['order:read'])]
    private ?Product $product = null;

    #[ORM\Column(length: 255)]
    #[Groups(['order:read'])]
    private ?string $status = null;

    #[ORM\Column(length: 255, nullable: true, unique: true)]
    #[Groups(['order:read'])]
    private ?string $qrCodeToken = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBuyer(): ?User
    {
        return $this->buyer;
    }

    public function setBuyer(?User $buyer): static
    {
        $this->buyer = $buyer;

        return $this;
    }

    public function getSeller(): ?User
    {
        return $this->seller;
    }

    public function setSeller(?User $seller): static
    {
        $this->seller = $seller;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getQrCodeToken(): ?string
    {
        return $this->qrCodeToken;
    }

    public function setQrCodeToken(string $qrCodeToken): static
    {
        $this->qrCodeToken = $qrCodeToken;

        return $this;
    }
}

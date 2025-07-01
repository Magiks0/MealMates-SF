<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\PurchaseStatus;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class  Order
{
    use TimestampableTrait;

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

    // #[ORM\Column(length: 255)]
    // #[Groups(['order:read'])]
    // private ?string $status = null;

    #[ORM\Column(length: 255, nullable: true, unique: true)]
    #[Groups(['order:read'])]
    private ?string $qrCodeToken = null;

    #[ORM\Column(type: 'string', enumType: PurchaseStatus::class)]
    #[Groups(['order:read'])]
    private PurchaseStatus $purchaseStatus;


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

    public function getStatus(): PurchaseStatus
    {
        return $this->purchaseStatus;
    }

    public function setStatus(PurchaseStatus $purchaseStatus): static
    {
        $this->purchaseStatus = $purchaseStatus;
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

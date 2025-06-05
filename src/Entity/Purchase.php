<?php

namespace App\Entity;

use App\Repository\PurchaseRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: PurchaseRepository::class)]
class Purchase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['purchase:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'purchases')]
    #[Groups(['purchase:read'])]
    private ?User $buyer = null;

    #[ORM\ManyToOne(inversedBy: 'purchases')]
    #[Groups(['purchase:read'])]
    private ?User $seller = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[Groups(['purchase:read'])]
    private ?Product $product = null;

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
}

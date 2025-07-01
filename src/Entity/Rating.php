<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\RatingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RatingRepository::class)]
class Rating
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['rating:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ratingsGiven')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['rating:read'])]
    private ?User $reviewer = null;

    #[ORM\ManyToOne(inversedBy: 'ratingsReceived')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['rating:read'])]
    private ?User $reviewed = null;
    
    #[ORM\ManyToOne(inversedBy: 'ratings')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['rating:read'])]
    private ?Order $order = null;

    #[ORM\Column]
    #[Assert\Range(min: 1, max: 5)]
    #[Groups(['rating:read', 'rating:write'])]
    private ?float $score = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['rating:read', 'rating:write'])]
    private ?string $comment = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReviewer(): ?User
    {
        return $this->reviewer;
    }

    public function setReviewer(?User $reviewer): static
    {
        $this->reviewer = $reviewer;

        return $this;
    }

    public function getReviewed(): ?User
    {
        return $this->reviewed;
    }

    public function setReviewed(?User $reviewed): static
    {
        $this->reviewed = $reviewed;

        return $this;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): static
    {
        $this->order = $order;

        return $this;
    }

    public function getScore(): ?float
    {
        return $this->score;
    }

    public function setScore(float $score): static
    {
        $this->score = $score;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }
}
<?php

namespace App\Entity;

use App\Enum\DayOfWeek;
use App\Repository\AvailabilityRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AvailabilityRepository::class)]
class Availability
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(enumType: DayOfWeek::class)]
    #[Assert\NotBlank]
    private ?DayOfWeek $dayOfWeek = null;

    #[ORM\Column(type: 'time')]
    #[Assert\NotBlank]
    #[Assert\Type(\DateTimeInterface::class)]
    private ?\DateTimeInterface $minTime = null;

    #[ORM\Column(type: 'time')]
    #[Assert\NotBlank]
    #[Assert\Type(\DateTimeInterface::class)]
    private ?\DateTimeInterface $maxTime = null;

    #[ORM\ManyToOne(inversedBy: 'availabilities')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDayOfWeek(): ?DayOfWeek
    {
        return $this->dayOfWeek;
    }

    public function setDayOfWeek(DayOfWeek|string $dayOfWeek): self
    {
        $this->dayOfWeek = is_string($dayOfWeek) ? DayOfWeek::from($dayOfWeek) : $dayOfWeek;
        return $this;
    }

    public function getMinTime(): ?\DateTimeInterface
    {
        return $this->minTime;
    }

    public function setMinTime(\DateTimeInterface|string|null $minTime): self
    {
        $this->minTime = is_string($minTime) ? new \DateTime($minTime) : $minTime;
        return $this;
    }

    public function getMaxTime(): ?\DateTimeInterface
    {
        return $this->maxTime;
    }

    public function setMaxTime(\DateTimeInterface|string|null $maxTime): self
    {
        $this->maxTime = is_string($maxTime) ? new \DateTime($maxTime) : $maxTime;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }
}

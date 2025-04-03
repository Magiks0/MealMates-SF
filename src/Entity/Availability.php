<?php

namespace App\Entity;

use App\Repository\AvailabilitiesRepository;
use App\Enum\DayOfWeek;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AvailabilitiesRepository::class)]
class Availabilities
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'availabilities')]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $user = null;

    #[ORM\Column(type: 'string', enumType: DayOfWeek::class)]
    private DayOfWeek $dayOfWeek;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $min_time = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $max_time = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getDayOfWeek(): DayOfWeek
    {
        return $this->dayOfWeek;
    }

    public function setDayOfWeek(DayOfWeek $dayOfWeek): self
    {
        $this->dayOfWeek = $dayOfWeek;
        return $this;
    }

    public function getMinTime(): ?\DateTimeInterface
    {
        return $this->min_time;
    }

    public function setMinTime(?\DateTimeInterface $min_time): static
    {
        $this->min_time = $min_time;
        return $this;
    }

    public function getMaxTime(): ?\DateTimeInterface
    {
        return $this->max_time;
    }

    public function setMaxTime(?\DateTimeInterface $max_time): static
    {
        $this->max_time = $max_time;
        return $this;
    }
}

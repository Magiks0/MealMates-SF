<?php

namespace App\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Attribute\Groups;

trait TimestampableTrait
{
    use TimestampableEntity;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    #[Groups('chat:read')]
    #[Gedmo\Timestampable(on: 'create')]
    protected $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    #[Gedmo\Timestampable(on: 'update')]
    protected $updatedAt;

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }
}

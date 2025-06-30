<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\ChatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ChatRepository::class)]
class Chat
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('chat:read')]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'chats')]
    private ?User $buyer = null;

    #[ORM\ManyToOne(inversedBy: 'chats')]
    private ?User $seller = null;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'chat')]
    private Collection $messages;

    #[ORM\ManyToOne(inversedBy: 'chats')]
    private ?Product $product = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[Groups(['order:read'])]
    private ?Order $linked_order = null;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setChat($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getChat() === $this) {
                $message->setChat(null);
            }
        }

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

    public function getLinkedOrder(): ?Order
    {
        return $this->linked_order;
    }

    public function setLinkedOrder(?Order $linked_order): static
    {
        $this->linked_order = $linked_order;

        return $this;
    }
}

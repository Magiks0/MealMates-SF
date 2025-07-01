<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['product:read', 'user:read', 'notification:read', 'order:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['product:read', 'order:read'])]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Groups('product:read')]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups('product:read')]
    private ?int $quantity = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups('product:read')]
    private ?\DateTimeInterface $peremptionDate = null;

    #[ORM\Column]
    #[Groups(['product:read', 'order:read'])]
    private ?float $price = null;

    #[ORM\Column]
    #[Groups(['product:read', 'user:read'])]
    private ?bool $donation = false;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups('product:read')]
    private ?\DateTimeInterface $collection_date = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[Groups('product:read', 'order:read')]
    private ?User $user = null;

    /**
     * @var Collection<int, File>
     */
    #[ORM\OneToMany(targetEntity: File::class, mappedBy: 'product', cascade: ['persist'])]
    #[Groups('product:read')]
    private Collection $files;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[Groups('product:read')]
    private ?Type $type = null;

    /**
     * @var Collection<int, Dietary>
     */
    #[ORM\ManyToMany(targetEntity: Dietary::class, inversedBy: 'products')]
    #[Groups('product:read')]
    private Collection $dietaries;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[Groups('product:read')]
    private ?Address $address = null;

    #[Groups('product:read')]
    private $location = null;

    /**
     * @var Collection<int, Chat>
     */
    #[ORM\OneToMany(targetEntity: Chat::class, mappedBy: 'product')]
    private Collection $chats;

    #[ORM\Column]
    private ?string $stripeProductId = null;

    #[ORM\Column]
    private ?string $stripePriceId = null;

    #[ORM\Column]
    private ?bool $published = true;

    public function __construct()
    {
        $this->files = new ArrayCollection();
        $this->dietaries = new ArrayCollection();
        $this->chats = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPeremptionDate(): ?\DateTimeInterface
    {
        return $this->peremptionDate;
    }

    public function setPeremptionDate(\DateTimeInterface $peremptionDate): static
    {
        $this->peremptionDate = $peremptionDate;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function isDonation(): ?bool
    {
        return $this->donation;
    }

    public function setDonation(bool $donation): static
    {
        $this->donation = $donation;

        return $this;
    }

    public function getCollectionDate(): ?\DateTimeInterface
    {
        return $this->collection_date;
    }

    public function setCollectionDate(\DateTimeInterface $collectionDate): static
    {
        $this->collection_date = $collectionDate;

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

    /**
     * @return Collection<int, File>
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(File $file): static
    {
        if (!$this->files->contains($file)) {
            $this->files->add($file);
            $file->setProduct($this);
        }

        return $this;
    }

    public function removeFile(File $file): static
    {
        if ($this->files->removeElement($file)) {
            // set the owning side to null (unless already changed)
            if ($file->getProduct() === $this) {
                $file->setProduct(null);
            }
        }

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getDietaries(): Collection
    {
        return $this->dietaries;
    }

    public function addDietary(self $dietary): static
    {
        if (!$this->dietaries->contains($dietary)) {
            $this->dietaries->add($dietary);
        }

        return $this;
    }

    public function removeDietary(self $dietary): static
    {
        $this->dietaries->removeElement($dietary);

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): static
    {
        $this->address = $address;

        return $this;
    }

    #[Groups('product:read')]
    public function getLocation(): ?string
    {
        if ($this->address) {
            return $this->address->getName();
        }
        return null;
    }

    /**
     * @return Collection<int, Chat>
     */
    public function getChats(): Collection
    {
        return $this->chats;
    }

    public function addChat(Chat $chat): static
    {
        if (!$this->chats->contains($chat)) {
            $this->chats->add($chat);
            $chat->setProduct($this);
        }

        return $this;
    }

    public function removeChat(Chat $chat): static
    {
        if ($this->chats->removeElement($chat)) {
            // set the owning side to null (unless already changed)
            if ($chat->getProduct() === $this) {
                $chat->setProduct(null);
            }
        }

        return $this;
    }

    public function getStripeProductId(): ?string
    {
        return $this->stripeProductId;
    }

    public function setStripeProductId(string $stripeProductId): static
    {
        $this->stripeProductId = $stripeProductId;

        return $this;
    }

    public function getStripePriceId(): ?string
    {
        return $this->stripePriceId;
    }

    public function setStripePriceId(string $stripePriceId): static
    {
        $this->stripePriceId = $stripePriceId;

        return $this;
    }

    public function isPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): static
    {
        $this->published = $published;

        return $this;
    }
}
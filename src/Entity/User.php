<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups('user:read')]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column]
    private ?bool $isVerified = false;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('product:read')]
    private ?float $note = null;

    /**
     * @var Collection<int, Availability>
     */
    #[ORM\OneToMany(targetEntity: Availability::class, mappedBy: 'user_id', orphanRemoval: true)]
    private Collection $availabilities;

    #[ORM\Column(length: 255)]
    #[Groups('product:read')]
    private ?string $username = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('product:read')]
    private ?string $address = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('product:read')]
    private ?string $image_url = null;

    /**
     * @var Collection<int, Dietary>
     */
    #[ORM\ManyToMany(targetEntity: Dietary::class, inversedBy: 'users')]
    private Collection $dietaries;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Product::class)]
    private Collection $products;

    public function __construct()
    {
        $this->availabilities = new ArrayCollection();
        $this->dietaries = new ArrayCollection();
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    // Cette méthode est utilisée pour nettoyer les données sensibles
    public function eraseCredentials(): void
    {
        // Si tu stockes des données temporaires sensibles sur l'utilisateur, nettoie-les ici
    }

    public function isVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;
        return $this;
    }

    /**
     * @return Collection<int, Availability>
     */
    public function getAvailabilities(): Collection
    {
        return $this->availabilities;
    }

    public function addAvailability(Availability $availability): static
    {
        if (!$this->availabilities->contains($availability)) {
            $this->availabilities->add($availability);
            $availability->setUser($this);
        }

        return $this;
    }

    public function removeAvailability(Availability $availability): static
    {
        if ($this->availabilities->removeElement($availability)) {
            // set the owning side to null (unless already changed)
            if ($availability->getUser() === $this) {
                $availability->setUser(null);
            }
        }

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->image_url;
    }

    public function setImageUrl(?string $image_url): static
    {
        $this->image_url = $image_url;

        return $this;
    }

    public function getNote(): ?float
    {
        return $this->note;
    }

    public function setNote(?float $note): self
    {
        $this->note = $note;
        return $this;
    }

    /**
     * @return Collection<int, Dietary>
     */
    public function getDietaries(): Collection
    {
        return $this->dietaries;
    }

    public function addDietetic(Dietary $dietary): static
    {
        if (!$this->dietaries->contains($dietary)) {
            $this->dietaries->add($dietary);
        }

        return $this;
    }

    public function removeDietetic(Dietary $dietary): static
    {
        $this->dietaries->removeElement($dietary);

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setUser($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getUser() === $this) {
                $product->setUser(null);
            }
        }

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }
}
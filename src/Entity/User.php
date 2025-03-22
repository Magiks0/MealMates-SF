<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column]
    private ?bool $isVerified = false;

    /**
     * @var Collection<int, Availabilities>
     */
    #[ORM\OneToMany(targetEntity: Availabilities::class, mappedBy: 'user_id', orphanRemoval: true)]
    private Collection $availabilities;

    /**
     * @var Collection<int, Preferences>
     */
    #[ORM\ManyToMany(targetEntity: Preferences::class, mappedBy: 'user_id')]
    private Collection $preferences;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adress = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image_url = null;

    public function __construct()
    {
        $this->availabilities = new ArrayCollection();
        $this->preferences = new ArrayCollection();
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

    // Cette méthode remplace getUsername() dans Symfony 5.3+ et 7
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    // Retourne le(s) rôle(s) de l'utilisateur. Ici, on lui attribue ROLE_USER par défaut.
    public function getRoles(): array
    {
        return ['ROLE_USER'];
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
     * @return Collection<int, Availabilities>
     */
    public function getAvailabilities(): Collection
    {
        return $this->availabilities;
    }

    public function addAvailability(Availabilities $availability): static
    {
        if (!$this->availabilities->contains($availability)) {
            $this->availabilities->add($availability);
            $availability->setUser($this);
        }

        return $this;
    }

    public function removeAvailability(Availabilities $availability): static
    {
        if ($this->availabilities->removeElement($availability)) {
            // set the owning side to null (unless already changed)
            if ($availability->getUser() === $this) {
                $availability->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Preferences>
     */
    public function getPreferences(): Collection
    {
        return $this->preferences;
    }

    public function addPreference(Preferences $preference): static
    {
        if (!$this->preferences->contains($preference)) {
            $this->preferences->add($preference);
            $preference->addUserId($this);
        }

        return $this;
    }

    public function removePreference(Preferences $preference): static
    {
        if ($this->preferences->removeElement($preference)) {
            $preference->removeUserId($this);
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

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(?string $adress): static
    {
        $this->adress = $adress;

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
}

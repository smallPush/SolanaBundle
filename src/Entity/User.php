<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: SolanaContract::class)]
    private Collection $authoredContracts;

    #[ORM\OneToMany(mappedBy: 'donor', targetEntity: SolanaContract::class)]
    private Collection $contractsAsDonor;

    #[ORM\OneToMany(mappedBy: 'volunteer', targetEntity: SolanaContract::class)]
    private Collection $contractsAsVolunteer;

    public function __construct()
    {
        $this->authoredContracts = new ArrayCollection();
        $this->contractsAsDonor = new ArrayCollection();
        $this->contractsAsVolunteer = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, SolanaContract>
     */
    public function getAuthoredContracts(): Collection
    {
        return $this->authoredContracts;
    }

    public function addAuthoredContract(SolanaContract $authoredContract): self
    {
        if (!$this->authoredContracts->contains($authoredContract)) {
            $this->authoredContracts->add($authoredContract);
            $authoredContract->setAuthor($this);
        }

        return $this;
    }

    public function removeAuthoredContract(SolanaContract $authoredContract): self
    {
        if ($this->authoredContracts->removeElement($authoredContract)) {
            // set the owning side to null (unless already changed)
            if ($authoredContract->getAuthor() === $this) {
                $authoredContract->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SolanaContract>
     */
    public function getContractsAsDonor(): Collection
    {
        return $this->contractsAsDonor;
    }

    public function addContractsAsDonor(SolanaContract $contractsAsDonor): self
    {
        if (!$this->contractsAsDonor->contains($contractsAsDonor)) {
            $this->contractsAsDonor->add($contractsAsDonor);
            $contractsAsDonor->setDonor($this);
        }

        return $this;
    }

    public function removeContractsAsDonor(SolanaContract $contractsAsDonor): self
    {
        if ($this->contractsAsDonor->removeElement($contractsAsDonor)) {
            // set the owning side to null (unless already changed)
            if ($contractsAsDonor->getDonor() === $this) {
                $contractsAsDonor->setDonor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SolanaContract>
     */
    public function getContractsAsVolunteer(): Collection
    {
        return $this->contractsAsVolunteer;
    }

    public function addContractsAsVolunteer(SolanaContract $contractsAsVolunteer): self
    {
        if (!$this->contractsAsVolunteer->contains($contractsAsVolunteer)) {
            $this->contractsAsVolunteer->add($contractsAsVolunteer);
            $contractsAsVolunteer->setVolunteer($this);
        }

        return $this;
    }

    public function removeContractsAsVolunteer(SolanaContract $contractsAsVolunteer): self
    {
        if ($this->contractsAsVolunteer->removeElement($contractsAsVolunteer)) {
            // set the owning side to null (unless already changed)
            if ($contractsAsVolunteer->getVolunteer() === $this) {
                $contractsAsVolunteer->setVolunteer(null);
            }
        }

        return $this;
    }
}

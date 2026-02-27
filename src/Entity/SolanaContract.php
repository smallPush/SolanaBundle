<?php

namespace App\Entity;

use App\Repository\SolanaContractRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SolanaContractRepository::class)]
#[ORM\HasLifecycleCallbacks]
class SolanaContract
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 44)]
    private ?string $donorWallet = null;

    #[ORM\Column(length: 44)]
    private ?string $volunteerWallet = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 9)]
    private ?string $amount = null;

    #[ORM\Column(length: 50)]
    private ?string $status = 'pending';

    #[ORM\ManyToOne(inversedBy: 'authoredContracts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\ManyToOne(inversedBy: 'contractsAsDonor')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $donor = null;

    #[ORM\ManyToOne(inversedBy: 'contractsAsVolunteer')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $volunteer = null;

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function validate(): void
    {
        if (bccomp((string)$this->amount, '0', 9) <= 0) {
            throw new \InvalidArgumentException('Amount must be positive.');
        }

        if ($this->donorWallet !== null && strlen($this->donorWallet) > 44) {
            throw new \LengthException('Donor wallet address cannot exceed 44 characters.');
        }

        if ($this->volunteerWallet !== null && strlen($this->volunteerWallet) > 44) {
            throw new \LengthException('Volunteer wallet address cannot exceed 44 characters.');
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDonorWallet(): ?string
    {
        return $this->donorWallet;
    }

    public function setDonorWallet(string $donorWallet): self
    {
        $this->donorWallet = $donorWallet;

        return $this;
    }

    public function getVolunteerWallet(): ?string
    {
        return $this->volunteerWallet;
    }

    public function setVolunteerWallet(string $volunteerWallet): self
    {
        $this->volunteerWallet = $volunteerWallet;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getDonor(): ?User
    {
        return $this->donor;
    }

    public function setDonor(?User $donor): self
    {
        $this->donor = $donor;

        return $this;
    }

    public function getVolunteer(): ?User
    {
        return $this->volunteer;
    }

    public function setVolunteer(?User $volunteer): self
    {
        $this->volunteer = $volunteer;

        return $this;
    }

    public function canBeValidatedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        // Use identity comparison if possible, otherwise rely on ID if persisted
        $isDonorUser = ($user === $this->getDonor()) || ($user->getId() && $this->getDonor()?->getId() && $user->getId() === $this->getDonor()->getId());
        $isVolunteerUser = ($user === $this->getVolunteer()) || ($user->getId() && $this->getVolunteer()?->getId() && $user->getId() === $this->getVolunteer()->getId());

        $isDonor = $isDonorUser && in_array('ROLE_DONOR', $user->getRoles(), true);
        $isVolunteer = $isVolunteerUser && in_array('ROLE_VOLUNTEER', $user->getRoles(), true);
        $status = $this->getStatus();

        if ($isDonor && in_array($status, ['pending', 'validated_volunteer'], true)) {
            return true;
        }

        if ($isVolunteer && in_array($status, ['pending', 'validated_donor'], true)) {
            return true;
        }

        return false;
    }
}

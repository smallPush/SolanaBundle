<?php

namespace App\Entity;

use App\Repository\SolanaContractRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SolanaContractRepository::class)]
class SolanaContract
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_VALIDATED_DONOR = 'validated_donor';
    public const STATUS_VALIDATED_VOLUNTEER = 'validated_volunteer';
    public const STATUS_READY_FOR_SIGNATURE = 'ready_for_signature';

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
    private ?string $status = self::STATUS_PENDING;

    #[ORM\ManyToOne(inversedBy: 'authoredContracts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\ManyToOne(inversedBy: 'contractsAsDonor')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $donor = null;

    #[ORM\ManyToOne(inversedBy: 'contractsAsVolunteer')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $volunteer = null;

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

    /**
     * Applies a status transition based on the user performing the validation.
     * Returns true if a transition was applied, false otherwise.
     */
    public function applyTransition(User $user): bool
    {
        if ($user === $this->getDonor() && $this->status === self::STATUS_PENDING) {
            $this->status = self::STATUS_VALIDATED_DONOR;
            return true;
        }

        if ($user === $this->getVolunteer() && $this->status === self::STATUS_PENDING) {
            $this->status = self::STATUS_VALIDATED_VOLUNTEER;
            return true;
        }

        if ($user === $this->getDonor() && $this->status === self::STATUS_VALIDATED_VOLUNTEER) {
            $this->status = self::STATUS_READY_FOR_SIGNATURE;
            return true;
        }

        if ($user === $this->getVolunteer() && $this->status === self::STATUS_VALIDATED_DONOR) {
            $this->status = self::STATUS_READY_FOR_SIGNATURE;
            return true;
        }

        return false;
    }
}

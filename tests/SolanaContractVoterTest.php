<?php

namespace App\Tests;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/stubs/Doctrine/Common/Collections/Collection.php';
require_once __DIR__ . '/stubs/Doctrine/Common/Collections/ArrayCollection.php';

use App\Entity\SolanaContract;
use App\Entity\User;
use App\Security\Voter\SolanaContractVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class MockToken implements TokenInterface
{
    private ?UserInterface $user;

    public function __construct(?UserInterface $user = null)
    {
        $this->user = $user;
    }

    public function __toString(): string
    {
        return '';
    }

    public function getUserIdentifier(): string
    {
        return $this->user ? $this->user->getUserIdentifier() : '';
    }

    public function getRoleNames(): array
    {
        return [];
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(UserInterface $user): void
    {
        $this->user = $user;
    }

    public function eraseCredentials(): void
    {
    }

    public function getAttributes(): array
    {
        return [];
    }

    public function setAttributes(array $attributes): void
    {
    }

    public function hasAttribute(string $name): bool
    {
        return false;
    }

    public function getAttribute(string $name): mixed
    {
        return null;
    }

    public function setAttribute(string $name, mixed $value): void
    {
    }

    public function __serialize(): array
    {
        return [];
    }

    public function __unserialize(array $data): void
    {
    }
}

class SolanaContractVoterTest
{
    private int $passes = 0;
    private int $fails = 0;
    private SolanaContractVoter $voter;

    public function __construct()
    {
        $this->voter = new SolanaContractVoter();
    }

    public function run()
    {
        echo "Running SolanaContractVoterTest...\n";

        $this->testCanViewAuthor();
        $this->testCanViewDonor();
        $this->testCanViewVolunteer();
        $this->testCannotViewUnrelated();

        $this->testCanValidateDonor();
        $this->testCanValidateVolunteer();
        $this->testCannotValidateAuthor();
        $this->testCannotValidateUnrelated();

        $this->testSupports();
        $this->testUserNotLoggedIn();

        echo "\nSummary: {$this->passes} Passed, {$this->fails} Failed.\n";

        if ($this->fails > 0) {
            exit(1);
        }
    }

    private function assert(bool $condition, string $message)
    {
        if ($condition) {
            echo "PASS: $message\n";
            $this->passes++;
        } else {
            echo "FAIL: $message\n";
            $this->fails++;
        }
    }

    private function createContract(User $author = null, User $donor = null, User $volunteer = null): SolanaContract
    {
        $contract = new SolanaContract();
        if ($author) $contract->setAuthor($author);
        if ($donor) $contract->setDonor($donor);
        if ($volunteer) $contract->setVolunteer($volunteer);
        return $contract;
    }

    private function testCanViewAuthor()
    {
        $user = new User();
        $contract = $this->createContract(author: $user);
        $token = new MockToken($user);

        $result = $this->voter->vote($token, $contract, [SolanaContractVoter::VIEW]);
        $this->assert($result === VoterInterface::ACCESS_GRANTED, "Author can VIEW contract");
    }

    private function testCanViewDonor()
    {
        $user = new User();
        $contract = $this->createContract(donor: $user);
        $token = new MockToken($user);

        $result = $this->voter->vote($token, $contract, [SolanaContractVoter::VIEW]);
        $this->assert($result === VoterInterface::ACCESS_GRANTED, "Donor can VIEW contract");
    }

    private function testCanViewVolunteer()
    {
        $user = new User();
        $contract = $this->createContract(volunteer: $user);
        $token = new MockToken($user);

        $result = $this->voter->vote($token, $contract, [SolanaContractVoter::VIEW]);
        $this->assert($result === VoterInterface::ACCESS_GRANTED, "Volunteer can VIEW contract");
    }

    private function testCannotViewUnrelated()
    {
        $user = new User();
        $contract = $this->createContract(); // No relations
        $token = new MockToken($user);

        $result = $this->voter->vote($token, $contract, [SolanaContractVoter::VIEW]);
        $this->assert($result === VoterInterface::ACCESS_DENIED, "Unrelated user cannot VIEW contract");
    }

    private function testCanValidateDonor()
    {
        $user = new User();
        $contract = $this->createContract(donor: $user);
        $token = new MockToken($user);

        $result = $this->voter->vote($token, $contract, [SolanaContractVoter::VALIDATE]);
        $this->assert($result === VoterInterface::ACCESS_GRANTED, "Donor can VALIDATE contract");
    }

    private function testCanValidateVolunteer()
    {
        $user = new User();
        $contract = $this->createContract(volunteer: $user);
        $token = new MockToken($user);

        $result = $this->voter->vote($token, $contract, [SolanaContractVoter::VALIDATE]);
        $this->assert($result === VoterInterface::ACCESS_GRANTED, "Volunteer can VALIDATE contract");
    }

    private function testCannotValidateAuthor()
    {
        $user = new User();
        $contract = $this->createContract(author: $user);
        $token = new MockToken($user);

        $result = $this->voter->vote($token, $contract, [SolanaContractVoter::VALIDATE]);
        $this->assert($result === VoterInterface::ACCESS_DENIED, "Author cannot VALIDATE contract (unless also donor/volunteer)");
    }

    private function testCannotValidateUnrelated()
    {
        $user = new User();
        $contract = $this->createContract();
        $token = new MockToken($user);

        $result = $this->voter->vote($token, $contract, [SolanaContractVoter::VALIDATE]);
        $this->assert($result === VoterInterface::ACCESS_DENIED, "Unrelated user cannot VALIDATE contract");
    }

    private function testSupports()
    {
        $user = new User();
        $contract = new SolanaContract();
        $token = new MockToken($user);

        // Unsupported attribute
        $result = $this->voter->vote($token, $contract, ['UNSUPPORTED_ATTRIBUTE']);
        $this->assert($result === VoterInterface::ACCESS_ABSTAIN, "Voter abstains on unsupported attribute");

        // Unsupported subject
        $result = $this->voter->vote($token, new \stdClass(), [SolanaContractVoter::VIEW]);
        $this->assert($result === VoterInterface::ACCESS_ABSTAIN, "Voter abstains on unsupported subject");
    }

    private function testUserNotLoggedIn()
    {
        $contract = new SolanaContract();
        $token = new MockToken(null); // No user

        $result = $this->voter->vote($token, $contract, [SolanaContractVoter::VIEW]);
        // If user is null, getUser() returns null.
        // voteOnAttribute logic: $user = $token->getUser(); if (!$user instanceof User) return false;
        // However, standard Voter logic is:
        // if supported:
        //    call voteOnAttribute.
        //    if voteOnAttribute returns true -> GRANTED.
        //    else -> DENIED.

        // So it should be ACCESS_DENIED.

        $this->assert($result === VoterInterface::ACCESS_DENIED, "Anonymous user cannot VIEW contract");
    }
}

(new SolanaContractVoterTest())->run();

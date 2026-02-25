<?php

namespace App\Tests;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Custom test runner for User entity.
 *
 * This test file is created because the development environment lacks standard testing tools (PHPUnit)
 * and the Doctrine ORM dependencies required by the User entity are missing.
 *
 * It uses stubs for Doctrine\Common\Collections\Collection and ArrayCollection to allow testing
 * the User entity logic in isolation.
 */

// Load stubs for missing dependencies
require_once __DIR__ . '/stubs/Doctrine/Common/Collections/Collection.php';
require_once __DIR__ . '/stubs/Doctrine/Common/Collections/ArrayCollection.php';

use App\Entity\User;
use App\Entity\SolanaContract;
use Doctrine\Common\Collections\Collection;

class UserTest
{
    private int $passes = 0;
    private int $fails = 0;

    public function run()
    {
        echo "Running UserTest...\n";

        $this->testInitialState();
        $this->testAddAuthoredContract();
        $this->testAddDuplicateAuthoredContract();
        $this->testRemoveAuthoredContract();
        $this->testBidirectionalAssociation();

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

    private function testInitialState()
    {
        $user = new User();
        $this->assert($user->getAuthoredContracts() instanceof Collection, "getAuthoredContracts returns a Collection");
        $this->assert($user->getAuthoredContracts()->isEmpty(), "New user has empty authored contracts");
    }

    private function testAddAuthoredContract()
    {
        $user = new User();
        $contract = new SolanaContract();

        $user->addAuthoredContract($contract);

        $this->assert($user->getAuthoredContracts()->contains($contract), "Contract added to collection");
        $this->assert($user->getAuthoredContracts()->count() === 1, "Collection count is 1");
        $this->assert($contract->getAuthor() === $user, "Contract author is set to user");
    }

    private function testAddDuplicateAuthoredContract()
    {
        $user = new User();
        $contract = new SolanaContract();

        $user->addAuthoredContract($contract);
        $user->addAuthoredContract($contract);

        $this->assert($user->getAuthoredContracts()->count() === 1, "Duplicate contract not added");
    }

    private function testRemoveAuthoredContract()
    {
        $user = new User();
        $contract = new SolanaContract();

        $user->addAuthoredContract($contract);
        $user->removeAuthoredContract($contract);

        $this->assert(!$user->getAuthoredContracts()->contains($contract), "Contract removed from collection");
        $this->assert($user->getAuthoredContracts()->isEmpty(), "Collection is empty after removal");
        $this->assert($contract->getAuthor() === null, "Contract author is null after removal");
    }

    private function testBidirectionalAssociation()
    {
        $user = new User();
        $contract = new SolanaContract();

        $user->addAuthoredContract($contract);
        $this->assert($contract->getAuthor() === $user, "Bidirectional: User adds contract, contract has author");
    }
}

// execute
(new UserTest())->run();

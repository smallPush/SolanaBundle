<?php

require_once __DIR__ . '/../../vendor/autoload.php';

// Stub missing Doctrine classes for this test execution environment
if (!interface_exists('Doctrine\Common\Collections\Collection')) {
    interface CollectionStub {
        public function contains($element);
        public function add($element);
        public function removeElement($element);
        public function isEmpty();
        public function count();
    }
    class_alias('CollectionStub', 'Doctrine\Common\Collections\Collection');
}

if (!class_exists('Doctrine\Common\Collections\ArrayCollection')) {
    class ArrayCollectionStub implements Doctrine\Common\Collections\Collection
    {
        private array $elements;
        public function __construct(array $elements = []) { $this->elements = $elements; }
        public function contains($element) { return in_array($element, $this->elements, true); }
        public function add($element) { $this->elements[] = $element; return true; }
        public function removeElement($element) { return false; } // simplified
        public function isEmpty() { return empty($this->elements); }
        public function count() { return count($this->elements); }
    }
    class_alias('ArrayCollectionStub', 'Doctrine\Common\Collections\ArrayCollection');
}

use App\Entity\SolanaContract;
use App\Entity\User;

// Helper to print test results
function assertTest($condition, $message) {
    if ($condition) {
        echo "[PASS] $message\n";
    } else {
        echo "[FAIL] $message\n";
        exit(1);
    }
}

echo "Running SolanaContractLogicTest...\n";

// Helper function to set private ID property via reflection
function setEntityId($entity, $id) {
    $reflection = new ReflectionClass($entity);
    $property = $reflection->getProperty('id');
    $property->setAccessible(true);
    $property->setValue($entity, $id);
}

// Setup
$donor = new User();
setEntityId($donor, 1);
$donor->setEmail('donor@example.com');
$donor->setRoles(['ROLE_DONOR']);

$volunteer = new User();
setEntityId($volunteer, 2);
$volunteer->setEmail('volunteer@example.com');
$volunteer->setRoles(['ROLE_VOLUNTEER']);

$otherUser = new User();
setEntityId($otherUser, 3);
$otherUser->setEmail('other@example.com');
$otherUser->setRoles(['ROLE_USER']);

$contract = new SolanaContract();
$contract->setDonor($donor);
$contract->setVolunteer($volunteer);

// Test Cases

// 1. Donor - Pending
$contract->setStatus(SolanaContract::STATUS_PENDING);
assertTest($contract->canBeValidatedBy($donor) === true, "Donor can validate when pending");

// 2. Donor - Validated Volunteer
$contract->setStatus(SolanaContract::STATUS_VALIDATED_VOLUNTEER);
assertTest($contract->canBeValidatedBy($donor) === true, "Donor can validate when validated_volunteer");

// 3. Donor - Validated Donor (already validated by them)
$contract->setStatus(SolanaContract::STATUS_VALIDATED_DONOR);
assertTest($contract->canBeValidatedBy($donor) === false, "Donor cannot validate when validated_donor");

// 4. Donor - Ready
$contract->setStatus(SolanaContract::STATUS_READY_FOR_SIGNATURE);
assertTest($contract->canBeValidatedBy($donor) === false, "Donor cannot validate when ready_for_signature");

// 5. Volunteer - Pending
$contract->setStatus(SolanaContract::STATUS_PENDING);
assertTest($contract->canBeValidatedBy($volunteer) === true, "Volunteer can validate when pending");

// 6. Volunteer - Validated Donor
$contract->setStatus(SolanaContract::STATUS_VALIDATED_DONOR);
assertTest($contract->canBeValidatedBy($volunteer) === true, "Volunteer can validate when validated_donor");

// 7. Volunteer - Validated Volunteer
$contract->setStatus(SolanaContract::STATUS_VALIDATED_VOLUNTEER);
assertTest($contract->canBeValidatedBy($volunteer) === false, "Volunteer cannot validate when validated_volunteer");

// 8. Other User - Pending
$contract->setStatus(SolanaContract::STATUS_PENDING);
assertTest($contract->canBeValidatedBy($otherUser) === false, "Other user cannot validate");

// 9. ID Comparison Test (Different Instances, Same ID)
$donorClone = new User();
setEntityId($donorClone, 1); // Same ID as $donor
$donorClone->setRoles(['ROLE_DONOR']);

$contract->setStatus(SolanaContract::STATUS_PENDING);
// Should pass due to ID comparison logic
assertTest($contract->canBeValidatedBy($donorClone) === true, "Donor instance with same ID can validate");

// 10. Missing Role Test
$donorNoRole = new User();
setEntityId($donorNoRole, 1);
$donorNoRole->setRoles([]); // No ROLE_DONOR
// Should fail because ROLE_DONOR is missing
assertTest($contract->canBeValidatedBy($donorNoRole) === false, "Donor without ROLE_DONOR cannot validate");

// 11. Null User Test
// Should return false for null user (anonymous)
assertTest($contract->canBeValidatedBy(null) === false, "Anonymous user cannot validate");

echo "All tests passed!\n";

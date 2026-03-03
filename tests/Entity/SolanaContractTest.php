<?php

namespace App\Tests\Entity;

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Entity\SolanaContract;

class SolanaContractTest
{
    public function testDefaultStatus(): void
    {
        $contract = new SolanaContract();
        if ($contract->getStatus() !== 'pending') {
            echo "Failed: default status is not pending.\n";
            exit(1);
        }
        echo "PASS: Default status is pending.\n";
    }
}

$test = new SolanaContractTest();
$test->testDefaultStatus();

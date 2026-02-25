<?php

namespace App\Tests\Entity;

use App\Entity\SolanaContract;
use PHPUnit\Framework\TestCase;

class SolanaContractTest extends TestCase
{
    public function testDefaultStatus(): void
    {
        $contract = new SolanaContract();
        $this->assertSame('pending', $contract->getStatus());
    }
}

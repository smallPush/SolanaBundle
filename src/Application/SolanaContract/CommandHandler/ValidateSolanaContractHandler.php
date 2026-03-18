<?php

namespace App\Application\SolanaContract\CommandHandler;

use App\Application\SolanaContract\Command\ValidateSolanaContractCommand;
use App\Contract\Cqrs\CommandHandlerInterface;
use App\Contract\Cqrs\CommandInterface;
use App\Entity\SolanaContract;
use Doctrine\ORM\EntityManagerInterface;

class ValidateSolanaContractHandler implements CommandHandlerInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(CommandInterface $command): void
    {
        if (!$command instanceof ValidateSolanaContractCommand) {
            throw new \InvalidArgumentException('Expected ValidateSolanaContractCommand');
        }

        $contract = $command->getContract();
        $validator = $command->getValidator();
        $status = $contract->getStatus();

        $isDonor = ($validator === $contract->getDonor());
        $isVolunteer = ($validator === $contract->getVolunteer());

        $newStatus = null;

        if ($isDonor && $status === SolanaContract::STATUS_PENDING) {
            $newStatus = SolanaContract::STATUS_VALIDATED_DONOR;
        } elseif ($isVolunteer && $status === SolanaContract::STATUS_PENDING) {
            $newStatus = SolanaContract::STATUS_VALIDATED_VOLUNTEER;
        } elseif ($isDonor && $status === SolanaContract::STATUS_VALIDATED_VOLUNTEER) {
            $newStatus = SolanaContract::STATUS_READY_FOR_SIGNATURE;
        } elseif ($isVolunteer && $status === SolanaContract::STATUS_VALIDATED_DONOR) {
            $newStatus = SolanaContract::STATUS_READY_FOR_SIGNATURE;
        }

        if ($newStatus) {
            $contract->setStatus($newStatus);
            $this->entityManager->flush();
        } else {
            throw new \DomainException('Cannot validate contract in current status or invalid permissions for transition.');
        }
    }
}

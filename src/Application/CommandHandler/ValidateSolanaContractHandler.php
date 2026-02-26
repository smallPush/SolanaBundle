<?php

namespace App\Application\CommandHandler;

use App\Application\Command\ValidateSolanaContractCommand;
use App\Contract\Cqrs\CommandHandlerInterface;
use App\Contract\Cqrs\CommandInterface;
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

        if ($isDonor && $status === 'pending') {
            $newStatus = 'validated_donor';
        } elseif ($isVolunteer && $status === 'pending') {
            $newStatus = 'validated_volunteer';
        } elseif ($isDonor && $status === 'validated_volunteer') {
            $newStatus = 'ready_for_signature';
        } elseif ($isVolunteer && $status === 'validated_donor') {
            $newStatus = 'ready_for_signature';
        }

        if ($newStatus) {
            $contract->setStatus($newStatus);
            $this->entityManager->flush();
        } else {
            throw new \DomainException('Cannot validate contract in current status or invalid permissions for transition.');
        }
    }
}

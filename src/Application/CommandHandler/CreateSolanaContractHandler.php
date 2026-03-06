<?php

namespace App\Application\CommandHandler;

use App\Application\Command\CreateSolanaContractCommand;
use App\Contract\Cqrs\CommandHandlerInterface;
use App\Contract\Cqrs\CommandInterface;
use App\Entity\SolanaContract;
use Doctrine\ORM\EntityManagerInterface;

class CreateSolanaContractHandler implements CommandHandlerInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(CommandInterface $command): void
    {
        if (!$command instanceof CreateSolanaContractCommand) {
            throw new \InvalidArgumentException('Expected CreateSolanaContractCommand');
        }

        $contract = $command->getContract();
        $author = $command->getAuthor();

        $contract->setAuthor($author);
        $contract->setStatus(SolanaContract::STATUS_PENDING);

        $this->entityManager->persist($contract);
        $this->entityManager->flush();
    }
}

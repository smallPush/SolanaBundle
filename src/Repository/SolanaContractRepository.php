<?php

namespace App\Repository;

use App\Entity\SolanaContract;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SolanaContract>
 *
 * @method SolanaContract|null find($id, $lockMode = null, $lockVersion = null)
 * @method SolanaContract|null findOneBy(array $criteria, array $orderBy = null)
 * @method SolanaContract[]    findAll()
 * @method SolanaContract[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SolanaContractRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SolanaContract::class);
    }

    public function findWithRelations(int $id): ?SolanaContract
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.author', 'a')
            ->addSelect('a')
            ->leftJoin('s.donor', 'd')
            ->addSelect('d')
            ->leftJoin('s.volunteer', 'v')
            ->addSelect('v')
            ->andWhere('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}

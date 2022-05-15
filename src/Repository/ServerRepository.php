<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Server;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Server|null find($id, $lockMode = null, $lockVersion = null)
 * @method Server|null findOneBy(array $criteria, array $orderBy = null)
 * @method Server[]    findAll()
 * @method Server[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Server::class);
    }

    /**
     * @return Server[]
     */
    public function findAllStartedAndSynchronizedServerOlderThan24Hours(): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.started < :date')
            ->andWhere('s.synchronized = :true')
            ->setParameter('true', true)
            ->setParameter('date', new DateTime('-1 days'))
            ->getQuery()
            ->getResult()
        ;
    }
}

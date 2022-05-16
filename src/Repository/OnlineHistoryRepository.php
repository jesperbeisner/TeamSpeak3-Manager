<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\OnlineHistory;
use App\Entity\Server;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OnlineHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method OnlineHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method OnlineHistory[]    findAll()
 * @method OnlineHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OnlineHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OnlineHistory::class);
    }

    public function findStartedOnlineHistoryByServerAndUuid(Server $server, string $uuid): ?OnlineHistory
    {
        return $this->createQueryBuilder('oh')
            ->where('oh.server = :server')
            ->andWhere('oh.uuid = :uuid')
            ->andWhere('oh.endSession IS null')
            ->setParameter('server', $server)
            ->setParameter('uuid', $uuid)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}

<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Server;
use App\Entity\ServerHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ServerHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServerHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServerHistory[]    findAll()
 * @method ServerHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServerHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServerHistory::class);
    }

    public function findLastServerStartedHistory(Server $server): ?ServerHistory
    {
        /** @var ServerHistory|null $result */
        $result = $this->createQueryBuilder('sh')
            ->where('sh.server = :server')
            ->andWhere('sh.action = :action')
            ->orderBy('sh.id', 'DESC')
            ->setMaxResults(1)
            ->setParameter('server', $server)
            ->setParameter('action', 'started')
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $result;
    }
}

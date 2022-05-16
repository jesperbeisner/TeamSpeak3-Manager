<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Live;
use App\Entity\Server;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Live|null find($id, $lockMode = null, $lockVersion = null)
 * @method Live|null findOneBy(array $criteria, array $orderBy = null)
 * @method Live[]    findAll()
 * @method Live[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LiveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Live::class);
    }

    public function removeAllByServer(Server $server): void
    {
        $this->createQueryBuilder('l')
            ->delete()
            ->where('l.server = :server')
            ->setParameter('server', $server)
            ->getQuery()
            ->execute()
        ;
    }
}

<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\OnlineHistory;
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
}

<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\KeyHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method KeyHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method KeyHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method KeyHistory[]    findAll()
 * @method KeyHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class KeyHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, KeyHistory::class);
    }
}

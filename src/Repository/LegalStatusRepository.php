<?php

namespace App\Repository;

use App\Entity\LegalStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LegalStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method LegalStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method LegalStatus[]    findAll()
 * @method LegalStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LegalStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LegalStatus::class);
    }

    // /**
    //  * @return LegalStatus[] Returns an array of LegalStatus objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LegalStatus
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

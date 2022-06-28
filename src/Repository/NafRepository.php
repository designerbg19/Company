<?php

namespace App\Repository;

use App\Entity\Naf;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Naf|null find($id, $lockMode = null, $lockVersion = null)
 * @method Naf|null findOneBy(array $criteria, array $orderBy = null)
 * @method Naf[]    findAll()
 * @method Naf[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NafRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Naf::class);
    }

    // /**
    //  * @return Naf[] Returns an array of Naf objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Naf
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

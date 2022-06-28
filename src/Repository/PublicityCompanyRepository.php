<?php

namespace App\Repository;

use App\Entity\PublicityCompany;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PublicityCompany|null find($id, $lockMode = null, $lockVersion = null)
 * @method PublicityCompany|null findOneBy(array $criteria, array $orderBy = null)
 * @method PublicityCompany[]    findAll()
 * @method PublicityCompany[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublicityCompanyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PublicityCompany::class);
    }
    public function findAllPublicityByCompany($value)
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.companies ', 'c')
            ->andWhere('c.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return PublicityCompany[] Returns an array of PublicityCompany objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PublicityCompany
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

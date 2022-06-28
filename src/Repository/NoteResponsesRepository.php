<?php

namespace App\Repository;

use App\Entity\NoteResponses;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NoteResponses|null find($id, $lockMode = null, $lockVersion = null)
 * @method NoteResponses|null findOneBy(array $criteria, array $orderBy = null)
 * @method NoteResponses[]    findAll()
 * @method NoteResponses[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NoteResponsesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NoteResponses::class);
    }
    public function findLNoteResponsesByUser($value)
    {
        return $this->createQueryBuilder('u')
            ->innerJoin('u.respondTo', 'r')
            ->select('r.id', 'r.username','r.email')
            ->andWhere('u.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult();
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

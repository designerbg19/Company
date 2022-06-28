<?php

namespace App\Repository;

use App\Entity\Note;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Note|null find($id, $lockMode = null, $lockVersion = null)
 * @method Note|null findOneBy(array $criteria, array $orderBy = null)
 * @method Note[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }
    public function findAll()
    {
        return $this->findBy(array(), array('createdAt' => 'DESC'));
    }
    public function findNoteByUser($user,$company)
    {
        $from = new \DateTime();
        $to   = new \DateTime('-6 month');
        $query =$this->createQueryBuilder('n')
            ->innerJoin('n.user ', 'u')
            ->innerJoin('n.company ', 'c')
            ->andWhere('u.id = :user')
            ->andWhere('c.id = :company')
            ->andWhere('n.createdAt < :from')
            ->andWhere('n.createdAt > :to')
            ->setParameter('from', $from )
            ->setParameter('to', $to)
            ->setParameter('user', $user)
            ->setParameter('company', $company)
            ->getQuery()
            ->getResult();
        return $query;
    }
    public function findNotatonByNote($value)
    {
        return $this->createQueryBuilder('n')
            ->innerJoin('n.noteNotation ', 'p')
            ->innerJoin('p.notation ', 'q')
            ->select('p.id','p.value','q.name','q.id as notation_id')
            ->andWhere('p.note = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult();
    }
    public function findQuestionnaireByNote($value)
    {
        return $this->createQueryBuilder('n')
            ->innerJoin('n.noteQuestionnaire ', 'p')
            ->innerJoin('p.questionnaire ', 'q')
            ->select('p.id','p.value','q.name','q.id as questionnaire_id')
            ->andWhere('p.note = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult();
    }
    public function findResponseByNote($value)
    {
        return $this->createQueryBuilder('n')
            ->innerJoin('n.noteResponses ', 'r')
            ->select('r.id','r.message')
            ->andWhere('r.note = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult();
    }
    public function findNoteByCompany($value)
    {
        return $this->createQueryBuilder('n')
            ->innerJoin('n.company ', 'c')
            ->innerJoin('n.profile', 'p')
            ->innerJoin('p.image', 'i')
            ->innerJoin('n.user', 'u')
            ->select('n.id','n.score','n.createdAt','n.description','p.name as profile','p.id as profileId','u.id as userId','u.username','u.email','i.name as image')
            ->andWhere('c.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Note[] Returns an array of Note objects
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
    public function findOneBySomeField($value): ?Note
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

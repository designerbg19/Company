<?php

namespace App\Repository;

use App\Entity\Company;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Company|null find($id, $lockMode = null, $lockVersion = null)
 * @method Company|null findOneBy(array $criteria, array $orderBy = null)
 * @method Company[]    findAll()
 * @method Company[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Company::class);
    }

    public function findCalendarByCompany($value)
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.calendar ', 'p')
            ->select('p.id', 'p.day','p.start','p.end')
            ->andWhere('c.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult();
    }
    public function findStatusByCompany($value)
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.legalStatus ', 's')
            ->select('s.id', 's.name','s.country')
            ->andWhere('c.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult();
    }

    public function findMacaronByCompany($value)
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.macarons ', 'm')
            ->select('m.id', 'm.name','m.published')
            ->andWhere('c.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult();
    }
    public function findTagsByCompany($value)
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.tags ', 't')
            ->select('t.id', 't.name')
            ->andWhere('c.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult();
    }

    public function findSectorsByCompany($value)
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.sectors ', 's')
            ->select('s.id', 's.name')
            ->andWhere('c.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult();
    }

    public function findCompanyBySectors($value)
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.sectors ', 's')
            ->andWhere('s.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult();
    }

    public function findCompanyByTags($value)
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.tags ', 's')
            ->andWhere('s.id IN (:val)')
            ->setParameter('val', array($value))
            ->getQuery()
            ->getResult();
    }

    public function findCompanyByName($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.name LIKE :val')
            ->setParameter('val', "%{$value}%")
            ->getQuery()
            ->getResult();
    }

    public function findCompanyByPlace($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.city LIKE :val OR c.postalCode LIKE :val')
            ->setParameter('val', "%{$value}%")
            ->getQuery()
            ->getResult();
    }

    public function findCompanyFilter($name, $sector, $tag, $place)
    {
        $sqlJoin = '';
        $where = '';
        if ($name != null) {
            $where .= " AND c.name LIKE :name OR c.siren LIKE :name OR c.siret LIKE :name";
        }
        if ($sector != null) {
            $sqlJoin .= ' INNER JOIN company_sector s ON c.id = s.company_id ';
            $where .= " AND s.sector_id = :sector";
        }
        if ($tag != null) {
            $sqlJoin .= ' INNER JOIN company_tag t ON c.id = t.company_id ';
            $where .= " AND t.tag_id IN (:tag)";
        }
        if($place != null) {
            $where .= " AND c.city LIKE :place OR c.postal_code LIKE :place";
        }
        $em = $this->getEntityManager();

        $query = 'SELECT c.* FROM company c LEFT JOIN note n ON n.company_id = c.id' .
            $sqlJoin . ' WHERE c.id IS NOT NULL ' . $where . ' ORDER BY n.score DESC ;';
        $statement = $em->getConnection()->prepare($query);

        if (!$tag == null) {
            $statement->bindValue('tag', $tag);
        }
        if (!$name == null) {
            $statement->bindValue('name', "%{$name}%");
        }
        if (!$sector == null) {
            $statement->bindValue('sector', $sector);
        }
        if (!$place == null) {
            $statement->bindValue('place', "%{$place}%");
        }
        $execute = $statement->execute();
        return $execute->fetchAll();
    }

    public function findCompanyByFilter($name, $sector, $tag, $city)
    {
        if (!$name && !$sector && !$tag && !$city) {
            return $this->createQueryBuilder('c')
                ->getQuery()
                ->getResult();
        } else {
            $query = $this->createQueryBuilder('c');
            if ($name) {
                $query->andWhere('c.name LIKE :name');
                $query->setParameter('name', '%' . $name . '%');
            }
            if ($sector) {
                $query->innerJoin('c.sectors ', 's');
                $query->andWhere('s.id = :sector');
                $query->setParameter('sector', $sector);
            }
            if ($tag) {
                $query->innerJoin('c.tags ', 't');
                $query->andWhere('t.name LIKE :tag');
                $query->setParameter('tag', '%' . $tag . '%');
            }
            if ($city) {
                $query->andWhere('c.city LIKE :city');
                $query->setParameter('city', '%' . $city . '%');
            }
            $result = $query->getQuery()
                ->getResult();
        }
        return $result;
    }
    public function findUserByCompany($value)
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.user ', 'u')
            ->select('u.id', 'u.username','u.email')
            ->andWhere('c.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult();
    }

    public function findCompanyPlusFilter($country, $macarons, $ids)
    {
        if (!$country && !$macarons && !$ids) {
            return $this->createQueryBuilder('c')
                ->getQuery()
                ->getResult();
        } else {
            $query = $this->createQueryBuilder('c');
            if ($country) {
                $query->andWhere('c.country LIKE :country');
                $query->setParameter('country', '%' . $country . '%');
            }
            $macaron = explode(',', $macarons);
            if ($macarons) {
                $query->innerJoin('c.macarons', 'm');
                $query->andWhere('m.id in (:macaron)');
                $query->setParameter(':macaron', array_values($macaron));
            }
            if ($ids) {
                $query->andWhere('c.id in (:id)');
                $query->setParameter(':id', array_values($ids));
            }
            $result = $query->getQuery()
                ->getResult();
        }
        return $result;
    }

    // /**
    //  * @return Company[] Returns an array of Company objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Company
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
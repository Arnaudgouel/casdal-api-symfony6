<?php

namespace App\Repository;

use App\Entity\Company;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
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

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Company $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Company $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
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


    /**
     * @return Company[] Returns an array of Company objects
     */

    public function findAllActiveQB()
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.deactivatedAt IS NULL')
            ->orderBy('c.companyCategoryId', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Company[] Returns an array of Company objects
     */

    public function findAllActive()
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT c.*, cc.title as "category" FROM company c
            JOIN company_category cc ON cc.id = c.company_category_id
            WHERE c.deactivated_at IS NULL
            AND c.owner_id IS NOT NULL
        ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    /**
     * @return Company[] Returns an array of Company objects
     */

    public function findOneActive($id)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT c.*, cc.title as "category" FROM company c
            JOIN company_category cc ON cc.id = c.company_category_id
            WHERE c.deactivated_at IS NULL
            AND c.owner_id IS NOT NULL
            AND c.id = :id
        ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery([
            'id' => $id
        ]);
        return $resultSet->fetchAssociative();
    }

    /**
     * @return Company[] Returns an array of Company objects
     */

    public function findAllActiveInCategory($cat)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT c.*, cc.title as "category" FROM company c
            JOIN company_category cc ON cc.id = c.company_category_id
            WHERE c.deactivated_at IS NULL
            AND c.company_category_id = :cat
            AND c.owner_id IS NOT NULL
        ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery([
            'cat' => $cat
        ]);
        return $resultSet->fetchAllAssociative();
    }

    /**
     * @return Company[] Returns an array of Company objects
     */

    public function findAllActiveWithSearch($search)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT c.*, cc.title as "category" FROM company c
            JOIN company_category cc ON cc.id = c.company_category_id
            JOIN "user" u ON u.id = c.owner_id
            WHERE c.deactivated_at IS NULL
            AND UPPER(c.name) LIKE UPPER(:search)
            AND c.owner_id IS NOT NULL
        ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery([
            'search' => '%' . $search . '%'
        ]);
        return $resultSet->fetchAllAssociative();
    }

    /**
     * @return Company[] Returns an array of Company objects
     */

    public function findAllActiveCompaniesManagedByUser($userId)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT c.*, cc.title as "category" FROM company c
            JOIN company_category cc ON cc.id = c.company_category_id
            JOIN "user" u ON u.id = c.owner_id
            WHERE c.deactivated_at IS NULL
            AND c.owner_id = :userId
        ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery([
            'userId' => $userId
        ]);
        return $resultSet->fetchAllAssociative();
    }

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

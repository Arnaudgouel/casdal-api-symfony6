<?php

namespace App\Repository;

use App\Entity\CompanyAddress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CompanyAddress|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompanyAddress|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompanyAddress[]    findAll()
 * @method CompanyAddress[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyAddressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompanyAddress::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(CompanyAddress $entity, bool $flush = true): void
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
    public function remove(CompanyAddress $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return CompanyAddress[] Returns an array of CompanyAddress objects
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
      * @return CompanyAddress[] Returns an array of CompanyAddress objects
      */
    
    public function findByCompany($companyId)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT ca.* FROM company_address ca
            WHERE ca.company_id = :companyId
        ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery([
            'companyId' => $companyId
        ]);
        return $resultSet->fetchAssociative();
    }
    

    /*
    public function findOneBySomeField($value): ?CompanyAddress
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

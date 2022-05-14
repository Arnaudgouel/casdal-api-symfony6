<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Product $entity, bool $flush = true): void
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
    public function remove(Product $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @return Product[] Returns an array of Product objects
     */

    public function findAllActiveProductsInCompanyOrderByCategory($companyId)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT p.*, pc.name as "category_name" FROM product p
            JOIN product_category pc ON pc.id = p.product_category_id
            WHERE p.deactivated_at IS NULL
            AND p.company_id = :companyId
            ORDER BY p.product_category_id ASC
        ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery([
            'companyId' => $companyId
        ]);
        return $resultSet->fetchAllAssociative();
    }

    /**
     * @return Product[] Returns an array of Product objects
     */

    public function findAllActiveProductsByCompanyByCategory($companyId, $categoryId)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT p.*, pc.name as "category_name" FROM product p
            JOIN product_category pc ON pc.id = p.product_category_id
            WHERE p.deactivated_at IS NULL
            AND p.company_id = :companyId
            AND pc.id = :categoryId
            ORDER BY p.product_category_id ASC
        ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery([
            'companyId' => $companyId,
            'categoryId' => $categoryId
        ]);
        return $resultSet->fetchAllAssociative();
    }

    /**
     * @return Product[] Returns an array of Product objects
     */

    public function findMostSoldProductsForOneCompany($companyId)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT SUM(oi.quantity) ventes, p.* FROM product p
            JOIN order_item oi ON oi.product_id = p.id
            JOIN "order" o ON o.id = oi.order_id
            WHERE o.deactivated_at IS NULL
            AND p.company_id = :companyId
            GROUP BY p.id
            ORDER BY ventes DESC
            LIMIT 5
        ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery([
            'companyId' => $companyId
        ]);
        return $resultSet->fetchAllAssociative();
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
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
    public function findOneBySomeField($value): ?Product
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

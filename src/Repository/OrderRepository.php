<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Order $entity, bool $flush = true): void
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
    public function remove(Order $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @return Order[] Returns an array of Order objects
     */
    public function findOrdersByCompany($companyId)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT Distinct(o.*), SUM(oi.quantity) product_count FROM "order" o
            JOIN order_item oi ON oi.order_id = o.id
            JOIN product p ON p.id = oi.product_id
            JOIN company c ON c.id = p.company_id
            WHERE o.deactivated_at IS NULL
            AND p.company_id = :companyId
            AND c.owner_id IS NOT NULL
            GROUP BY o.id
        ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery([
            'companyId' => $companyId
        ]);
        return $resultSet->fetchAllAssociative();
    }

    /**
     * @return Order[] Returns an array of Order objects
     */
    public function findOrdersByUser($userId)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT DISTINCT o.*, c2.name FROM "order" o
            LEFT JOIN "user" u ON u.id = o.user_id
            LEFT JOIN order_item oi ON oi.order_id = o.id
            LEFT JOIN product p ON p.id = oi.product_id
            LEFT JOIN 
            (
                SELECT DISTINCT c.name, c.id
                FROM company c
            )
            c2 ON c2.id = p.company_id
            WHERE o.deactivated_at IS NULL
            AND u.id = :userId
            ORDER BY o.created_at DESC
        ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery([
            'userId' => $userId
        ]);
        return $resultSet->fetchAllAssociative();
    }

    // /**
    //  * @return Order[] Returns an array of Order objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Order
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

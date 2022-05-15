<?php

namespace App\Repository;

use App\Entity\CartItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CartItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method CartItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method CartItem[]    findAll()
 * @method CartItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CartItem::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(CartItem $entity, bool $flush = true): void
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
    public function remove(CartItem $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return CartItem[] Returns an array of CartItem objects
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
     * @return CartItem[] Returns an array of CartItem objects
     */
    public function findByCartUser($userId)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT ci.*, p.name, p.price FROM cart_item ci
            JOIN product p ON p.id = ci.product_id
            WHERE ci.user_id = :userId
        ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery([
            'userId' => $userId
        ]);
        return $resultSet->fetchAllAssociative();
    }

    public function findTotalCart($userId)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT SUM(p.price * ci.quantity) price, SUM(ci.quantity) quantity FROM cart_item ci
            JOIN product p ON p.id = ci.product_id
            WHERE ci.user_id = :userId
            GROUP BY ci.user_id
        ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery([
            'userId' => $userId
        ]);
        return $resultSet->fetchAssociative();
    }

    public function findOneCartItem($productId, $userId)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT ci.* FROM cart_item ci
            JOIN product p ON p.id = ci.product_id
            WHERE ci.user_id = :userId
            AND p.id = :productId
        ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery([
            'productId' => $productId,
            'userId' => $userId
        ]);
        return $resultSet->fetchAssociative();
    }

    /**
     * @return CartItem[] Returns an array of CartItem objects
     */
    public function findItemsOfOtherCompanies($userId, $companyId)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT ci.* FROM cart_item ci
            JOIN product p ON p.id = ci.product_id
            WHERE ci.user_id = :userId
            AND p.company_id != :companyId
        ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery([
            'userId' => $userId,
            'companyId' => $companyId,
        ]);
        return $resultSet->rowCount() == 0;
    }

    /*
    public function findOneBySomeField($value): ?CartItem
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

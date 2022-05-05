<?php

namespace App\Repository;

use App\Entity\UserAddress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserAddress|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserAddress|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserAddress[]    findAll()
 * @method UserAddress[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserAddressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserAddress::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(UserAddress $entity, bool $flush = true): void
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
    public function remove(UserAddress $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return UserAddress[] Returns an array of UserAddress objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /**
     * @return UserAddress[] Returns an array of UserAddress objects
     */
    
    public function findAllByUser($userId)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT ua.* FROM user_address ua
            WHERE ua.user_id = :userId
            AND ua.deactivated_at IS NULL
        ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery([
            'userId' => $userId
        ]);
        return $resultSet->fetchAllAssociative();
    }

    /**
     * @return UserAddress Returns an array of UserAddress objects
     */
    
    public function findLastByUser($userId)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT ua.* FROM user_address ua
            WHERE ua.user_id = :userId
            AND ua.deactivated_at IS NULL
            ORDER BY ua.selected_at DESC
            LIMIT 1
        ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery([
            'userId' => $userId
        ]);
        return $resultSet->fetchAssociative();
    }

    public function insert($userId, $name, $addressLine1, $addressLine2, $city, $postalCode, $country, $phoneNumber) :bool
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
        INSERT INTO
            user_address 
            (user_id, name, address_line1, address_line2, city, postal_code, country, phone_number)
        VALUES
            (
                :userId,
                :name,
                :address_line1,
                :address_line2,
                :city,
                :postal_code,
                :country,
                :phone_number,
            ),
        ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery([
            'userId' => $userId,
            'name' => $name,
            'address_line1' => $addressLine1,
            'address_line2' => $addressLine2,
            'city' => $city,
            'postal_code' => $postalCode,
            'country' => $country,
            'phone_number' => $phoneNumber,
        ]);
        return $resultSet->rowCount() == 1;
    }

    /*
    public function findOneBySomeField($value): ?UserAddress
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

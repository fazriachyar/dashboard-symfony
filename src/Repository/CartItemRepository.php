<?php

namespace App\Repository;

use App\Entity\CartItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CartItem>
 *
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

    public function save(CartItem $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CartItem $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findCartByCustomerId($customerId): array
    {
        $sql = "
            SELECT
                cart_item.id,
                cart_item.product_quantity as quantity,
                cart_info.customer_id as customerId,
                product.name as productName,
                product.price * cart_item.product_quantity as price,
                cart_item.add_time as addTime
            FROM
                cart_item
            LEFT JOIN cart_info ON cart_item.cart_info_id = cart_info.id
            LEFT JOIN product ON product.id = cart_item.product_id
            WHERE
                cart_info.customer_id = ".$customerId." AND
                cart_info.action != 'D' AND
                cart_item.action != 'D'
            ORDER BY cart_item.id DESC
        ";

        return $this->getEntityManager()->getConnection()->prepare($sql)->execute()->fetchAll();
    }

    public function removeCartItem($cartInfoId)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
            UPDATE
                cart_item
            SET
                cart_item.action = 'D',
                cart_item.add_time = now()
            WHERE
                cart_item.cart_info_id = ".$cartInfoId." AND
                cart_item.action != 'D'
        ";

        return $conn->prepare($sql)->executeQuery();
    }

//    /**
//     * @return CartItem[] Returns an array of CartItem objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CartItem
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

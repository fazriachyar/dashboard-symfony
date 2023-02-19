<?php

namespace App\Repository;

use App\Entity\CheckoutInfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CheckoutInfo>
 *
 * @method CheckoutInfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method CheckoutInfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method CheckoutInfo[]    findAll()
 * @method CheckoutInfo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CheckoutInfoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CheckoutInfo::class);
    }

    public function save(CheckoutInfo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CheckoutInfo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findCheckoutInfoHistoryByCustomerId($customerId): array
    {
        $sql = "
            SELECT
                checkout_info.id,
                checkout_info.customer_id as customerId,
                checkout_info.customer_address as customerAddress,
                checkout_info.add_time as transactionDate,
                checkout_info.cart_info_id as cartId,
                checkout_info.order_note as orderNote,
                checkout_info.status,
                checkout_info.order_id as orderId,
                checkout_info.payment_code as paymentCode,
                cart_info.price as totalPrice
            FROM
                checkout_info
            LEFT JOIN (
                SELECT
                    cart_info.id,
                    cart_item.price
                FROM
                    cart_info
                LEFT JOIN (
                    SELECT
                        cart_item.cart_info_id as id,
                        SUM(product.price * cart_item.product_quantity) as price
                    FROM
                        cart_item
                    LEFT JOIN product ON cart_item.product_id = product.id
                    WHERE
                        cart_item.action != 'D'
                    GROUP BY cart_item.cart_info_id
                ) cart_item ON cart_item.id = cart_info.id
                WHERE
                    cart_info.action != 'D'
                ) cart_info ON cart_info.id = checkout_info.cart_info_id
            WHERE
                checkout_info.customer_id = ".$customerId."
            ORDER BY checkout_info.add_time DESC
        ";
        return $this->getEntityManager()->getConnection()->prepare($sql)->execute()->fetchAll();
    }

    public function findCheckoutInfoByCustomerId($customerId): array
    {
        $sql = "
            SELECT
                checkout_info.id,
                checkout_info.customer_id as customerId,
                checkout_info.customer_address as customerAddress,
                checkout_info.add_time as transactionDate,
                checkout_info.cart_info_id as cartId,
                checkout_info.order_note as orderNote,
                checkout_info.status,
                checkout_info.order_id as orderId,
                checkout_info.payment_code as paymentCode,
                cart_info.price as totalPrice
            FROM
                checkout_info
            LEFT JOIN (
                SELECT
                cart_info.id,
                cart_item.price,
                cart_info.action
                FROM
                cart_info
                LEFT JOIN (
                    SELECT
                    cart_item.cart_info_id as id,
                    SUM(product.price * cart_item.product_quantity) as price
                    FROM
                    cart_item
                    LEFT JOIN product ON cart_item.product_id = product.id
                    WHERE
                    cart_item.action != 'D'
                    GROUP BY cart_item.cart_info_id
                ) cart_item ON cart_item.id = cart_info.id
                WHERE
                    cart_info.action != 'D'
                ) cart_info ON cart_info.id = checkout_info.cart_info_id
            WHERE
                checkout_info.action != 'D' AND
                cart_info.action != 'D' AND
                checkout_info.customer_id = 2
            ORDER BY checkout_info.add_time DESC
        ";
        return $this->getEntityManager()->getConnection()->prepare($sql)->execute()->fetchAll();
    }

//    /**
//     * @return CheckoutInfo[] Returns an array of CheckoutInfo objects
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

//    public function findOneBySomeField($value): ?CheckoutInfo
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

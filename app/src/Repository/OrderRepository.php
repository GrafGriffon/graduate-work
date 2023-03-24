<?php

namespace App\Repository;

use App\Entity\Order;
use DateInterval;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
     * @return int|mixed|string
     */
    public function getOrdersListSortedByDate(?int $status = null, ?string $start, ?string $end)
    {
        $query = $this->createQueryBuilder('o');
        if ($status != null) {
            $query->andWhere('o.status = :status');
            $query->setParameter('status', $status);
        }
        if ($start) {
            $query->andWhere('o.date >= :start');
            $query->setParameter('start', new \DateTime($start));
        }
        if ($end) {
            $query->andWhere('o.date < :end');
            $query->setParameter('end', (new \DateTime($end))->add(DateInterval::createFromDateString('1 day')));
        }
        return $query
            ->orderBy('o.date', 'DESC')
            ->getQuery()
            ->getResult();
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

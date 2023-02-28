<?php

namespace App\Repository;

use App\Entity\Conscript;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Conscript|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conscript|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conscript[]    findAll()
 * @method Conscript[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConscriptRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conscript::class);
    }

    // /**
    //  * @return Conscript[] Returns an array of Conscript objects
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
    public function findOneBySomeField($value): ?Conscript
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

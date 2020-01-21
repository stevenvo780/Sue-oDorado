<?php

namespace App\Repository;

use App\Entity\MonedaApoyo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method MonedaApoyo|null find($id, $lockMode = null, $lockVersion = null)
 * @method MonedaApoyo|null findOneBy(array $criteria, array $orderBy = null)
 * @method MonedaApoyo[]    findAll()
 * @method MonedaApoyo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MonedaApoyoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MonedaApoyo::class);
    }

    // /**
    //  * @return MonedaApoyo[] Returns an array of MonedaApoyo objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MonedaApoyo
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

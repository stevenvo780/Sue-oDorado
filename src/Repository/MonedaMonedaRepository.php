<?php

namespace App\Repository;

use App\Entity\MonedaMoneda;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method MonedaMoneda|null find($id, $lockMode = null, $lockVersion = null)
 * @method MonedaMoneda|null findOneBy(array $criteria, array $orderBy = null)
 * @method MonedaMoneda[]    findAll()
 * @method MonedaMoneda[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MonedaMonedaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MonedaMoneda::class);
    }

    // /**
    //  * @return MonedaMoneda[] Returns an array of MonedaMoneda objects
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
    public function findOneBySomeField($value): ?MonedaMoneda
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

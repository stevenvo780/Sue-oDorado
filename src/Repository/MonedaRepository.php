<?php

namespace App\Repository;

use App\Entity\Moneda;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Moneda|null find($id, $lockMode = null, $lockVersion = null)
 * @method Moneda|null findOneBy(array $criteria, array $orderBy = null)
 * @method Moneda[]    findAll()
 * @method Moneda[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MonedaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Moneda::class);
    }

    // /**
    //  * @return Moneda[] Returns an array of Moneda objects
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
    public function findOneBySomeField($value): ?Moneda
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

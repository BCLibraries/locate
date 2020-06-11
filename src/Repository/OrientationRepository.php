<?php

namespace App\Repository;

use App\Entity\Orientation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Orientation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Orientation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Orientation[]    findAll()
 * @method Orientation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrientationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Orientation::class);
    }

    // /**
    //  * @return Orientation[] Returns an array of Orientation objects
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
    public function findOneBySomeField($value): ?Orientation
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

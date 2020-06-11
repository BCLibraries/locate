<?php

namespace App\Repository;

use App\Entity\MapImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method MapImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method MapImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method MapImage[]    findAll()
 * @method MapImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MapImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MapImage::class);
    }

    // /**
    //  * @return MapImage[] Returns an array of MapImage objects
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
    public function findOneBySomeField($value): ?MapImage
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

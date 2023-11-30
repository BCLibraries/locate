<?php

namespace App\Repository;

use App\Entity\Shelf;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Shelf|null find($id, $lockMode = null, $lockVersion = null)
 * @method Shelf|null findOneBy(array $criteria, array $orderBy = null)
 * @method Shelf[]    findAll()
 * @method Shelf[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShelfRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Shelf::class);
    }

    public function findOneByLibraryAndCallNumber(string $library_code, string $normalized_call_number): ?Shelf
    {
        return $this->getEntityManager()->createQuery(
            'SELECT s 
                FROM App\Entity\Shelf s
                LEFT JOIN s.map m
                LEFT JOIN m.library l
                WHERE l.code = :library_code
                AND s.start_sort_call_number <= :normalized_call_number
                AND s.end_sort_call_number >= :normalized_call_number')
            ->setParameter('library_code', $library_code)
            ->setParameter('normalized_call_number', $normalized_call_number)
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }
}

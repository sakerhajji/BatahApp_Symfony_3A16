<?php

namespace App\Repository;

use App\Entity\ReservationLocation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReservationLocation>
 *
 * @method ReservationLocation|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReservationLocation|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReservationLocation[]    findAll()
 * @method ReservationLocation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationLocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReservationLocation::class);
    }

//    /**
//     * @return ReservationLocation[] Returns an array of ReservationLocation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ReservationLocation
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
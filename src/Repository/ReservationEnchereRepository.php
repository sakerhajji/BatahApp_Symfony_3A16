<?php

namespace App\Repository;

use App\Entity\ReservationEnchere;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReservationEnchere>
 *
 * @method ReservationEnchere|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReservationEnchere|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReservationEnchere[]    findAll()
 * @method ReservationEnchere[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationEnchereRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReservationEnchere::class);
    }

//    /**
//     * @return ReservationEnchere[] Returns an array of ReservationEnchere objects
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

//    public function findOneBySomeField($value): ?ReservationEnchere
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

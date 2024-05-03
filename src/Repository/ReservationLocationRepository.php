<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Location;
use App\Entity\Utilisateur;
use App\Entity\ReservationLocation;




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
    public function findReservation(int $userId, int $locationId): array
    {
        return $this->createQueryBuilder('r')
            ->innerJoin('r.location', 'l') // Use 'location' instead of 'location2'
            ->innerJoin('l.id', 'u') // Assuming 'id' is the user ID in the Location entity
            ->andWhere('u.id = :userId')
            ->andWhere('l.id = :locationId')
            ->setParameter('userId', $userId)
            ->setParameter('locationId', $locationId)
            ->getQuery()
            ->getResult();
    }
    public function findReservationsForUser(int $userId): array
{
    return $this->createQueryBuilder('r')
        ->join('r.location', 'l')
        ->andWhere('l.id = :userId')
        ->setParameter('userId', $userId)
        ->getQuery()
        ->getResult();
}

public function findReservationsForLocations(array $locationIds): array
{
    return $this->createQueryBuilder('r')
        ->innerJoin('r.location', 'l')
        ->andWhere('l.id IN (:locationIds)')
        ->setParameter('locationIds', $locationIds)
        ->getQuery()
        ->getResult();
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
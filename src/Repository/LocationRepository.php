<?php

namespace App\Repository;

use App\Entity\Location;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Location>
 *
 * @method Location|null find($id, $lockMode = null, $lockVersion = null)
 * @method Location|null findOneBy(array $criteria, array $orderBy = null)
 * @method Location[]    findAll()
 * @method Location[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Location::class);
    }
    public function findById($id): ?Location
    {
        return $this->find($id);
    }
    public function findAllWithUser(): array
    {
        return $this->createQueryBuilder('l')
            ->leftJoin('l.id', 'u')
            ->addSelect('u')
            ->getQuery()
            ->getResult();
    }

    public function findByUserId(int $userId): array
    {
        return $this->createQueryBuilder('l')
            ->leftJoin('l.id', 'u')
            ->andWhere('u.id = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }
     public function findUsers()
    {
        $qb = $this->createQueryBuilder('l')
            ->select('DISTINCT u.id', 'u.nomutilisateur', 'u.prenomutilisateur')
            ->innerJoin('l.id', 'u');

        return $qb->getQuery()->getResult();
    }

       /**
     * Find locations by type
     *
     * @param string $type
     * @return Location[]
     */
    public function findByType(string $type): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.type = :type')
            ->setParameter('type', $type)
            ->getQuery()
            ->getResult();
    }

    public function findByPrice(float $prix)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.prix = :prix')
            ->setParameter('prix', $prix)
            ->getQuery()
            ->getResult();
    }

   // Autres fonctions déjà présentes

    /**
     * Count total reservations for each location.
     *
     * @return array
     */
    public function countReservationsByLocation(): array
    {
        $query = $this->getEntityManager()->createQuery('
        SELECT l.idLocation as location_id, COUNT(r.id_reservation_location) as reservation_count
        FROM App\Entity\Location l
        LEFT JOIN App\Entity\ReservationLocation r WITH r.location = l
        GROUP BY l.idLocation
    ');

    return $query->getResult();
    }

 

    public function countLocationsByType(string $type): array
    {
        $query = $this->createQueryBuilder('l')
            ->select('COUNT(l.idLocation) as location_count')
            ->andWhere('l.type = :type')
            ->setParameter('type', $type)
            ->getQuery();
    
        return $query->getSingleResult();
    }

    public function findLocationsForUser(int $userId): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.id = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }
    
    public function findBySearchQuery(string $searchQuery, int $limit = null, int $offset = null): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.type LIKE :query ')
            ->setParameter('query', '%' . $searchQuery . '%')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }

    public function findByUserIdAndAvailability(int $userId, bool $availability): array
    {
        return $this->createQueryBuilder('l')
            ->leftJoin('l.id', 'u')
            ->andWhere('u.id = :userId')
            ->setParameter('userId', $userId)
            ->andWhere('l.disponibilite = :availability')
            ->setParameter('availability', $availability)
            ->getQuery()
            ->getResult();
    }


    //    /**
    //     * @return Location[] Returns an array of Location objects
    //     */
    //    public function findByExampleField($value): array
    //    {
   //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Location
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

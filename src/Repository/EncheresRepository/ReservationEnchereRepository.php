<?php

namespace App\Repository\EncheresRepository;

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

    public function findByDate(string $searchDate, int $limit = null, int $offset = null): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.dateReservation = :searchDate')
            ->setParameter('searchDate', new \DateTime($searchDate))
            ->orderBy('r.dateReservation', 'ASC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }
}

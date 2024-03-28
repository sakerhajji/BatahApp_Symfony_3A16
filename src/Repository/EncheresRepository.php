<?php

namespace App\Repository;

use App\Entity\Encheres;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Encheres>
 *
 * @method Encheres|null find($id, $lockMode = null, $lockVersion = null)
 * @method Encheres|null findOneBy(array $criteria, array $orderBy = null)
 * @method Encheres[]    findAll()
 * @method Encheres[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EncheresRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Encheres::class);
    }
    public function findBySearchQuery(string $searchQuery): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.type LIKE :query')
            ->setParameter('query', '%' . $searchQuery . '%')
            ->getQuery()
            ->getResult();
    }
    /*
public function searchEvents($searchQuery, $limit, $offset)
{
    $qb = $this->createQueryBuilder('e')
        ->andWhere('e.name LIKE :searchQuery OR e.type LIKE :searchQuery')
        ->setParameter('searchQuery', '%' . $searchQuery . '%')
        ->orderBy('e.datedebut', 'ASC')
        ->setMaxResults($limit)
        ->setFirstResult($offset);

    // Add other criteria as needed

    return $qb->getQuery()->getResult();
}
*/
    //    /**
    //     * @return Encheres[] Returns an array of Encheres objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Encheres
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

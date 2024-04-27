<?php

namespace App\Repository;

use App\Entity\Produits;
use App\Entity\Rating;
use App\Entity\Ratings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Rating>
 *
 * @method Rating|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rating|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rating[]    findAll()
 * @method Rating[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RatingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ratings::class);
    }

    /**
     * Calculate average rating for a given product.
     *
     * @param Produits $product
     * @return float|null
     */
    public function getAverageRatingForProduct(Produits $product): ?float
    {
        $queryBuilder = $this->createQueryBuilder('r');
        $queryBuilder->select('AVG(r.rating) as average_rating')
            ->andWhere('r.produit = :product')
            ->setParameter('product', $product);

        $result = $queryBuilder->getQuery()->getOneOrNullResult();

        return $result['average_rating'];
    }

    //    /**
    //     * @return Rating[] Returns an array of Rating objects
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

    //    public function findOneBySomeField($value): ?Rating
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

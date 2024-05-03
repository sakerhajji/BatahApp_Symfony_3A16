<?php

namespace App\Repository;

use App\Entity\Forum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Forum>
 *
 * @method Forum|null find($id, $lockMode = null, $lockVersion = null)
 * @method Forum|null findOneBy(array $criteria, array $orderBy = null)
 * @method Forum[]    findAll()
 * @method Forum[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ForumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Forum::class);
    }

    public function search($keyword): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.title LIKE :keyword OR f.content LIKE :keyword')
            ->setParameter('keyword', '%' . $keyword . '%')
            ->getQuery()
            ->getResult();
    }

    public function findBestTimeToBuy(): array
    {
        return $this->createQueryBuilder('f')
            ->select('MIN(f.prix) as lowest_price', 'f.createdAt as best_time_to_buy')
            ->getQuery()
            ->getSingleResult();
    }

    public function findBestPrices(int $limit): array
    {
        // Implement your logic to fetch the best prices data from the database
        // For example:
        return $this->createQueryBuilder('f')
            ->select('f.prix, f.createdAt')
            ->orderBy('f.prix', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findLatestForums(int $limit = 5): array
    {
        return $this->createQueryBuilder('f')
            ->orderBy('f.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return Forum[] Returns an array of Forum objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Forum
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

<?php

namespace App\Repository;

use App\Entity\AvisLivraison;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AvisLivraison>
 *
 * @method AvisLivraison|null find($id, $lockMode = null, $lockVersion = null)
 * @method AvisLivraison|null findOneBy(array $criteria, array $orderBy = null)
 * @method AvisLivraison[]    findAll()
 * @method AvisLivraison[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AvisLivraisonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AvisLivraison::class);
    }

//    /**
//     * @return AvisLivraison[] Returns an array of AvisLivraison objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AvisLivraison
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

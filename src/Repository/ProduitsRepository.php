<?php

namespace App\Repository;

use App\Entity\Produits;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produits>
 *
 * @method Produits|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produits|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produits[]    findAll()
 * @method Produits[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produits::class);
    }
    public function findBySearchQuery(string $searchQuery, int $limit = null, int $offset = null): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.labelle LIKE :query OR p.type LIKE :query')
            ->setParameter('query', '%' . $searchQuery . '%')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
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
    //     * @return Produits[] Returns an array of Produits objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Produits
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findAllSorted(): array
    {
        $queryBuilder = $this->createQueryBuilder('cl')
            ->orderBy('cl.prix', 'ASC');

        return $queryBuilder->getQuery()->getResult();
    }
    public function findAllSorted1(): array
    {
        $queryBuilder = $this->createQueryBuilder('cl')
            ->orderBy('cl.prix', 'DESC');

        return $queryBuilder->getQuery()->getResult();
    }

    public function advancedSearch($query, $idProduit, $labelle, $status)
    {
        $qb = $this->createQueryBuilder('c');

        if ($query) {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('c.idProduit', ':query'),
                $qb->expr()->like('c.labelle', ':query'),
                $qb->expr()->like('c.status', ':query'),

            ))
                ->setParameter('query', '%' . $query . '%');
        }

        if ($idProduit) {
            $qb->andWhere('c.idProduit = :idProduit')
                ->setParameter('idProduit', $idProduit);
        }

        if ($labelle) {
            $qb->andWhere('c.labelle = :labelle')
                ->setParameter('labelle', $labelle);
        }

        if ($status) {
            $qb->andWhere('c.status = :status')
                ->setParameter('status', $status);
        }
    }

    public function countByType($type)
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.type)')
            ->where('r.type = :type')
            ->setParameter('type', $type)
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function countAllProducts()
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.idProduit)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}

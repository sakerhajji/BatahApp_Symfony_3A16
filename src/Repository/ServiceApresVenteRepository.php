<?php

namespace App\Repository;

use App\Entity\ServiceApresVente;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ServiceApresVente>
 *
 * @method ServiceApresVente|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServiceApresVente|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServiceApresVente[]    findAll()
 * @method ServiceApresVente[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceApresVenteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServiceApresVente::class);
    }
    public function findByTypeOrStatus($search)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.type LIKE :search')
            ->orWhere('s.status = :status')
            ->setParameter('search', '%' . $search . '%')
            ->setParameter('status', $search)
            ->getQuery()
            ->getResult();
    }
    public function countMostPurchasedServices(int $limit = 5): array
    {
        return $this->createQueryBuilder('s')
            ->select('s.idService, s.description, COUNT(s.idService) AS total')
            ->groupBy('s.idService')
            ->orderBy('total', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
    //    /**
    //     * @return ServiceApresVente[] Returns an array of ServiceApresVente objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ServiceApresVente
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

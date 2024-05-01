<?php

namespace App\Repository\EncheresRepository;

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
    public function findAll()
    {
        return $this->createQueryBuilder('s')
            ->select('s.idService', 's.description', 's.type', 's.date', 's.status', 's.idAchats', 's.idPartenaire')
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

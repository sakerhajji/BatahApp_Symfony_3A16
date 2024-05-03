<?php

namespace App\Repository;

use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Utilisateur>
 *
 * @method Utilisateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Utilisateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Utilisateur[]    findAll()
 * @method Utilisateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UtilisateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisateur::class);
    }

//    /**
//     * @return Utilisateur[] Returns an array of Utilisateur objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Utilisateur
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function login(string $username, string $password): ?Utilisateur
    {
        $queryBuilder = $this->createQueryBuilder('u')
            ->where('u.adresseemail = :username') // Assuming 'adresseemail' is analogous to 'username'
            ->setParameter('username', $username)
            ->setMaxResults(1);

        $user = $queryBuilder->getQuery()->getOneOrNullResult();

        return $user;
    }

    public function ForgetPassword(string $username): ?Utilisateur
    {
        $queryBuilder = $this->createQueryBuilder('u')
            ->where('u.adresseemail = :username')
            ->setParameter('username', $username)
            ->setMaxResults(1);
        $user = $queryBuilder->getQuery()->getOneOrNullResult();
        return $user;
    }

    public function updatePasswor(int $userId, string $newPassword): int
    {
        $entityManager = $this->getEntityManager();
        $dql = "UPDATE App\Entity\Utilisateur u SET u.motdepasse = :newPassword WHERE u.id = :id";
        $query = $entityManager->createQuery($dql);
        $query->setParameter('newPassword', password_hash($newPassword, PASSWORD_BCRYPT));
        $query->setParameter('id', $userId);

        return $query->execute(); // Execute the DQL query and return the number of affected rows
    }
}

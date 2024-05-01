<?php

namespace App\Service;

use App\Entity\Basket;
use App\Entity\ReservationEnchere;
use App\Repository\ArticleRepository;
use App\Repository\EncheresRepository\BasketRepository;
use App\Repository\EncheresRepository\EncheresRepository;
use App\Repository\EncheresRepository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;


class ReservationService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function addToCart($userId, $encheresId, UtilisateurRepository $userRep, EncheresRepository $articleRep)
    {
        $user = $userRep->find($userId);
        $article = $articleRep->find($articleId);

        $panier = new ReservationEnchere();
        $panier->setIdUser($user);
        $panier->setIdEncher($article);
        $panier->setDateAjout(new \DateTime());

        $this->entityManager->persist($panier);
        $this->entityManager->flush();
    }

    public function getCartItems($userId)
    {
        $panier = $this->entityManager->getRepository(Basket::class)->findBy([
            'idClient' => $userId
        ]);

        return $panier;
    }

    public function removeFromCart($basketId, BasketRepository $basketRep)
    {
        $basket = $basketRep->find($basketId);

        if (!$basket) {
            throw new \Exception('Basket item not found');
        }

        $this->entityManager->remove($basket);
        $this->entityManager->flush();
    }

    public function emptyCart($userId)
    {
        $panier = $this->entityManager->getRepository(Basket::class)->findBy([
            'idClient' => $userId
        ]);

        foreach ($panier as $item) {
            $this->entityManager->remove($item);
        }

        $this->entityManager->flush();
    }
}

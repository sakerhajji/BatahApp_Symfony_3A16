<?php

namespace App\Service;

use App\Repository\ArticleRepository;
use App\Repository\UtilisateurRepository;
use App\Repository\BasketRepository;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Basket;
use App\Repository\ProduitsRepository;

class BasketService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function addToCart($userId, $prodId, UtilisateurRepository $userRep, ProduitsRepository $pr)
    {
        $user = $userRep->find($userId);
        $article = $pr->find($prodId);

        $panier = new Basket();
        $panier->setIdClient($user);
        $panier->setIdProduit($article);
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

    public function applyPromoCode($userId, $promoCode)
    {
        // Vérifier si le code promo est "battah"
        if ($promoCode === "battah") {
            // Récupérer les articles dans le panier de l'utilisateur
            $panier = $this->entityManager->getRepository(Basket::class)->findBy([
                'idClient' => $userId
            ]);

            // Calculer la remise de 20% sur le prix total du panier
            $totalPrice = 0;
            foreach ($panier as $item) {
                $totalPrice += $item->getIdProduit()->getPrix();
            }
            $discount = $totalPrice * 0.20; // 20% de remise

            // Appliquer la remise à chaque article dans le panier
            foreach ($panier as $item) {
                $article = $item->getIdProduit();
                $article->setPrix($article->getPrix() - ($discount / count($panier)));
                $this->entityManager->persist($article);
            }

            // Sauvegarder les modifications dans la base de données
            $this->entityManager->flush();

            return true; // Remise appliquée avec succès
        } else {
            return false; // Code promo invalide
        }
    }
}

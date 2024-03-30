<?php

namespace App\Controller;

use App\Service\BasketService;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Utilisateur;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\ArticleRepository;
use App\Repository\BasketRepository;
use App\Repository\ProduitsRepository;
use App\Repository\UtilisateurRepository;
use Twilio\Rest\Preview\HostedNumbers\ReadAuthorizationDocumentOptions;

class PanierController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    #[Route('/panier', name: 'app_panier')]
    public function index(BasketService $basketService, UtilisateurRepository $userRep, Request $request): Response
    {
        $session =  $request->getSession();
        $connectedUser = $session->get('user');

        // If user is not logged in, redirect to login page
        if ($connectedUser == null) {
            return $this->redirectToRoute("app_login");
        }

        // Fetch cart items for the connected user
        $basketData = $basketService->getCartItems($connectedUser->getId());
        $basketItemsCount = count($basketData);

        // Calculate total price of items in the cart
        $totalPrice = array_reduce($basketData, function ($total, $product) {
            return $total + $product->getIdProduit()->getPrix();
        }, 0);

        // Render the panier.html.twig template with necessary data
        return $this->render('panier/panier.html.twig', [
            'controller_name' => 'PanierController',
            'basketData' => $basketData,
            'totalPrice' => $totalPrice,
            'connectedUser' => $connectedUser,
            'basketItemsCount' => $basketItemsCount,
        ]);
    }


    #[Route('/addToBasket/{idp}', name: 'app_addToBasket')]
    public function addToBasket(Request $request, $idp, BasketService $basketService, UtilisateurRepository $userRep, ProduitsRepository $pr): Response
    {
        $session =  $request->getSession();
        $connectedUser = $session->get('user');
        $connectedUser = $userRep->find($connectedUser->getId());

        $basketService->addToCart($connectedUser->getId(), $idp, $userRep, $pr);

        // add flash message
        $this->addFlash('command_ajoute', 'Article ajouté au panier');

        return $this->redirectToRoute('app_afficahge_produits');
    }

    #[Route('/removeFromBasket/{idp}', name: 'app_removeFromBasket')]
    public function removeFromBasket($idp, BasketService $basketService, BasketRepository $basketRep): Response
    {
        // Remove the selected article from the user's basket
        $basketService->removeFromCart($idp, $basketRep);

        // Redirect back to the cart page
        return $this->redirectToRoute('app_panier');
    }
}

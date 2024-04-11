<?php

namespace App\Controller;

use Stripe;
use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\BasketService;
use Symfony\Component\Security\Core\Security;



class StripeController extends AbstractController
{
    #[Route('/command/stripe', name: 'app_stripe')]
    public function index(BasketService $basketService, UtilisateurRepository $userRep, Security $security, Request $request): Response
    {


        $connectedUser = $request->getSession()->get('user');

        $basketData = $basketService->getCartItems($connectedUser->getId());
        $basketItemsCount = count($basketData);
        //$connectedUser = $userRep->find(32);

        $totalPrice = array_reduce($basketData, function ($total, $product) {
            return $total + $product->getIdProduit()->getPrix();
        }, 0);

        $totalPrice += 8;

        return $this->render('stripe/index.html.twig', [
            'stripe_key' => $_ENV["STRIPE_KEY"],
            'totalPrice' => $totalPrice,
            'basketItemsCount' => $basketItemsCount,
        ]);
    }


    #[Route('/stripe/create-charge', name: 'app_stripe_charge', methods: ['POST'])]
    public function createCharge(Request $request, BasketService $basketService, UtilisateurRepository $userRep)
    {

        $basketData = $basketService->getCartItems(32);
        $basketItemsCount = count($basketData);
        $connectedUser = $userRep->find(32);

        $totalPrice = array_reduce($basketData, function ($total, $product) {
            return $total + $product->getIdProduit()->getPrix();
        }, 0);


        Stripe\Stripe::setApiKey($_ENV["STRIPE_SECRET"]);
        Stripe\Charge::create([
            "amount" => ($totalPrice + 8) * 100,
            "currency" => "usd",
            "source" => $request->request->get('stripeToken'),
            "description" => "Paiement de la commande via BATTAH",
            "metadata" => [
                "client_name" => "John Doe"
            ]
        ]);
        $this->addFlash(
            'successPaiement',
            'Payment succées!',
        );
        $basketService->emptyCart(32);
        return $this->redirectToRoute('app_afficahge_produits');
    }
}

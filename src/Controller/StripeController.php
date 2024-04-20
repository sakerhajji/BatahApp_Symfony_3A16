<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Entity\Utilisateur;
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
    public function createCharge(Request $request, BasketService $basketService, Security $security, UtilisateurRepository $userRep)
    {

        $session =  $request->getSession();
        $connectedUser = $session->get('user');
        $connectedUser = $userRep->find($connectedUser->getId());


        $basketData = $basketService->getCartItems($connectedUser);
        $basketItemsCount = count($basketData);

        $totalPrice = array_reduce($basketData, function ($total, $product) {
            return $total + $product->getIdProduit()->getPrix();
        }, 0);

        $totalPrice += 8;

        // Log the total price for debugging
        error_log("Total Price: " . $totalPrice);

        Stripe\Stripe::setApiKey($_ENV["STRIPE_SECRET"]);

        try {
            Stripe\Charge::create([
                "amount" => $totalPrice * 100,  // Amount in cents
                "currency" => "usd",
                "source" => $request->request->get('stripeToken'),
                "description" => "Paiement de la commande via Bttah",
                "metadata" => [
                    "client_name" => $connectedUser->getUsername() // Assuming getUsername() returns the user's name
                ]
            ]);

            $this->addFlash(
                'successPaiement',
                'Payment succées!',
            );

            $basketService->emptyCart($connectedUser);

            return $this->redirectToRoute('produits');
        } catch (\Exception $e) {
            // Log the exception for debugging
            error_log("Error creating charge: " . $e->getMessage());

            $this->addFlash(
                'errorPaiement',
                'Error processing payment!',
            );

            return $this->redirectToRoute('app_afficahge_produits');
        }
    }
}

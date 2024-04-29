<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\ReservationEnchere;
use App\Form\ReservationEnchereType;
use App\Service\ReservationService;
use App\Repository\ReservationEnchereRepository;
use App\Entity\Enchere;
use App\Entity\Encheres;
use App\Repository\EncheresRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;




class ReservationEnchereController extends AbstractController
{
    #[Route('/reservation/enchere', name: 'app_reservation_enchere')]
    public function index(): Response
    {
        return $this->render('reservation_enchere/index.html.twig', [
            'controller_name' => 'ReservationEnchereController',
        ]);
    }

    #[Route('/affichereservations', name: 'app_affiche_reservations')]
    public function afficheReservations(Request $request, ReservationEnchereRepository $reservationEnchereRepository): Response
    {
        $currentPage = $request->query->getInt('page', 1);
        $itemsPerPage = 5;
        $offset = ($currentPage - 1) * $itemsPerPage;
    
        $searchDate = $request->query->get('searchDate');
    
        if ($searchDate) {
            $reservations = $reservationEnchereRepository->findByDate($searchDate, $itemsPerPage, $offset);
            $totalItems = count($reservations);
        } else {
            $reservations = $reservationEnchereRepository->findBy([], null, $itemsPerPage, $offset);
            $totalItems = $reservationEnchereRepository->count([]);
        }
    
        $totalPages = ceil($totalItems / $itemsPerPage);
    
        return $this->render('reservation_enchere/affiche_reservations.html.twig', [
            'reservations' => $reservations,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
        ]);
    }
    



    #[Route('/wala', name: 'app_add_reservation_enchere')]
    public function addReservationEnchere(Request $request): Response
    {
        $reservationEnchere = new ReservationEnchere();
        dump($reservationEnchere); 
        // Initialiser les valeurs par défaut si nécessaire
        $form = $this->createForm(ReservationEnchereType::class, $reservationEnchere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reservationEnchere);
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation_enchere');
        }

        return $this->render('reservation_enchere/add_reservation_enchere.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/addToBasket/{idp}', name: 'app_addToBasket')]
    public function addToBasket(Request $request, $idp, BasketService $basketService, UtilisateurRepository $userRep, ProduitsRepository $pr): Response
    {

        $basketService->addToCart($connectedUser->getId(), $idp, $userRep, $pr);

        // add flash message
        $this->addFlash('command_ajoute', 'Article ajouté au panier');

        return $this->redirectToRoute('app_afficahge_produits');
    }

    #[Route('/reserverwala/{idp}/{iduser}', name: 'app_reserverwala')]
    public function reserver(int $idp, int $iduser, EncheresRepository $encheresRepository, Request $request, UtilisateurRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'ID de l'enchère à partir de la requête POST
       
        $iduser = 3;
        $user = $userRepository->find($iduser);
    
        $enchere = $encheresRepository->find($idp);


        // Vérifier si l'enchère a été trouvée
        if (!$enchere) {
            // Enchère non trouvée, retourner une réponse avec un code d'erreur approprié
            return new Response('Enchère non trouvée', Response::HTTP_NOT_FOUND);
        }

        // Créer une nouvelle instance de ReservationEnchere
        $reservation = new ReservationEnchere();

        // Récupérer l'ID de l'utilisateur connecté (vous devrez implémenter cette partie)


        // Définir les valeurs de la réservation
        $reservation->setIdEnchere($enchere);
        $reservation->setIdUser($user);
        $reservation->setDateReservation(new \DateTime());
        $reservation->setConfirmation(true); // ou 1

        // Enregistrer la réservation en base de données
        $entityManager->persist($reservation);
        $entityManager->flush();

        // Rediriger ou effectuer toute autre action nécessaire
        // Vous pouvez rediriger vers une page de confirmation ou une autre page appropriée

        $this->sendTwilioMessage($reservation);
        return $this->redirectToRoute('app_Afficheclient_enchere',[],Response::HTTP_SEE_OTHER);
    }

    /**
     * @throws ConfigurationException
     * @throws TwilioException
     */
    private function sendTwilioMessage(ReservationEnchere $reservation): void
    {
        $twilioAccountSid = $this->getParameter('twilio_account_sid');
        $twilioAuthToken = $this->getParameter('twilio_auth_token');
        $twilioPhoneNumber = $this->getParameter('twilio_phone_number');

        $twilioClient = new Client($twilioAccountSid, $twilioAuthToken);

        $messageBody = sprintf(
            'Your affectation has been successfully registered with the following details:' .
            "\nDescription: %s\nType: %s\nDate: %s\nStatus: %s",
            $reservation->getConfirmation(),
            $reservation->getDateReservation()->format('Y-m-d H:i:s'),
            $reservation->getIdReservation(),
            $reservation->getIdReservation(),
            $reservation->getIdReservation()

        );

        $twilioClient->messages->create(
            '+21621834550', // Replace with the recipient's phone number
            [
                'from' => $twilioPhoneNumber,
                'body' => $messageBody
            ]
        );
    }



    
}

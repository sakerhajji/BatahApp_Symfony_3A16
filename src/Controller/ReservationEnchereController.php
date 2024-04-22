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

    #[Route('/reserverwala', name: 'app_reserverwala')]
public function reserver(Request $request, EntityManagerInterface $entityManager): Response
{
    // Récupérer l'ID de l'enchère à partir de la requête POST
    $enchereId = $request->request->get('enchereId');

    // Récupérer l'objet Enchere correspondant à l'ID donné
    $enchere = $entityManager->getRepository(Enchere::class)->find($enchereId);

    // Vérifier si l'enchère a été trouvée
    if (!$enchere) {
        // Enchère non trouvée, retourner une réponse avec un code d'erreur approprié
        return new Response('Enchère non trouvée', Response::HTTP_NOT_FOUND);
    }

    // Créer une nouvelle instance de ReservationEnchere
    $reservation = new ReservationEnchere();

    // Récupérer l'ID de l'utilisateur connecté (vous devrez implémenter cette partie)
    $idUser = 3;

    // Définir les valeurs de la réservation
    $reservation->setIdEnchere($enchere);
    $reservation->setIdUser($idUser);
    $reservation->setDate(new \DateTime());
    $reservation->setConfirmation(true); // ou 1

    // Enregistrer la réservation en base de données
    $entityManager->persist($reservation);
    $entityManager->flush();

    // Rediriger ou effectuer toute autre action nécessaire
    // Vous pouvez rediriger vers une page de confirmation ou une autre page appropriée
    return $this->redirectToRoute('nom_de_votre_route_de_redirection');
}



    
}

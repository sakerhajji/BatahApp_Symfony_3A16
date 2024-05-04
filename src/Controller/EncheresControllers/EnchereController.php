<?php

namespace App\Controller\EncheresControllers;


use App\Entity\Encheres;
use App\Entity\Utilisateur;
use App\Form\EncheresType;
use App\Repository\EncheresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;


class EnchereController extends AbstractController
{
    private $session;
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }
    #[Route('/enchere', name: 'app_enchere')]
    public function index(): Response
    {
        return $this->render('EncheresTemplates/enchere/index.html.twig', [
            'controller_name' => 'EnchereController',
            'user'=>$this->session->get('user'),
        ]);
    }
    #[Route('/add', name: 'app_Add_enchere')]
    public function add(Request $request): Response
    {
        $encheres = new Encheres();
        $encheres->setNbrParticipants(0);
        $form = $this->createForm(EncheresType::class, $encheres);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($encheres);
            $entityManager->flush();

            return $this->redirectToRoute('app_Affiche_enchere');
        }

        return $this->render('EncheresTemplates/enchere/page-dashboard-add-encheres.html.twig', [
            'f' => $form->createView(),
            'user'=>$this->session->get('user'),
        ]);
    }



    #[Route('/affiche', name: 'app_Affiche_enchere')]
    public function affiche(Request $request, EncheresRepository $encheresRepository): Response
    {
        $currentPage = $request->query->getInt('page', 1);
        $itemsPerPage = 5;
        $offset = ($currentPage - 1) * $itemsPerPage;

        $startDate = $request->query->get('start_date');
        $endDate = $request->query->get('end_date');

        if ($startDate && $endDate) {
            $encheresItems = $encheresRepository->findByDateRange($startDate, $endDate, $itemsPerPage, $offset);
            $totalItems = $encheresRepository->countByDateRange($startDate, $endDate);
        } else {
            $encheresItems = $encheresRepository->findBy([], null, $itemsPerPage, $offset);
            $totalItems = $encheresRepository->count([]);
        }

        $totalPages = ceil($totalItems / $itemsPerPage);

        return $this->render('EncheresTemplates/enchere/page-dashboard-listing.html.twig', [
            'event' => $encheresItems,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'user'=>$this->session->get('user'),
        ]);
    }


    #[Route('/supprimerenchere/{ide}', name: 'app_supprimerenchere')]
    public function removench(ManagerRegistry $em, Request $request, EncheresRepository $pr, $ide): Response
    {

        $ench = $pr->find($ide);
        $em = $em->getManager();
        $em->remove($ench);
        $em->flush();
        return $this->redirectToRoute('app_Affiche_enchere');
    }

    #[Route('/modifierenchere/{id}', name: 'app_modifier_enchere')]
    public function modifierEnchere(ManagerRegistry $registry, Request $request, EncheresRepository $encheresRepository, $id): Response
    {
        $entityManager = $registry->getManager();
        $enchere = $encheresRepository->find($id);

        if (!$enchere) {
            throw $this->createNotFoundException('Enchère non trouvée');
        }

        $form = $this->createForm(EncheresType::class, $enchere);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_Affiche_enchere');
        }

        return $this->render('EncheresTemplates/enchere/page-dashboard-edit-encheres.html.twig', [
            'form' => $form->createView(),
            'user'=>$this->session->get('user'),
        ]);
    }

    #[Route('/afficheclient', name: 'app_Afficheclient_enchere')]
    public function afficheclient(Request $request, EntityManagerInterface $em, EncheresRepository $encheresRepository, SerializerInterface $serializer): Response
    {

        $currentPage = $request->query->getInt('page', 1);
        $itemsPerPage = 5;
        $offset = ($currentPage - 1) * $itemsPerPage;

        $searchQuery = $request->query->get('search', '');

        if ($searchQuery !== '') {
            $encheresItems = $encheresRepository->findBySearchQuery($searchQuery, $itemsPerPage, $offset);
            $totalItems = count($encheresItems);
        } else {
            $encheresItems = $encheresRepository->findBy([], null, $itemsPerPage, $offset);
            $totalItems = $encheresRepository->count([]);
        }

        $totalPages = ceil($totalItems / $itemsPerPage);

        // Formater les dates de début et de fin
        $formattedEncheresItems = [];
        foreach ($encheresItems as $enchere) {
            // Vérifier si l'enchère est définie et si les méthodes getDateDebut() et getDateFin() existent
            if (isset($enchere) && method_exists($enchere, 'getDateDebut') && method_exists($enchere, 'getDateFin')) {
                // Formater les dates de début et de fin s'ils sont disponibles
                $formattedEncheresItems[] = [
                    'enchere' => $enchere,
                    'dateDebutFormatted' => $enchere->getDateDebut() ? $enchere->getDateDebut()->format('Y-m-d H:i:s') : 'Date de début non disponible',
                    'dateFinFormatted' => $enchere->getDateFin() ? $enchere->getDateFin()->format('Y-m-d H:i:s') : 'Date de fin non disponible',
                ];
            }
        }

        // Fetch products associated with the auctions
        $products = [];
        foreach ($encheresItems as $enchere) {
            $products[] = $enchere->getProduit();
        }

        return $this->render('EncheresTemplates/enchere/page-front-enchere.html.twig', [
            'encheres' => $formattedEncheresItems,
            'products' => $products,
            'searchQuery' => $searchQuery,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'user'=>$this->session->get('user'),
            'partenaires' => $this->session->get('partenaires'),
            'avis' => $this->session->get('avis'),
        ]);
    }

    #[Route('/walouta', name: 'walouta_enchere')]
    public function waloutalmahboula(Request $request, EncheresRepository $encheresRepository, SerializerInterface $serializer): Response
    {
        $currentPage = $request->query->getInt('page', 1);
        $itemsPerPage = 5;
        $offset = ($currentPage - 1) * $itemsPerPage;

        // Récupérer les enchères de l'utilisateur d'ID 6 avec confirmation de réservation égale à 1
        $encheresItems = $encheresRepository->findEncheresByUserAndConfirmation(6, 1, $itemsPerPage, $offset);
        $totalItems = count($encheresItems);

        $totalPages = ceil($totalItems / $itemsPerPage);

        // Formater les dates de début et de fin
        $formattedEncheresItems = [];
        foreach ($encheresItems as $enchere) {
            if (isset($enchere) && method_exists($enchere, 'getDateDebut') && method_exists($enchere, 'getDateFin')) {
                $formattedEncheresItems[] = [
                    'enchere' => $enchere,
                    'dateDebutFormatted' => $enchere->getDateDebut() ? $enchere->getDateDebut()->format('Y-m-d H:i:s') : 'Date de début non disponible',
                    'dateFinFormatted' => $enchere->getDateFin() ? $enchere->getDateFin()->format('Y-m-d H:i:s') : 'Date de fin non disponible',
                    'user'=>$this->session->get('user'),
                ];
            }
        }

        // Fetch products associated with the auctions
        $products = [];
        foreach ($encheresItems as $enchere) {
            $products[] = $enchere->getProduit();
        }

        return $this->render('EncheresTemplates/enchere/liste_encheres_utilisateur.html.twig', [
            'encheres' => $formattedEncheresItems,
            'products' => $products, // Pass products to Twig
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'user'=>$this->session->get('user'),
        ]);
    }


    #[Route('/encheret/statistics', name: 'app_encheret_statistics')]
    public function statistics(EncheresRepository $encheresRepository): Response
    {
        // Récupérer les données pour les statistiques
        $statisticsData = $encheresRepository->getEncheresByDate();

        // Rendre la vue avec les données des statistiques
        return $this->render('EncheresTemplates/enchere/statistics.html.twig', [
            'statisticsData' => $statisticsData,
            'user'=>$this->session->get('user'),
        ]);
    }








}





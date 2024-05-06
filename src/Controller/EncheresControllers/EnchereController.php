<?php

namespace App\Controller\EncheresControllers;


use App\Entity\Encheres;
use App\Entity\Produits;
use App\Entity\Utilisateur;
use App\Form\EncheresType;
use App\Repository\AvisLivraisonRepository;
use App\Repository\BasketRepository;
use App\Repository\EncheresRepository;
use App\Repository\ImageRepository;
use App\Repository\LocationRepository;
use App\Repository\PartenairesRepository;
use App\Repository\ProduitsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
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
            'user' => $this->session->get('user'),
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
            'user' => $this->session->get('user'),
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
            'user' => $this->session->get('user'),
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
            'user' => $this->session->get('user'),
        ]);
    }

    #[Route('/afficheclient', name: 'app_Afficheclient_enchere')]
    public function afficheclient(Request $request, LocationRepository $lr, PaginatorInterface $paginator, EntityManagerInterface $em, EncheresRepository $encheresRepository, SerializerInterface $serializer, ImageRepository $imageRepository, ProduitsRepository $pr, LocationRepository $loca, BasketRepository $basketRep, PartenairesRepository $PartenairesRepository, AvisLivraisonRepository $avisLivraisonRepository): Response
    {

        $partenaires = $PartenairesRepository->findAll();
        $avis = $avisLivraisonRepository->findAll();


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



        $connectedUser = $this->session->get('user');

        $listArticles = $pr->findAll();

        $existingArticles = [];
        $basketItems = $basketRep->findBy(['idClient' => $connectedUser]);
        $basketItemsCount = count($basketItems);
        $em = $this->getDoctrine()->getManager()->getRepository(Produits::class);

        $repository = $this->getDoctrine()->getRepository(Produits::class)->findAll();


        foreach ($basketItems as $basketItem) {
            // Check if $basketItem has an associated Produits object
            if ($basketItem->getIdProduit() !== null) {
                $articleId = $basketItem->getIdProduit()->getIdProduit();

                // Check if the article ID exists in the list of articles
                foreach ($repository as $article) {
                    if ($article->getIdProduit() === $articleId) {
                        // Add the existing article to the list of existing articles
                        $existingArticles[] = $article->getIdProduit();
                        break;
                    }
                }
            } else {
                // Handle the case where $basketItem doesn't have an associated Produits
                // For example, you can log an error message or skip this basket item
            }
        }
        $pagination = $paginator->paginate(
            $repository,
            $request->query->getInt('page', 1), // Current page number
            3 // Number of items per page
        );
        $basketItemsCount = count($basketItems);
        $allProducts = $pr->findAll();

        $allLocation = $loca->findAll();


        // Fetch images
        $imagesByLocation = [];
        foreach ($allProducts as $prod) { // Utilisez $allProducts à la place de $products
            $imagesByLocation[$prod->getIdProduit()] = $imageRepository->findBy(['produits' => $prod]);
        }

        // Fetch images
        $imagesByLo = [];
        foreach ($allLocation as $l) { // Utilisez $allProducts à la place de $products
            $imagesByLo[$l->getIdLocation()] = $imageRepository->findBy(['location' => $l]);
        }


        // Fetch locations
        $locations = $lr->findAllWithUser(); // Assuming you have a custom method findAllWithUser in LocationRepository to join User entity





        return $this->render('EncheresTemplates/enchere/page-front-enchere.html.twig', [
            'encheres' => $formattedEncheresItems,
            //'listS' => $pagination,
            'products' => $products,
            'searchQuery' => $searchQuery,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'user' => $this->session->get('user'),
            'imagesByLocation' => $imagesByLocation,
            "imagesByLo" => $imagesByLo,
            'existingArticles' => $existingArticles,
            'basketItemsCount' => $basketItemsCount,
            'prod' => $allProducts, // Produits pour nav-home
            'listArticles' => $listArticles,
            'locations' => $locations,
            'partenaires' => $partenaires,
            'avis' => $avis,

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
                    'user' => $this->session->get('user'),
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
            'user' => $this->session->get('user'),
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
            'user' => $this->session->get('user'),
        ]);
    }
}

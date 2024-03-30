<?php

namespace App\Controller;

use App\Entity\Produits;
use App\Form\ProduitsType;
use App\Repository\ProduitsRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Knp\Component\Pager\PaginatorInterface;

use Knp\Snappy\Pdf;
use App\Form\CalculatorType;
use App\Repository\BasketRepository;
use App\Repository\UtilisateurRepository;
use Dompdf\Dompdf;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ProduitController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    #[Route('/home', name: 'app_home')]
    public function home(): Response
    {

        return $this->render('home/home.html.twig');
    }

    /************************************************************************************************************************************************* */
    /**************************************************************CRUD-PRODUCT*********************************************************************************** */


    #[Route('/ajout', name: 'app_produit')]
    public function addprod(ManagerRegistry $em, Request $request): Response
    {
        $em = $em->getManager();

        $prod = new Produits();
        $form = $this->createForm(ProduitsType::class, $prod);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifiez si une image a été téléchargée
            $uploadedFile = $form->get('photo')->getData();
            if ($uploadedFile) {
                // Générez un nom de fichier unique
                $newFilename = uniqid() . '.' . $uploadedFile->guessExtension();
                // Déplacez le fichier vers le répertoire où les images sont stockées
                $uploadedFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads',
                    $newFilename
                );
                // Définissez le chemin de l'image dans l'entité Produits
                $prod->setPhoto($newFilename);
            }


            $em->persist($prod);
            $em->flush();

            return $this->redirectToRoute('app_back_affiche');
        }

        return $this->render('produit/page-dashboard-add-produits.html.twig', ['form' => $form->createView()]);
    }
    #[Route('/supprimer/{idp}', name: 'app_supprimer')]
    public function removeprod(ManagerRegistry $em, Request $request, ProduitsRepository $pr, $idp): Response
    {

        $prod = $pr->find($idp);
        $em = $em->getManager();
        $em->remove($prod);
        $em->flush();
        return $this->redirectToRoute('app_back_affiche');
    }

    #[Route('/modifierProduit/{idp}', name: 'app_modifier_produit')]
    public function editprod(ManagerRegistry $em, Request $request, ProduitsRepository $pr, $idp): Response
    {
        $em = $em->getManager();
        $produit = $pr->find($idp);

        if (!$produit) {
            throw $this->createNotFoundException('Produit non trouvé');
        }

        $form = $this->createForm(ProduitsType::class, $produit);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle image upload
            $photoFile = $form->get('photo')->getData();

            if ($photoFile instanceof UploadedFile) {
                $newFilename = md5(uniqid()) . '.' . $photoFile->guessExtension();
                $photoFile->move($this->getParameter('kernel.project_dir') . '/public/uploads', $newFilename);
                $produit->setPhoto($newFilename);
            }

            $em->flush();

            return $this->redirectToRoute('app_back_affiche');
        }

        return $this->render('produit/page-dashboard-edit-produits.html.twig', [
            'form' => $form->createView(),
            'photo_path' => $produit->getPhoto() ? '/uploads/' . $produit->getPhoto() : null,
        ]);
    }
    #[Route('/produitback', name: 'app_back_affiche')]
    public function showprodback(Request $request, ProduitsRepository $pr): Response
    {
        $user = $this->getUser();
        $currentPage = $request->query->getInt('page', 1);
        $itemsPerPage = 5;
        $offset = ($currentPage - 1) * $itemsPerPage;

        $searchQuery = $request->query->get('search', '');

        if ($searchQuery !== '') {
            $products = $pr->findBySearchQuery($searchQuery, $itemsPerPage, $offset);
            $totalItems = count($products);
        } else {
            $products = $pr->findBy([], null, $itemsPerPage, $offset);
            $totalItems = $pr->count([]);
        }
        // Manipulation de l'URL pour extraire la partie souhaitée
        foreach ($products as &$product) {
            $url = $product->getLocalisation();
            $start_pos = strpos($url, '?q=');
            $end_pos = strpos($url, '&t');

            // Extraire la sous-chaîne entre ces deux positions
            if ($start_pos !== false && $end_pos !== false) {
                $localisation = substr($url, $start_pos + strlen('?q='), $end_pos - $start_pos - strlen('?q='));
            } else {
                // Gérer le cas où "?q=" ou "&t" n'est pas trouvé
                $localisation = ''; // Ou toute autre valeur par défaut que vous souhaitez utiliser
            }
            $product->setlocalisation($localisation); // Mettre à jour l'attribut localisation avec la partie extraite
        }

        $totalPages = ceil($totalItems / $itemsPerPage);

        return $this->render('produit/page-dashboard-listing.html.twig', [
            'products' => $products,
            'searchQuery' => $searchQuery,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
        ]);
    }


    #[Route('/detailsProduit/{idp}', name: 'app_details_produit')]
    public function details(ProduitsRepository $pr, $idp): Response
    {

        $product = $pr->find($idp);

        return $this->render('produit/page-front-details-produit.html.twig', ['product' => $product]);
    }
    #[Route('/detail', name: 'app_detail_produit')]
    public function detail(): Response
    {
        return $this->render('produit/page-front-details-produit.html.twig');
    }

    #[Route('/az', name: 'app_afficahge_produits')]
    public function index(Request $request, PaginatorInterface $paginator, UtilisateurRepository $userRep, BasketRepository $basketRep, ProduitsRepository $pr): Response
    {

        $session =  $request->getSession();
        $usersession = $session->get('user');
        if ($usersession == null) {
            return $this->redirectToRoute("app_login");
        }

        $connectedUser = $this->session->get('user');


        $user = $userRep->find($connectedUser->getId());

        $existingArticles = [];
        $basketItems = $basketRep->findBy(['idClient' => $user]);
        $basketItemsCount = count($basketItems);
        $em = $this->getDoctrine()->getManager()->getRepository(Produits::class);

        $repository = $this->getDoctrine()->getRepository(Produits::class)->findAll();


        // Loop through each basket item
        foreach ($basketItems as $basketItem) {
            $articleId = $basketItem->getIdProduit()->getIdProduit();

            // Check if the article ID exists in the list of articles
            foreach ($repository as $article) {
                if ($article->getIdProduit() === $articleId) {
                    // Add the existing article to the list of existing articles
                    $existingArticles[] = $article->getIdProduit();
                    break;
                }
            }
        }


        $pagination = $paginator->paginate(
            $repository,
            $request->query->getInt('page', 1), // Current page number
            50 // Number of items per page
        );
        $basketItemsCount = count($basketItems);

        $allProducts = $pr->findAll();
        $newCars =  $newCars = array_reverse($allProducts); // Inverser l'ordre des produits pour simuler un tri par date d'ajout décroissante
        //$otherProducts = array_slice($allProducts, 0, count($allProducts) - count($newCars)); // Produits restants

        return $this->render('produit/indexfront.html.twig', [
            'listS' => $pagination,
            'existingArticles' => $existingArticles,
            'basketItemsCount' => $basketItemsCount,
            'products' => $allProducts, // Produits pour nav-home
            'newCars' => $newCars, // Produits pour nav-shopping
        ]);
    }


    /*
#[Route('/az', name: 'app_afficahge_produits')]
    public function index(ProduitsRepository $pr): Response
    {
        $allProducts = $pr->findAll();
        $newCars =  $newCars = array_reverse($allProducts); // Inverser l'ordre des produits pour simuler un tri par date d'ajout décroissante
        //$otherProducts = array_slice($allProducts, 0, count($allProducts) - count($newCars)); // Produits restants

        return $this->render('produit/page-front-produit.html.twig',  [
            'products' => $allProducts, // Produits pour nav-home
            'newCars' => $newCars, // Produits pour nav-shopping
        ]);
    }
*/






    /************************************************************************************************************************************************* */
    /************************************************************************************************************************************************* */
    #[Route('/generate-pdf', name: 'app_generate')]
    public function generatePdf(Pdf $pdf): Response
    {
        // Fetch all produits from your repository
        $produits = $this->getDoctrine()->getRepository(Produits::class)->findAll();

        // Render the produits to HTML (assuming you have a 'pdf_produits.html.twig' template)
        $html = $this->renderView('produit/pdf_produits.html.twig', [
            'produits' => $produits,
        ]);

        // Generate PDF
        $filename = 'produits_' . date('Ymd_His') . '.pdf';
        $response = new Response($pdf->getOutputFromHtml($html), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);

        return $response;
    }

    #[Route('/calculator', name: 'app_calculator')]
    public function calculator(Request $request): Response
    {
        $form = $this->createForm(CalculatorType::class); // Use CalculatorType form type

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $vehiclePrice = $data['vehiclePrice'];
            $interestRate = $data['interestRate'] / 100; // Convert percentage to decimal
            $period = $data['period'];
            $downPayment = $data['downPayment'];

            // Calculate the loan payment
            $loanAmount = $vehiclePrice - $downPayment;
            $monthlyInterestRate = $interestRate / 12;
            $monthlyPayment = ($loanAmount * $monthlyInterestRate) / (1 - pow(1 + $monthlyInterestRate, -$period));

            return $this->render('produit/page-calculator.html.twig', [
                'form' => $form->createView(),
                'monthlyPayment' => $monthlyPayment,
            ]);
        }

        return $this->render('produit/page-calculator.html.twig', [
            'form' => $form->createView(),
        ]);
    }





    #[Route('/detailProduit/front/{idp}', name: 'detailProduitFront')]
    public function detailArticlefront(\Symfony\Component\HttpFoundation\Request $req, $idp)
    {
        $session =  $req->getSession();
        $usersession = $session->get('user');
        if ($usersession == null) {
            return $this->redirectToRoute("app_login");
        }

        $em = $this->getDoctrine()->getManager();
        $prod = $em->getRepository(Produits::class)->find($idp);


        return $this->render('produit/detailProduitFront.html.twig', array(
            'id' => $prod->getIdProduit(),
            'name' => $prod->getLabelle(),
            'prix' => $prod->getPrix(),
            'artdispo' => $prod->getStatus(),
            'description' => $prod->getDescription(),
            'image' => $prod->getPhoto(),
            'pg' => $prod->getPeriodeGarantie()

        ));
    }




    #[Route('/exportExcel', name: 'exportExcel')]
    public function exportExcel(Request $request)
    {
        $session =  $request->getSession();
        $usersession = $session->get('user');
        if ($usersession == null) {
            return $this->redirectToRoute("app_login");
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add headers to the sheet
        $sheet->setCellValue('A1', 'labelle');
        $sheet->setCellValue('B1', 'prix');
        $sheet->setCellValue('C1', 'status');
        $sheet->setCellValue('D1', 'periodegarantie');
        $sheet->setCellValue('E1', 'description');

        // Get the products from the database
        $products = $this->getDoctrine()->getRepository(Produits::class)->findAll();

        // Add the products to the sheet
        $row = 2;
        foreach ($products as $product) {
            $sheet->setCellValue('A' . $row, $product->getLabelle());
            $sheet->setCellValue('B' . $row, $product->getPrix());
            $sheet->setCellValue('C' . $row, $product->getStatus());
            $sheet->setCellValue('D' . $row, $product->getPeriodeGarantie());
            $sheet->setCellValue('E' . $row, $product->getDescription());
            $row++;
        }


        // Create the Excel file
        $writer = new Xlsx($spreadsheet);
        $filename = 'services.xlsx';
        $writer->save($filename);

        // Return the Excel file as a response
        return $this->file($filename);
    }



    /**
     * @Route("/ajax_search/", name="ajax_search")
     */
    public function chercherArticles(Request $request, EntityManagerInterface $entityManager)
    {
        $session =  $request->getSession();
        $usersession = $session->get('user');
        if ($usersession == null) {
            return $this->redirectToRoute("app_login");
        }

        $requestString = $request->get('q');

        $queryBuilder = $entityManager->createQueryBuilder();
        $query = $queryBuilder
            ->select('P')
            ->from('App\Entity\Produits', 'P')
            ->where('P.labelle LIKE :str')
            ->setParameter('str', '%' . $requestString . '%')
            ->getQuery();

        $products = $query->getResult();
        if (!$products) {
            $result['products']['error'] = "Produits non trouvé :( ";
        } else {
            $result['products'] = $this->getRealEntities($products);
        }
        return new Response(json_encode($result));
    }


    // LES  attributs
    public function getRealEntities($products)
    {
        foreach ($products as $products) {
            $realEntities[$products->getLabelle()] = [$products->getPhoto(), $products->getStatus(), $products->getLabelle(), $products->getPrix()];
        }
        return $realEntities;
    }












    #[Route('/exportpdf', name: 'exportpdf')]
    public function exportToPdf(\App\Repository\ProduitsRepository $repository): Response
    {
        // Récupérer les données de réservation depuis votre base de données
        $Services = $repository->findAll();

        // Créer le tableau de données pour le PDF
        $tableData = [];
        foreach ($Services as $Services) {
            $tableData[] = [
                'name' => $Services->getLabelle(),
                'prix' => $Services->getPrix(),
                'status' => $Services->getStatus(),
                'description' => $Services->getDescription(),
                'periodeGarentie' => $Services->getPeriodeGarantie(),
                'User' => $Services->getIdUtilisateur()->getNomutilisateur() . ' ' . $Services->getIdUtilisateur()->getPrenomutilisateur(),
                'mail' => $Services->getIdUtilisateur()->getAdresseemail()
            ];
        }

        // Créer le PDF avec Dompdf
        $dompdf = new Dompdf();
        $html = $this->renderView('produit/export-pdf.html.twig', [
            'tableData' => $tableData,
        ]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        // Envoyer le PDF au navigateur
        $response = new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="articles.pdf"',
        ]);
        return $response;
    }







    #[Route('/produit/tricroi', name: 'tri', methods: ['GET', 'POST'])]
    public function triCroissant(\App\Repository\ProduitsRepository $pr): Response
    {
        $produit = $pr->findAllSorted();

        return $this->render('produit/index.html.twig', [
            'listS' => $produit,
        ]);
    }

    #[Route('/produit/tridesc', name: 'trid', methods: ['GET', 'POST'])]
    public function triDescroissant(\App\Repository\ProduitsRepository $pr): Response
    {
        $produit = $pr->findAllSorted1();

        return $this->render('produit/index.html.twig', [
            'listS' => $produit,
        ]);
    }

    #[Route('/produit/search', name: 'search2', methods: ['GET', 'POST'])]
    public function search2(Request $request, \App\Repository\ProduitsRepository $repo): Response
    {
        $query = $request->query->get('query');
        $prodid = $request->query->get('prodid');
        $prodlabelle = $request->query->get('prodlabelle');
        $prodstatus = $request->query->get('prodstatus');

        $produit = $repo->advancedSearch($query, $prodid, $prodlabelle, $prodstatus);

        return $this->render('produit/index.html.twig', [
            'listS' => $produit,
        ]);
    }

    // stat
    #[Route('/dashboard/stat', name: 'stat', methods: ['POST', 'GET'])]
    public function VoitureStatistics(\App\Repository\ProduitsRepository $repo, Request $request): Response
    {
        $session = $request->getSession();
        $usersession = $session->get('user');
        if ($usersession == null) {
            return $this->redirectToRoute("app_login");
        }

        // Calcul du total des produits
        $total = $repo->countByType('voiture') +
            $repo->countByType('maison') +
            $repo->countByType('terrain');

        // Vérification si le total est différent de zéro avant de calculer les pourcentages
        if ($total != 0) {
            // Calcul des nombres de produits par type
            $voitureCount = $repo->countByType('voiture');
            $maisonCount = $repo->countByType('maison');
            $terrainCount = $repo->countByType('terrain');

            // Calcul des pourcentages
            $voiturePercentage = round(($voitureCount / $total) * 100);
            $maisonPercentage = round(($maisonCount / $total) * 100);
            $terrainPercentage = round(($terrainCount / $total) * 100);
        } else {
            // Si le total est égal à zéro, les pourcentages seront également égaux à zéro
            $voiturePercentage = 0;
            $maisonPercentage = 0;
            $terrainPercentage = 0;
        }

        return $this->render('produit/stat.html.twig', [
            'voiturePercentage' => $voiturePercentage,
            'maisonPercentage' => $maisonPercentage,
            'terrainPercentage' => $terrainPercentage,
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Produits;

use App\Entity\Ratings;
use App\Entity\Utilisateur;
use App\Entity\Views;
use App\Form\ProduitsType;
use App\Form\RatingsType;
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
use App\Repository\ImageRepository;
use App\Repository\UtilisateurRepository;
use App\Repository\ViewsRepository;
use Dompdf\Dompdf;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\JsonResponse;

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
    public function addprod(UtilisateurRepository $userRepository, ManagerRegistry $em, Request $request, Security $security): Response
    {
        $em = $em->getManager();
        // Récupérer l'utilisateur connecté à partir de la session Symfony
        $connectedUser = $request->getSession()->get('user');




        // Vérifier si l'utilisateur est connecté
        if (!$connectedUser) {
            return $this->redirectToRoute('app_login');
        }
        // Vérifier le rôle de l'utilisateur
        $isAdmin = $security->isGranted('ROLE_ADMIN');

        // Choix de la template en fonction du rôle de l'utilisateur
        $template = $isAdmin ? 'produit/page-dashboard-add-produits.html.twig' : 'produit/page-dashboard-add-produits_front.html.twig';


        $prod = new Produits();

        $prod->setIdUtilisateur($connectedUser);

        $form = $this->createForm(ProduitsType::class, $prod);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $localisation = $prod->getLocalisation();
            // Construire la valeur de "localisation" avec le préfixe et le suffixe requis
            $formattedLocalisation = "https://maps.google.com/maps?q=" . urlencode($localisation) . "&t=&z=13&ie=UTF8&iwloc=&output=embed";
            // Définir la valeur formatée dans l'entité
            $prod->setLocalisation($formattedLocalisation);


            /*  
! photo

            $imageFile = $form->get('photo')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle file upload error
                }

                // Create and persist the image entity
                $image = new Image();
                $image->setUrl('/uploads/' . $newFilename); // Assuming your images are stored in the uploads directory
                $image->setProduits($prod);
                $em->persist($image);
            }     // Persist location and associated image
*/

            $imageFiles = $form->get('images')->getData(); // Utilisez le nom de champ associé à la relation OneToMany

            foreach ($imageFiles as $imageFile) {
                if ($imageFile) {
                    try {
                        // Générez un nom de fichier unique en utilisant l'extension de fichier d'origine
                        $originalFilename = $imageFile->getClientOriginalName();
                        $extension = $imageFile->getClientOriginalExtension();
                        $newFilename = uniqid() . '.' . $extension;

                        $imageFile->move(
                            $this->getParameter('kernel.project_dir') . '/public/uploads',
                            $newFilename
                        );

                        // Définissez le chemin réel de l'image téléchargée dans l'entité Image
                        $imagePath = '/uploads/' . $newFilename; // Chemin relatif depuis le répertoire public

                        // Créez une nouvelle entité Image
                        $image = new Image();
                        $image->setUrl($imagePath);
                        // Associez l'image au produit
                        $prod->addImage($image);

                        // Persistez l'entité Image
                        $em->persist($image);
                    } catch (FileException $e) {
                        // Gérer l'erreur de téléchargement de fichier
                        $this->addFlash('error', 'Failed to upload one or more images.');
                        return $this->redirectToRoute('app_produit');
                    }
                }
            }

            //
            $em->persist($prod);
            $em->flush();

            return $this->redirectToRoute('app_back_affiche');
        }

        return $this->render($template, [
            'form' => $form->createView(),
            'connectedUser' => $connectedUser,
        ]);
    }
    #[Route('/supprimer/{idp}', name: 'app_supprimer')]
    public function removeprod(ManagerRegistry $em, Request $request, ProduitsRepository $pr, $idp): Response
    {

        $prod = $pr->find($idp);


        $em = $em->getManager();
        $em->remove($prod);
        $em->flush();
        // Ajouter un message flash pour informer l'utilisateur que l'article a été supprimé
        $this->addFlash(
            'noticedelete',
            'L\'article a été supprimé avec succès.'
        );
        return $this->redirectToRoute('app_back_affiche');
    }

    #[Route('/modifierProduit/{idp}', name: 'app_modifier_produit')]
    public function editprod(ManagerRegistry $em, Request $request, ProduitsRepository $pr, $idp, ImageRepository $imageRepository): Response
    {
        $em = $em->getManager();
        $produit = $pr->find($idp);

        if (!$produit) {
            throw $this->createNotFoundException('Produit non trouvé');
        }

        $form = $this->createForm(ProduitsType::class, $produit);


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Format localisation value
            $localisation = $produit->getLocalisation();
            $formattedLocalisation = "https://maps.google.com/maps?q=" . urlencode($localisation) . "&t=&z=13&ie=UTF8&iwloc=&output=embed";
            $produit->setLocalisation($formattedLocalisation);

            // Handle image upload if form is submitted
            $existingImages = $imageRepository->findBy(['produits' => $produit]);
            $imageFiles = $form->get('images')->getData(); // Récupère un tableau d'objets UploadedFile

            // Supprimer les images existantes associées au produit
            foreach ($existingImages as $existingImage) {
                $existingImagePath = $existingImage->getUrl();
                if (file_exists($existingImagePath)) {
                    unlink($existingImagePath);
                }
                // Supprimer l'image de la base de données
                $em->remove($existingImage);
            }

            // Enregistrer les nouvelles images associées au produit
            foreach ($imageFiles as $imageFile) {
                if ($imageFile) {
                    try {
                        // Générer un nom de fichier unique en utilisant l'extension de fichier d'origine
                        $originalFilename = $imageFile->getClientOriginalName();
                        $extension = $imageFile->getClientOriginalExtension();
                        $newFilename = uniqid() . '.' . $extension;

                        // Déplacer le fichier téléchargé vers l'emplacement désiré
                        $imageFile->move(
                            $this->getParameter('kernel.project_dir') . '/public/uploads',
                            $newFilename
                        );

                        // Créer une nouvelle entité Image
                        $newImage = new Image();
                        $newImage->setUrl('/uploads/' . $newFilename);
                        $newImage->setProduits($produit);

                        // Persistez l'entité Image
                        $em->persist($newImage);
                    } catch (FileException $e) {
                        // Gérer l'erreur de téléchargement de fichier
                        $this->addFlash('error', 'Failed to upload one or more images.');
                        return $this->redirectToRoute('app_produit');
                    }
                }
            }
            // Persist the changes to the database
            $em->flush();


            return $this->redirectToRoute('app_back_affiche');
        }

        return $this->render('produit/page-dashboard-edit-produits.html.twig', [
            'form' => $form->createView(),
            //'photo_path' => $produit->getPhoto() ? '/uploads/' . $produit->getPhoto() : null,
        ]);
    }
    #[Route('/produitback', name: 'app_back_affiche')]
    public function showprodback(Request $request, ProduitsRepository $pr,  ImageRepository $imageRepository): Response
    {
        /*
        // Vérifier si l'utilisateur a le rôle requis
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->render('error/not_admin.html.twig', []);
        }
*/

        // Récupérer l'utilisateur connecté à partir de la session Symfony
        $connectedUser = $request->getSession()->get('user');




        // Vérifier si l'utilisateur est connecté
        if (!$connectedUser) {
            return $this->redirectToRoute('app_login');
        }

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

        /*
! images
*/
        // Fetch images
        $imagesByLocation = [];
        foreach ($products as $prod) {
            $imagesByLocation[$prod->getIdProduit()] = $imageRepository->findBy(['produits' => $prod]);
        }


        $totalPages = ceil($totalItems / $itemsPerPage);

        return $this->render('produit/page-dashboard-listing.html.twig', [
            'products' => $products,
            'searchQuery' => $searchQuery,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'imagesByLocation' => $imagesByLocation,
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
    public function index(Request $request, PaginatorInterface $paginator, UtilisateurRepository $userRep, BasketRepository $basketRep, ProduitsRepository $pr, ImageRepository $imageRepository): Response
    {

        $session =  $request->getSession();
        $usersession = $session->get('user');
        if ($usersession == null) {
            return $this->redirectToRoute("app_login");
        }

        $connectedUser = $this->session->get('user');


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
        $newCars =  $newCars = array_reverse($allProducts); // Inverser l'ordre des produits pour simuler un tri par date d'ajout décroissante
        //$otherProducts = array_slice($allProducts, 0, count($allProducts) - count($newCars)); // Produits restants
        /*
        //images
        $entityManager = $this->getDoctrine()->getManager();
        $produit = $entityManager->getRepository(Produits::class)->find(2);
        $images = [];

        // Vérifiez si le produit existe
        if ($produit) {
            // Récupérez les images associées à ce produit
            $produitImages = $produit->getImages();

            // Ajoutez chaque image à la liste des images
            foreach ($produitImages as $image) {
                $images[] = $image;
            }
        } else {
            echo 'Produit non trouvé.';
        }
*/



        // Fetch images
        $imagesByLocation = [];
        foreach ($allProducts as $prod) { // Utilisez $allProducts à la place de $products
            $imagesByLocation[$prod->getIdProduit()] = $imageRepository->findBy(['produits' => $prod]);
        }
        return $this->render('produit/indexfront.html.twig', [
            'listS' => $pagination,
            'existingArticles' => $existingArticles,
            'basketItemsCount' => $basketItemsCount,
            'products' => $allProducts, // Produits pour nav-home
            'newCars' => $newCars, // Produits pour nav-shopping
            //   'p' => $produit, // Assurez-vous de transmettre le produit au modèle Twig
            // 'images' => $images, // Transmettez la liste des images au modèle Twig
            'imagesByLocation' => $imagesByLocation,
        ]);
    }



    #[Route('/az2', name: 'app_afficahge_produits2')]
    public function index2(ProduitsRepository $pr): Response
    {
        $allProducts = $pr->findAll();
        $newCars =  $newCars = array_reverse($allProducts); // Inverser l'ordre des produits pour simuler un tri par date d'ajout décroissante
        //$otherProducts = array_slice($allProducts, 0, count($allProducts) - count($newCars)); // Produits restants

        return $this->render('produit/page-front-produit.html.twig',  [
            'products' => $allProducts, // Produits pour nav-home
            'newCars' => $newCars, // Produits pour nav-shopping
        ]);
    }





    #[Route('/detailProduit/front/{idp}', name: 'detailProduitFront')]
    public function detailArticlefront(\Symfony\Component\HttpFoundation\Request $req, $idp, SessionInterface $session, EntityManagerInterface $em, UtilisateurRepository $userRepository, ImageRepository $imageRepository)
    {
        // Retrieve the user from the session
        $userId = $session->get('user');
        $user = $em->getRepository(Utilisateur::class)->find($userId);

        if (!$user) {
            return $this->redirectToRoute("app_login");
        }
        $em = $this->getDoctrine()->getManager();
        $prod = $em->getRepository(Produits::class)->find($idp);

        // Check if the user has already viewed this product
        $view = $em->getRepository(Views::class)->findOneBy(['utilisateur' => $user, 'produit' => $prod]);

        if (!$view) {
            // If the user has not viewed this product before, create a new view record
            $view = new Views();
            $view->setUtilisateur($user);
            $view->setProduit($prod);

            // Persist and flush the view entity to the database
            $em->persist($view);
            $em->flush();

            // Increment the product view count since this is a new view
            $prod->setNombreDeVues($prod->getNombreDeVues() + 1);

            // Persist the updated product entity to the database
            $em->persist($prod);
            $em->flush();
        }





        // Get the localisation
        $localisation = $prod->getLocalisation();
        // Check if localisation is null
        if ($localisation === null) {
            // Set a default value or handle the null case as needed
            $localisation = "No GPS coordinates available";
        }

        // Fetch images for the product
        $imagesByLocation = [];
        $imagesByLocation[$prod->getIdProduit()] = $imageRepository->findBy(['produits' => $prod]);


        return $this->render('produit/detailProduitFront.html.twig', array(
            'id' => $prod->getIdProduit(),
            'name' => $prod->getLabelle(),
            'prix' => $prod->getPrix(),
            'artdispo' => $prod->getStatus(),
            'description' => $prod->getDescription(),
            'image' => $prod->getPhoto(),
            'pg' => $prod->getPeriodeGarantie(),
            'gps' => $localisation,
            'video' => $video = $prod->getVideo(), // Add the 'video' attribute to pass the video URL to the Twig template
            'nombreDeVues' => $prod->getNombreDeVues(), // Pass the updated view count to the template
            'product' => $prod,
            'imagesByLocation' => $imagesByLocation,
            'user' => $user,
        ));
    }

    /*///
  *************************************************likes*************************
*/
    /*  
public function likeProduct($idp, EntityManagerInterface $em)
    {
        // Fetch the product
        $prod = $em->getRepository(Produits::class)->find($idp);

        // Increment likes count in the Views entity
        $prod->getViews()->incrementLikes();

        // Update the entity
        $em->flush();

        // Return the updated likes count
        return new JsonResponse(['likes' => $prod->getViews()->getLikes()]);
    }

    public function dislikeProduct($idp, EntityManagerInterface $em)
    {
        // Fetch the product
        $prod = $em->getRepository(Produits::class)->find($idp);

        // Increment dislikes count in the Views entity
        $prod->getViews()->incrementDislikes();

        // Update the entity
        $em->flush();

        // Return the updated dislikes count
        return new JsonResponse(['dislikes' => $prod->getViews()->getDislikes()]);
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
            $realEntities[$products->getLabelle()] = [$products->getIdProduit(), $products->getPhoto(), $products->getStatus(), $products->getLabelle(), $products->getPrix()];
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
            $imagePath = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $Services->getPhoto();
            $tableData[] = [
                'image_path' => $imagePath,
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



    /************************************************************************************************************************************************* */
    /*******************************************************rating ****************************************************************************************** */

    #[Route('/submit/comment-and-rating/{id_produit}', name: 'submit_comment_and_rating', methods: ['POST'])]
    public function submitCommentAndRating(Request $request, int $id_produit): Response
    {
        // Récupérer la note et le commentaire soumis depuis la requête
        $rating = $request->request->get('rating');
        $commentaire = $request->request->get('comment');

        // Récupérer le produit correspondant à l'ID
        $produit = $this->getDoctrine()->getRepository(Produits::class)->find($id_produit);

        // Vérifier si le produit existe
        if (!$produit) {
            throw $this->createNotFoundException('Le produit n\'existe pas.');
        }

        // Créer une nouvelle instance de l'entité Ratings
        $ratingEntity = new Ratings();
        $ratingEntity->setRating($rating);
        $ratingEntity->setProduit($produit);
        $ratingEntity->setCommentaire($commentaire); // Setter pour le commentaire

        // Enregistrer l'évaluation dans la base de données
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($ratingEntity);
        $entityManager->flush();

        // Rediriger ou renvoyer une réponse appropriée
        return $this->redirectToRoute('produit/indexfront.html.twig');
    }




    /**
     * @Route("/submit_rating/{id_produit}", name="submit_rating")
     */
    public function submitRating(Request $request, EntityManagerInterface $entityManager, int $id_produit): Response
    {
        $rating = new Ratings();
        $form = $this->createForm(RatingsType::class, $rating);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Set the product and user for the rating
            $rating->setProduit($this->getDoctrine()->getRepository(Produits::class)->find($id_produit));
            $rating->setUser($this->getUser());

            $entityManager->persist($rating);
            $entityManager->flush();

            return $this->redirectToRoute('product_details', ['id' => $id_produit]);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $product = $entityManager->getRepository(Produits::class)->find($id_produit);

        return $this->render('produit/indexfront.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/show/{id}", name="product_details")
     */
    public function show(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $product = $entityManager->getRepository(Produits::class)->find($id);

        // Calculate average rating
        $averageRating = $this->calculateAverageRating($product);

        return $this->render('produit/indexfront.html.twig', [
            'product' => $product,
            'averageRating' => $averageRating,
        ]);
    }

    /**
     * Calculate average rating for a product.
     */
    private function calculateAverageRating(Produits $product): float
    {
        $ratings = $product->getRatings();

        if (count($ratings) === 0) {
            return 0.0;
        }

        $totalRating = 0;
        foreach ($ratings as $rating) {
            $totalRating += $rating->getRating();
        }

        return $totalRating / count($ratings);
    }

    #[Route('/noterService/{artid}/{note}', name: 'noterService')]
    public function noterService(Request $request, $artid, $note): Response
    {
        $session =  $request->getSession();
        $usersession = $session->get('user');
        if ($usersession == null) {
            return $this->redirectToRoute("app_login");
        }

        $Services = $this->getDoctrine()->getManager()->getRepository(Produits::class)->find($artid);

        $form = $this->createForm(ProduitsType::class, $Services);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $fileUpload = $form->get('photo')->getData();
            $fileName = md5(uniqid()) . '.' . $fileUpload->guessExtension();

            $fileUpload->move($this->getParameter('kernel.project_dir') . '/public/uploads', $fileName);

            $Services->setServImg($fileName);
            $Services->setNote($note);



            $em = $this->getDoctrine()->getManager();
            $em->persist($Services);
            $em->flush();
            $this->addFlash(
                'notice',
                'Produit a été bien noté '
            );

            return $this->redirectToRoute('display_prod_front');
        }

        return $this->render(
            'article/modifierArticle.html.twig',
            ['f' => $form->createView()]
        );
    }


    /**
     * @Route("/articles/{id}/note", name="service_note")
     */
    public function addNoteToService(Request $request, Produits $service)
    {
        $session =  $request->getSession();
        $usersession = $session->get('user');
        if ($usersession == null) {
            return $this->redirectToRoute("app_login");
        }

        $note = $request->request->get('note');

        if ($note) {
            $service->setNote($note);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Note added successfully!');
        } else {
            $this->addFlash('error', 'Note value is required!');
        }

        return $this->redirectToRoute('display_prod_front');
    }

    #[Route('/getNoterArticlePage/{artid}', name: 'getNoterArticlePage')]
    public function getNoterServicePage(\Symfony\Component\HttpFoundation\Request $req, $artid)
    {
        $session =  $req->getSession();
        $usersession = $session->get('user');
        if ($usersession == null) {
            return $this->redirectToRoute("app_login");
        }

        $em = $this->getDoctrine()->getManager();
        $Services = $em->getRepository(Article::class)->find($artid);


        return $this->render('article/getNoterArticlePage.html.twig', array(
            'Id' => $Services->getArtid(),
            'name' => $Services->getArtlib(),
            'prix' => $Services->getArtprix(),
            'artdispo' => $Services->getArtdispo(),
            'description' => $Services->getArtdesc(),
            'image' => $Services->getArtimg(),
            'catlib' => $Services->getCatlib(),
            'User' => $Services->getId()->getNom() . ' ' . $Services->getId()->getPrenom(),
            'mail' => $Services->getId()->getMail()


        ));
    }
}

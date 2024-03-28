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

use Knp\Snappy\Pdf;
use App\Form\CalculatorType;

class ProduitController extends AbstractController
{


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
}

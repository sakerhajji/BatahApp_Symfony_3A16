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

use Knp\Snappy\Pdf;

class ProduitController extends AbstractController
{
    #[Route('/details/{idp}', name: 'app_details')]
    public function details(ProduitsRepository $pr, $idp): Response
    {

        $product = $pr->find($idp);

        return $this->render('produit/page-front-details-produit.html.twig', ['product' => $product]);
    }
    #[Route('/detail', name: 'app_detail')]
    public function detail(): Response
    {
        return $this->render('produit/page-front-details-produit.html.twig');
    }

    #[Route('/az', name: 'app_afficahge_produits')]
    public function index(ProduitsRepository $pr): Response
    {
        return $this->render('produit/page-front-produit.html.twig', ['products' => $pr->findAll()]);
    }

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

    #[Route('/produitback', name: 'app_back_affiche')]
    public function showprodback(ManagerRegistry $em, ProduitsRepository $pr): Response
    {

        return $this->render('produit/page-dashboard-listing.html.twig', ['products' => $pr->findAll()]);
    }
    #[Route('/modifier/{idp}', name: 'app_modifier_produit')]
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
}

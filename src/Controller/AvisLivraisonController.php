<?php

namespace App\Controller;

use App\Entity\AvisLivraison;
use App\Entity\Livraison;
use App\Form\AvisLivraisonType;
use App\Repository\AvisLivraisonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/avis')]
class AvisLivraisonController extends AbstractController
{
    #[Route('/', name: 'app_avis_livraison_index', methods: ['GET'])]
    public function index(AvisLivraisonRepository $avisLivraisonRepository): Response
    {
        return $this->render('avis_livraison/index.html.twig', [
            'avis_livraisons' => $avisLivraisonRepository->findAll(),
        ]);
    }


    #[Route('/new/{idLivraison}', name: 'app_avis_livraison_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,$idLivraison): Response
    {
        $avisLivraison = new AvisLivraison();
        $livraison = $this->getDoctrine()->getRepository(Livraison::class)->find($idLivraison);
        $avisLivraison->setLivraison($livraison);
        $form = $this->createForm(AvisLivraisonType::class, $avisLivraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($avisLivraison);
            $entityManager->flush();

            return $this->redirectToRoute('liste_livraisons_utilisateur', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('avis_livraison/new.html.twig', [
            'avis_livraison' => $avisLivraison,
            'form' => $form,
        ]);
    }

    #[Route('/{idAvis}', name: 'app_avis_livraison_show', methods: ['GET'])]
    public function show(AvisLivraison $avisLivraison): Response
    {
        return $this->render('avis_livraison/show.html.twig', [
            'avis_livraison' => $avisLivraison,
        ]);
    }

    #[Route('/{idAvis}/edit', name: 'app_avis_livraison_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AvisLivraison $avisLivraison, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AvisLivraisonType::class, $avisLivraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_avis_livraison_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('avis_livraison/edit.html.twig', [
            'avis_livraison' => $avisLivraison,
            'form' => $form,
        ]);
    }

    #[Route('/{idAvis}', name: 'app_avis_livraison_delete', methods: ['POST'])]
    public function delete(Request $request, AvisLivraison $avisLivraison, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$avisLivraison->getIdAvis(), $request->request->get('_token'))) {
            $entityManager->remove($avisLivraison);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_avis_livraison_index', [], Response::HTTP_SEE_OTHER);
    }
}

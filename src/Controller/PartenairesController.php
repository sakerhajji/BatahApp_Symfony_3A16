<?php

namespace App\Controller;

use App\Entity\Partenaires;
use App\Form\PartenairesType;
use App\Repository\PartenairesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;


#[Route('/partenaires')]
class PartenairesController extends AbstractController
{
    #[Route('/', name: 'app_partenaires_index', methods: ['GET'])]
    public function index(PartenairesRepository $PartenairesRepository): Response
    {
        $partenaires = $PartenairesRepository->findAll();
        $data = [];
        foreach ($partenaires as $partenaire) {
            $data[] = [
                'nom' => $partenaire->getNom(),
                'points' => $partenaire->getPoints(),
            ];
        }

        // Rendre la vue Twig avec les données des partenaires
        return $this->render('partenaires/index.html.twig', [
            'partenaires' => $partenaires,
            'partenairesJson' => json_encode($data),
        ]);
    }



    #[Route('/new', name: 'app_partenaires_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $partenaire = new Partenaires();
        $form = $this->createForm(PartenairesType::class, $partenaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($partenaire);
            $entityManager->flush();

            return $this->redirectToRoute('app_partenaires_index', [], Response::HTTP_SEE_OTHER);
        }

        // Récupérer les erreurs de validation
        $errors = $form->getErrors(true);

        return $this->render('partenaires/new.html.twig', [
            'partenaire' => $partenaire,
            'form' => $form->createView(),
        ]);

    }

    #[Route('/{idpartenaire}', name: 'app_partenaires_show', methods: ['GET'])]
    public function show(Partenaires $partenaire): Response
    {
        return $this->render('partenaires/show.html.twig', [
            'partenaire' => $partenaire,
        ]);
    }

    #[Route('/{idpartenaire}/edit', name: 'app_partenaires_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Partenaires $partenaire, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PartenairesType::class, $partenaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($partenaire);
            $entityManager->flush();

            return $this->redirectToRoute('app_partenaires_index', [], Response::HTTP_SEE_OTHER);
        }

        // Récupérer les erreurs de validation
        $errors = $form->getErrors(true);

        return $this->render('partenaires/edit.html.twig', [
            'partenaire' => $partenaire,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/partenaires/{idpartenaire}', name: 'app_partenaires_delete', methods: ['POST'])]
    public function delete(Request $request, Partenaires $partenaire, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$partenaire->getIdpartenaire(), $request->request->get('_token'))) {
            $entityManager->remove($partenaire);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_partenaires_index', [], Response::HTTP_SEE_OTHER);
    }
}

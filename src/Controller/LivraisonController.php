<?php

namespace App\Controller;

use App\Entity\Commands;
use App\Entity\Livraison;
use App\Entity\Utilisateur;
use App\Entity\Partenaires;
use App\Entity\ServiceApresVente;
use App\Form\LivraisonType;
use App\Repository\CommandsRepository;
use App\Repository\LivraisonRepository;
use App\Services\EmailSender;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/livraison')]

class LivraisonController extends AbstractController
{
    #[Route('/recuperer-livraison/{idLivraison}', name: 'recuperer_livraison')]
    public function recupererLivraison($idLivraison, EntityManagerInterface $entityManager): Response
    {
        $livraison = $entityManager->getRepository(Livraison::class)->find($idLivraison);

        if (!$livraison) {
            throw $this->createNotFoundException('La livraison n\'existe pas');
        }

        // Modifier le statut de la livraison à "Récupéré"
        $livraison->setStatut('Récupéré');
        $livraison->setPartenaire(null);

        // Enregistrer les modifications dans la base de données
        $entityManager->flush();

        // Rediriger vers la page de liste des livraisons
        return $this->redirectToRoute('liste_livraisons_utilisateur');
    }

    #[Route('/liste-livraisons-utilisateur', name: 'liste_livraisons_utilisateur')]
    public function listeLivraisonsUtilisateur(): Response
    {
        $userId = 1;

        $commandes = $this->getDoctrine()->getRepository(Commands::class)->findBy(['idClient' => $userId]);
        $livraisons = [];

        foreach ($commandes as $commande) {
            $livraisonsCommande = $this->getDoctrine()->getRepository(Livraison::class)->findBy(['commande' => $commande]);

            foreach ($livraisonsCommande as $livraison) {
                $livraisons[] = $livraison;
            }
        }
        return $this->render('livraison/livraisons_utilisateur.html.twig', [
            'livraisons' => $livraisons,
        ]);
    }

    #[Route('/commandes', name: 'liste_commandes', methods: ['GET'])]
    public function listeCommandes(CommandsRepository $commandsRepository, LivraisonRepository $livraisonRepository): Response
    {
        $commandes = $commandsRepository->findAll();
        $livraisons = $livraisonRepository->findAll();

        return $this->render('livraison/liste.html.twig', [
            'commandes' => $commandes,
            'livraisons' => $livraisons,
        ]);
    }




    #[Route('/livrer-commande/{id}', name: 'livrer_commande')]
    public function livrerCommande($id, EntityManagerInterface $entityManager): Response
    {
        $commande = $entityManager->getRepository(Commands::class)->find($id);

        if (!$commande) {
            throw $this->createNotFoundException('La commande n\'existe pas');
        }

        $livraison = new Livraison();
        $livraison->setCommande($commande);
        $livraison->setDateLivraison(new \DateTime());

        $entityManager->persist($livraison);
        $entityManager->flush();

        $this->addFlash('success', 'La commande a été livrée avec succès');

        return $this->redirectToRoute('liste_commandes');
    }
    #[Route('/', name: 'app_livraison_index', methods: ['GET'])]
    public function index(LivraisonRepository $livraisonRepository): Response
    {
        return $this->render('livraison/index.html.twig', [
            'livraisons' => $livraisonRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_livraison_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $livraison = new Livraison();
        $form = $this->createForm(LivraisonType::class, $livraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($livraison);
            $entityManager->flush();

            return $this->redirectToRoute('app_livraison_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('livraison/new.html.twig', [
            'livraison' => $livraison,
            'form' => $form,
        ]);
    }

    #[Route('/{idLivraison}', name: 'app_livraison_show', methods: ['GET'])]
    public function show(Livraison $livraison): Response
    {
        return $this->render('livraison/show.html.twig', [
            'livraison' => $livraison,
        ]);
    }

    #[Route('/{idLivraison}/edit', name: 'app_livraison_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Livraison $livraison, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LivraisonType::class, $livraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_livraison_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('livraison/edit.html.twig', [
            'livraison' => $livraison,
            'form' => $form,
        ]);
    }

    #[Route('/{idLivraison}', name: 'app_livraison_delete', methods: ['POST'])]
    public function delete(Request $request, Livraison $livraison, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$livraison->getIdLivraison(), $request->request->get('_token'))) {
            $entityManager->remove($livraison);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_livraison_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{idLivraison}/affecter_partenaire', name: 'affecter_partenaire', methods: ['GET', 'POST'])]
    public function affecterpartenaire(Request $request, Livraison $livraison, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->add('partner', EntityType::class, [
                'class' => Partenaires::class,
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionner un partenaire',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->where('p.type = :type')
                        ->setParameter('type', 'livraison');
                },
            ])
            ->add('submit', SubmitType::class, ['label' => 'Assigner'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le partenaire sélectionné
            $selectedPartner = $form->get('partner')->getData();

            $selectedPartner->setPoints($selectedPartner->getPoints() + 1);

            $livraison->setPartenaire($selectedPartner->getIdpartenaire());
            $livraison->setStatut("en cours");

            // Envoyer un e-mail au partenaire
            $partnerEmail = $selectedPartner->getEmail();
            $partnerNom = $selectedPartner->getNom();
            $email=new EmailSender();
            $email->sendEmail($partnerEmail,"affectation","$partnerNom vous avez affecter à un service");

            // Enregistrer les modifications dans la base de données
            $entityManager->persist($selectedPartner);
            $entityManager->persist($livraison);
            $entityManager->flush();

            // Redirection vers la page d'index après l'attribution du partenaire
            return $this->redirectToRoute('app_livraison_index');
        }

        return $this->render('livraison/affecter_partenaire.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}

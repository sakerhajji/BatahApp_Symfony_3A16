<?php

namespace App\Controller\PartenaireControllers;

use App\Entity\Commands;

use App\Entity\Livraison;
use App\Entity\Partenaires;
use App\Form\LivraisonType;
use App\Repository\CommandsRepository;
use App\Repository\LivraisonRepository;
use App\Service\EmailSender2;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/livraison')]
class LivraisonController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }
    #[Route('/{idLivraison}/affecter_partenaire', name: 'affecter_partenaire', methods: ['GET', 'POST'])]
    public function affecterpartenaire(Request $request, $idLivraison, EntityManagerInterface $entityManager): Response
    {
        $livraison = $entityManager->getRepository(Livraison::class)->find($idLivraison);
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
            $adresseCommande = $livraison->getCommande()->getAdresse();
            $lienMaps = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($adresseCommande);
            $urlLogo = $this->getParameter('kernel.project_dir') . '/public/images/batah.jpg';
            $message = '
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Affectation de service</title>
    <style>
        body {
            font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .logo {
            width: 100px;
            display: block;
            margin: 0 auto 20px;
        }
        .content {
            text-align: center;
            margin-top: 20px;
        }
        .button {
            background-color: #007bff;
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }
        .button:hover {
            background-color: #0056b3;
        }
        .footer {
            font-size: 14px;
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="cid:logo" alt="Logo BATAH" class="logo">
        <div class="content">
            <p>Bonjour <strong>'. $partnerNom .'</strong>,</p>
            <p>Vous avez été affecté à une nouvelle livraison. Veuillez suivre le lien ci-dessous pour consulter les détails et l\'itinéraire de la livraison.</p>
            <a href="' .$lienMaps.'" class="button">Voir l\'itinéraire</a>
            <p>Merci de faire partie de notre réseau de partenaires.</p>
        </div>
        <div class="footer">
            Pour toute assistance, contactez-nous au : +21623456789<br>
            <small>© BATAH - Tous droits réservés</small>
        </div>
    </div>
</body>
</html>


';
            $email=new EmailSender2();
            $email->sendEmail($partnerEmail, "Affectation de service", $message,$urlLogo);

            // Enregistrer les modifications dans la base de données
            $entityManager->persist($selectedPartner);
            $entityManager->persist($livraison);
            $entityManager->flush();

            // Redirection vers la page d'index après l'attribution du partenaire
            return $this->redirectToRoute('app_livraison_index');
        }

        return $this->render('livraison/affecter_partenaire.html.twig', [
            'form' => $form->createView(),
            'user' => $this->session->get('user')
        ]);
    }
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
    public function listeLivraisonsUtilisateur(SessionInterface $session): Response
    {

        $commandes = $this->getDoctrine()->getRepository(Commands::class)->findBy(['idClient' => $this->session->get('user')->getId()]);
        $livraisons = [];

        foreach ($commandes as $commande) {
            $livraisonsCommande = $this->getDoctrine()->getRepository(Livraison::class)->findBy(['commande' => $commande]);

            foreach ($livraisonsCommande as $livraison) {
                $livraisons[] = $livraison;
            }
        }
        return $this->render('livraison/livraisons_utilisateur.html.twig', [
            'livraisons' => $livraisons,
            'user' => $this->session->get('user'),
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
            'user' => $this->session->get('user'),
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
        $livraison->setDateLivraison(new DateTime());

        $entityManager->persist($livraison);
        $entityManager->flush();

        $this->addFlash('success', 'La commande a été livrée avec succès');

        return $this->redirectToRoute('liste_commandes');
    }

    #[Route('/', name: 'app_livraison_index', methods: ['GET'])]
    public function index(LivraisonRepository $livraisonRepository): Response
    {
        $livraisonsStats = $livraisonRepository->countDeliveriesByStatus();
        return $this->render('livraison/index.html.twig', [
            'livraisons' => $livraisonRepository->findAll(),
            'livraisonsStats' => $livraisonsStats,
            'user' => $this->session->get('user'),

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
            'user' => $this->session->get('user'),
        ]);
    }

    #[Route('/{idLivraison}', name: 'app_livraison_show', methods: ['GET'])]
    public function show($idLivraison,EntityManagerInterface $entityManager): Response
    {
        $livraison = $entityManager->getRepository(Livraison::class)->find($idLivraison);
        return $this->render('livraison/show.html.twig', [
            'livraison' => $livraison,
            'user' => $this->session->get('user'),
        ]);
    }

    #[Route('/{idLivraison}/edit', name: 'app_livraison_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, $idLivraison, EntityManagerInterface $entityManager): Response
    {
        $livraison = $entityManager->getRepository(Livraison::class)->find($idLivraison);
        $form = $this->createForm(LivraisonType::class, $livraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_livraison_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('livraison/edit.html.twig', [
            'livraison' => $livraison,
            'form' => $form,
            'user' => $this->session->get('user'),
        ]);
    }

    #[Route('/{idLivraison}', name: 'app_livraison_delete', methods: ['POST'])]
    public function delete(Request $request, Livraison $livraison, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $livraison->getIdLivraison(), $request->request->get('_token'))) {
            $entityManager->remove($livraison);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_livraison_index', [], Response::HTTP_SEE_OTHER);
    }



}

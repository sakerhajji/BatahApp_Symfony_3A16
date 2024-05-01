<?php

namespace App\Controller\PartenaireControllers;

use App\Entity\Partenaires;
use App\Entity\ServiceApresVente;
use App\Form\ServiceApresVenteType;
use App\Repository\ServiceApresVenteRepository;
use App\Services\EmailSender2;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[Route('/service')]
class ServiceApresVenteController extends AbstractController
{
    private $session;
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }
    #[Route('/', name: 'app_service_apres_vente_index', methods: ['GET'])]
    public function index(ServiceApresVenteRepository $serviceApresVenteRepository): Response
    {
        return $this->render('service_apres_vente/index.html.twig', [
            'service_apres_ventes' => $serviceApresVenteRepository->findAll(),
            'user'=>$this->session->get('user'),
        ]);
    }

    #[Route('/new', name: 'app_service_apres_vente_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $serviceApresVente = new ServiceApresVente();
        $form = $this->createForm(ServiceApresVenteType::class, $serviceApresVente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($serviceApresVente);
            $entityManager->flush();

            return $this->redirectToRoute('app_service_apres_vente_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('service_apres_vente/new.html.twig', [
            'service_apres_vente' => $serviceApresVente,
            'form' => $form,
            'user'=>$this->session->get('user'),
        ]);
    }

    #[Route('/{idService}', name: 'app_service_apres_vente_show', methods: ['GET'])]
    public function show(ServiceApresVente $serviceApresVente): Response
    {
        return $this->render('service_apres_vente/show.html.twig', [
            'service_apres_vente' => $serviceApresVente,
            'user'=>$this->session->get('user'),
        ]);
    }

    #[Route('/{idService}/edit', name: 'app_service_apres_vente_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ServiceApresVente $serviceApresVente, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ServiceApresVenteType::class, $serviceApresVente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_service_apres_vente_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('service_apres_vente/edit.html.twig', [
            'service_apres_vente' => $serviceApresVente,
            'form' => $form,
            'user'=>$this->session->get('user'),
        ]);
    }

    #[Route('/{idService}', name: 'app_service_apres_vente_delete', methods: ['POST'])]
    public function delete(Request $request, ServiceApresVente $serviceApresVente, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$serviceApresVente->getIdService(), $request->request->get('_token'))) {
            $entityManager->remove($serviceApresVente);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_service_apres_vente_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/{idService}/app_assign_partner', name: 'app_assign_partner', methods: ['GET', 'POST'])]
    public function assignPartner(Request $request, ServiceApresVente $serviceApresVente, EntityManagerInterface $entityManager): Response
    {
        $serviceType = $serviceApresVente->getType();

        $form = $this->createFormBuilder()
            ->add('partner', EntityType::class, [
                'class' => Partenaires::class,
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionner un partenaire',
                // Ajoutez une condition pour filtrer les partenaires par type
                'query_builder' => function (EntityRepository $er) use ($serviceType) {
                    return $er->createQueryBuilder('p')
                        ->andWhere('p.type = :type')
                        ->setParameter('type', $serviceType);
                },
            ])
            ->add('submit', SubmitType::class, ['label' => 'Assigner'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le partenaire sélectionné
            $selectedPartner = $form->get('partner')->getData();

            // Incrémenter les points du partenaire
            $selectedPartner->setPoints($selectedPartner->getPoints() + 1);

            // Affecter le partenaire au service après-vente
            $serviceApresVente->setIdPartenaire($selectedPartner->getIdpartenaire());

            // Envoyer un e-mail au partenaire
            $partnerEmail = $selectedPartner->getEmail();
            $partnerNom = $selectedPartner->getNom();
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
            font-family: Arial, sans-serif;
            font-size: 16px;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            color: #333; /* Default text color */
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff; /* Container background color */
            border: 1px solid #ddd; /* Container border */
            border-radius: 5px; /* Container border radius */
        }
        .logo {
            max-width: 150px;
            float: left;
            margin-right: 20px; /* Add some spacing between the image and text */
        }
        p {
            margin-bottom: 10px;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        /* Custom styles */
        .message {
            color: #555; /* Message text color */
        }
        .description {
            color: #f00; /* Description text color */
        }
    </style>
</head>
<body>
 <div class="container">
    <div style="text-align: center;">
        <img src="cid:logo" alt="Logo de l\'application" class="logo">
    </div>
   
    <p>Bonjour ' . $partnerNom . ',</p>
    <p class="message">Vous avez été affecté à un service</p>
    <p>Merci,</p>
    <p >Votre équipe d\'application BATAH</p>
    <div style="text-align: center;">
        <p class="description">Pour toute assistance, veuillez nous contacter au numéro suivant : +21623456789</p>
    </div>
    </div>
</body>
</html>

';
            $email=new EmailSender2();
            $email->sendEmail($partnerEmail, "Affectation de service", $message,$urlLogo);

            // Enregistrer les modifications dans la base de données
            $entityManager->persist($selectedPartner);
            $entityManager->persist($serviceApresVente);
            $entityManager->flush();

            // Redirection vers la page d'index après l'attribution du partenaire
            return $this->redirectToRoute('app_service_apres_vente_index');
        }

        return $this->render('service_apres_vente/assign_partner.html.twig', [
            'form' => $form->createView(),
            'user'=>$this->session->get('user'),
        ]);
    }


}

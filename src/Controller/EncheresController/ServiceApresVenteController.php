<?php

namespace App\Controller\EncheresController;

use App\Entity\Partenaires;
use App\Entity\ServiceApresVente;
use App\Form\ServiceApresVenteType;
use App\Repository\EncheresRepository\ServiceApresVenteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/service')]
class ServiceApresVenteController extends AbstractController
{
    #[Route('/', name: 'app_service_apres_vente_index', methods: ['GET'])]
    public function index(ServiceApresVenteRepository $serviceApresVenteRepository): Response
    {
        return $this->render('service_apres_vente/index.html.twig', [
            'service_apres_ventes' => $serviceApresVenteRepository->findAll(),
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
        ]);
    }

    #[Route('/{idService}', name: 'app_service_apres_vente_show', methods: ['GET'])]
    public function show(ServiceApresVente $serviceApresVente): Response
    {
        return $this->render('service_apres_vente/show.html.twig', [
            'service_apres_vente' => $serviceApresVente,
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



    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }
    #[Route('/{idService}/app_assign_partner', name: 'app_assign_partner', methods: ['GET', 'POST'])]
    public function assignPartner(Request $request, ServiceApresVente $serviceApresVente, EntityManagerInterface $entityManager): Response
    {
        // Créez le formulaire pour sélectionner le partenaire
        $form = $this->createFormBuilder()
            ->add('partner', EntityType::class, [
                'class' => Partenaires::class,
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionner un partenaire',
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
            $email = (new Email())
                ->from('batahapp@gmail.com')
                ->to($partnerEmail)
                ->subject('Nouvelle affectation')
                ->html('<p>Bonjour,</p><p>Vous avez été affecté à un nouveau service. Merci de consulter votre tableau de bord pour plus de détails.</p>');

            $this->mailer->send($email);

            // Enregistrer les modifications dans la base de données
            $entityManager->persist($selectedPartner);
            $entityManager->persist($serviceApresVente);
            $entityManager->flush();

            // Redirection vers la page d'index après l'attribution du partenaire
            return $this->redirectToRoute('app_service_apres_vente_index');
        }

        return $this->render('service_apres_vente/assign_partner.html.twig', [
            'form' => $form->createView(),
        ]);
    }


}

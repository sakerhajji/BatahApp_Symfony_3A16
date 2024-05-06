<?php

namespace App\Controller\serviceControllers;

use App\Entity\Partenaires;
use App\Entity\ServiceApresVente;
use App\Form\ServiceApresVenteType;
use App\Form\ServiceApresVenteTypeEdit;
use App\Repository\ServiceApresVenteRepository;
use App\Service\EmailSender2;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

#[Route('/service/apres/vente')]
class ServiceApresVenteController extends AbstractController
{
    private $session;
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }
    #[Route('/', name: 'app_service_apres_vente_index', methods: ['GET'])]
    public function index(ServiceApresVenteRepository $serviceApresVenteRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $allServiceApresVentesQuery = $serviceApresVenteRepository->createQueryBuilder('s')
            ->getQuery();

        // Paginate the results
        $serviceApresVentes = $paginator->paginate(
            $allServiceApresVentesQuery, // Query to paginate
            $request->query->getInt('page', 1), // Current page number, 1 by default
            5 // Number of items per page
        );

        return $this->render('service_apres_vente/index.html.twig', [
            'service_apres_ventes' => $serviceApresVentes,
            'user' => $this->session->get('user'),
        ]);
    }

    #[Route('/new', name: 'app_service_apres_vente_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $serviceApresVente = new ServiceApresVente();
        $serviceApresVente->setStatus(false);
        $form = $this->createForm(ServiceApresVenteType::class, $serviceApresVente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($serviceApresVente);
            $entityManager->flush();
        }

        return $this->renderForm('service_apres_vente/new.html.twig', [
            'service_apres_vente' => $serviceApresVente,
            'form' => $form,
            'user' => $this->session->get('user'),
        ]);
    }
    #[Route('/stat', name: 'services_plus_achetes')]
    public function servicesPlusAchetes(ServiceApresVenteRepository $serviceApresVenteRepository): Response
    {
        $servicesPlusAchetes = $serviceApresVenteRepository->countMostPurchasedServices();

        $labels = [];
        $data = [];
        foreach ($servicesPlusAchetes as $service) {
            $labels[] = $service['description'];
            $data[] = $service['total'];
        }

        return $this->render('service_apres_vente/stat.html.twig', [
            'labels' => json_encode($labels),
            'data' => json_encode($data),
            'user' => $this->session->get('user'),
        ]);
    }
    #[Route('/{idService}', name: 'app_service_apres_vente_show', methods: ['GET'])]
    public function show(ServiceApresVente $serviceApresVente): Response
    {
        return $this->render('service_apres_vente/show.html.twig', [
            'service_apres_vente' => $serviceApresVente,
            'user' => $this->session->get('user'),
        ]);
    }

    #[Route('/{idService}/edit', name: 'app_service_apres_vente_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ServiceApresVente $serviceApresVente, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ServiceApresVenteTypeEdit::class, $serviceApresVente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $selectedPartnerId = $form->get('idPartenaire')->getData();
            $selectedPartner = $entityManager->getRepository(Partenaires::class)->find($selectedPartnerId);
            $serviceApresVente->setIdPartenaire($selectedPartner);
            $selectedPartner->setPoints($selectedPartner->getPoints() + 1);
            $partnerEmail = $selectedPartner->getEmail();
            $partnerNom = $selectedPartner->getNom();
            $urlLogo = $this->getParameter('kernel.project_dir') . '/public/images/batah.jpg';
            $message = '<!DOCTYPE html>
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
</html>';
            $email = new EmailSender2();
            $email->sendEmail($partnerEmail, "Affectation de service", $message, $urlLogo);

            $entityManager->persist($selectedPartner);
            $entityManager->persist($serviceApresVente);
            $entityManager->flush();

            $this->sendTwilioMessage($serviceApresVente);

            return $this->redirectToRoute('app_service_apres_vente_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('service_apres_vente/edit.html.twig', [
            'service_apres_vente' => $serviceApresVente,
            'form' => $form,
            'user' => $this->session->get('user'),
        ]);
    }
    #[Route('/searchccc/aaa/', name: 'search', methods: ['GET'])]
    public function search(Request $request, ServiceApresVenteRepository $serviceApresVenteRepository): Response
    {
        $search = $request->query->get('search');

        if (!$search) {
            return $this->redirectToRoute('app_service_apres_vente_index');
        }

        $results = $serviceApresVenteRepository->findByTypeOrStatus($search);

        return $this->render('service_apres_vente/search.html.twig', [
            'results' => $results,
            'user' => $this->session->get('user'),
        ]);
    }
    #[Route('/{idService}', name: 'app_service_apres_vente_delete', methods: ['POST'])]
    public function delete(Request $request, ServiceApresVente $serviceApresVente, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $serviceApresVente->getIdService(), $request->request->get('_token'))) {
            $entityManager->remove($serviceApresVente);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_service_apres_vente_index', [], Response::HTTP_SEE_OTHER);
    }
    /**
     * @throws ConfigurationException
     * @throws TwilioException
     */
    private function sendTwilioMessage(ServiceApresVente $serviceApresVente): void
    {
        $twilioAccountSid = $this->getParameter('twilio_account_sid');
        $twilioAuthToken = $this->getParameter('twilio_auth_token');
        $twilioPhoneNumber = $this->getParameter('twilio_phone_number');

        $twilioClient = new Client($twilioAccountSid, $twilioAuthToken);

        $messageBody = sprintf(
            'Your affectation has been successfully registered with the following details:' .
                "\nDescription: %s\nType: %s\nDate: %s\nStatus: %s",
            $serviceApresVente->getDescription(),
            $serviceApresVente->getType(),
            $serviceApresVente->getDate()->format('Y-m-d H:i:s'),
            $serviceApresVente->isStatus() ? 'Yes' : 'No'
        );

        $twilioClient->messages->create(
            '+21699425500', // Replace with the recipient's phone number
            [
                'from' => $twilioPhoneNumber,
                'body' => $messageBody
            ]
        );
    }


    #[Route('/sav/pdf/{id}', name: 'app_sav_pdf')]
    public function generatePdf(ServiceApresVente $serviceApresVente): Response
    {
        $html = $this->renderView('service_apres_vente/pdf_template.html.twig', [
            'serviceApresVente' =>  $serviceApresVente,
        ]);

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isHtml5ParserEnabled', true);
        $pdfOptions->set('isPhpEnabled', true);
        $pdfOptions->set('isHtml5ParserEnabled', true);
        $pdfOptions->set('isPhpEnabled', true);
        $pdfOptions->set('isPhpEnabled', true);
        $pdfOptions->set('isPhpEnabled', true);
        $pdfOptions->set('isPhpEnabled', true);


        $dompdf = new Dompdf($pdfOptions);
        $dompdf->loadHtml($html);

        // Set paper size (A4)
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Stream the generated PDF back to the user
        $output = $dompdf->output();

        $response = new Response($output);
        $response->headers->set('Content-Type', 'application/pdf');

        return $response;
    }
}

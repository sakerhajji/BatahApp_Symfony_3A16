<?php

namespace App\Controller;

use App\Entity\ServiceApresVente;
use App\Form\ServiceApresVenteType;
use App\Form\ServiceApresVenteTypeEdit;
use App\Repository\ServiceApresVenteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

#[Route('/service/apres/vente')]
class ServiceApresVenteController extends AbstractController
{
    #[Route('/', name: 'app_service_apres_vente_index', methods: ['GET'])]
    public function index(ServiceApresVenteRepository $serviceApresVenteRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $allServiceApresVentesQuery = $serviceApresVenteRepository->createQueryBuilder('s')
            ->getQuery();

        // Paginate the results
        $serviceApresVentes = $paginator->paginate(
            $allServiceApresVentesQuery, // Query to paginate
            $request->query->getInt('page', 1), // Current page number, 1 by default
            5// Number of items per page
        );

        return $this->render('service_apres_vente/index.html.twig', [
            'service_apres_ventes' => $serviceApresVentes,
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

            return $this->redirectToRoute('app_service_apres_vente_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('service_apres_vente/new.html.twig', [
            'service_apres_vente' => $serviceApresVente,
            'form' => $form,
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
        $form = $this->createForm(ServiceApresVenteTypeEdit::class, $serviceApresVente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->sendTwilioMessage($serviceApresVente);

            return $this->redirectToRoute('app_service_apres_vente_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('service_apres_vente/edit.html.twig', [
            'service_apres_vente' => $serviceApresVente,
            'form' => $form,
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

<?php

namespace App\Controller;

use App\Entity\Partenaires;
use App\Form\PartenairesType;
use App\Repository\LivraisonRepository;
use App\Repository\PartenairesRepository;
use App\Repository\ServiceApresVenteRepository;
use App\Services\EmailSender2;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Services\FileUploader;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;



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
            $imageFile = $form->get('logo')->getData();
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $originalFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
            try {
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            $partenaire->setLogo($newFilename);


            $entityManager->persist($partenaire);
            $entityManager->flush();


            return $this->redirectToRoute('app_partenaires_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('partenaires/new.html.twig', [
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
            $imageFile = $form->get('logo')->getData(); // Ensure 'logo' is the correct field name
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // Update the 'logo' property to store the new filename
                $partenaire->setLogo($newFilename);
            }

            $entityManager->persist($partenaire);
            $entityManager->flush();

            return $this->redirectToRoute('app_partenaires_index', [], Response::HTTP_SEE_OTHER);
        }

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
    #[Route('/{idPartenaire}/services', name: 'app_partenaire_services', methods: ['GET'])]
    public function showServices($idPartenaire, LivraisonRepository $livraisonRepository, ServiceApresVenteRepository $serviceRepository): Response
    {
        $partenaire = $this->getDoctrine()->getRepository(Partenaires::class)->find($idPartenaire);

        if ($partenaire->getType() === 'livraison') {
            $services = $livraisonRepository->findBy(['partenaire' => $idPartenaire]);
            return $this->render('partenaires/livraison.html.twig', [
                'services' => $services,
            ]);
        } else {
            $services = $serviceRepository->findBy(['idPartenaire' => $idPartenaire]);
            return $this->render('partenaires/services.html.twig', [
                'services' => $services,
            ]);
        }

    }
}

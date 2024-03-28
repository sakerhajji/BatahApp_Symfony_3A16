<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Location;
use App\Entity\Image;
use App\Form\LocationType;
use App\Repository\LocationRepository;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Repository\ImageRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;



class LocationController extends AbstractController
{
    #[Route('/location', name: 'app_location')]
    public function index(): Response
    {
        return $this->render('location/index2.html.twig', [
            'controller_name' => 'LocationController',
        ]);
    }


    #[Route('/ha', name: 'app_afficahgel')]
    public function front(LocationRepository $lr): Response
    {
        return $this->render('location/page-shop.html.twig', ['locations' => $lr->findAll()]);
    }

    #[Route('/ajoutlocation', name: 'app_ajout_location')]
    public function addLocation(ManagerRegistry $em, Request $request): Response
    {
        $em = $em->getManager();

        $loca = new Location();
        $form = $this->createForm(LocationType::class, $loca);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle image upload
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle file upload error
                }

                // Create and persist the image entity
                $image = new Image();
                $image->setUrl('/uploads/' . $newFilename); // Assuming your images are stored in the uploads directory
                $image->setLocation($loca);
                $em->persist($image);
            }     // Persist location and associated image
            $em->persist($loca);
            $em->flush();

            return $this->redirectToRoute('app_location_back_affiche');
        }

        return $this->render('location/page-dashboard-add-locations.html.twig', ['form' => $form->createView()]);
    }
    #[Route('/location/{idLocation}', name: 'show_location_images')]
    public function showLocationImages(int $idLocation): Response
    {
        $location = $this->getDoctrine()->getRepository(Location::class)->find($idLocation);

        if (!$location) {
            throw $this->createNotFoundException('Location not found');
        }

        return $this->render('your_template.html.twig', [
            'location' => $location,
        ]);
    }
    #[Route('/locationback', name: 'app_location_back_affiche')]
    public function showprodback(ManagerRegistry $em, LocationRepository $lr): Response
    {

        return $this->render('location/page-dashboard-listing.html.twig', ['locations' => $lr->findAll()]);
    }

    #[Route('/supprimer/{idl}', name: 'app_supprimer_location')]
    public function removeloca(ManagerRegistry $em, Request $request, LocationRepository $lr, $idl): Response
    {

        $loca = $lr->find($idl);
        $em = $em->getManager();
        $em->remove($loca);
        $em->flush();
        return $this->redirectToRoute('app_location_back_affiche');
    }
    #[Route('/detailsLocation/{idl}', name: 'app_details_location')]
    public function details(LocationRepository $lr, $idl): Response
    {
        $location = $lr->find($idl);


        return $this->render('location/page-car-single-v1.html.twig', [
            'location' => $location,

        ]);
    }
    #[Route('/detailLocation', name: 'app_detail_location')]
    public function detail(): Response
    {
        return $this->render('produit/page-car-single-v1.html.twig');
    }

    #[Route('/modifierLocation/{idl}', name: 'app_modifier_location')]
    public function editLocation(ManagerRegistry $em, Request $request, LocationRepository $lr, $idl): Response
    {
        $em = $em->getManager();
        $location = $lr->find($idl);

        if (!$location) {
            throw $this->createNotFoundException('Location non trouvée');
        }

        // Create a new form instance for the Location entity
        $form = $this->createForm(LocationType::class, $location);

        // Handle the form submission
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle image upload
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle file upload error
                }

                // Remove old image if exists
                $oldImage = $location->getImages()->first();
                if ($oldImage) {
                    $em->remove($oldImage);
                }

                // Create a new image entity
                $image = new Image();
                $image->setUrl('/uploads/' . $newFilename);
                $image->setLocation($location);
                $em->persist($image);
            }

            // Persist the changes to the database
            $em->flush();

            return $this->redirectToRoute('app_location_back_affiche');
        }

        // Render the form template
        return $this->render('location/page-dashboard-edit-locations.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

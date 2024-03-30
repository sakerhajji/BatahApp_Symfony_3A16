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
    public function addLocation(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $location = new Location();
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persist the Location entity first
            $entityManager->persist($location);
            $entityManager->flush();

            // Handle image upload
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                try {
                    // Generate a unique filename using the original file extension
                    $originalFilename = $imageFile->getClientOriginalName();
                    $extension = $imageFile->getClientOriginalExtension();
                    $newFilename = uniqid() . '.' . $extension;

                    $imageFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads', // Specify the directory where you want to store the uploaded images
                        $newFilename
                    );
                    
                    // Set the real path of the uploaded image in the Image entity
                    $imagePath = '/uploads/' . $newFilename; // Relative path from the public directory
                    
                    // Create a new Image entity
                    $image = new Image();
                    $image->setUrl($imagePath);
                    // Associate the image with the location
                    $image->setLocation($location);
                    
                    // Persist the Image entity
                    $entityManager->persist($image);
                } catch (FileException $e) {
                    // Handle file upload error
                    $this->addFlash('error', 'Failed to upload the image.');
                    return $this->redirectToRoute('app_ajout_location');
                }
            }

            // Flush changes to the database
            $entityManager->flush();

            $this->addFlash('success', 'Location added successfully.');
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
    public function showprodback(ManagerRegistry $em, LocationRepository $lr, ImageRepository $imageRepository): Response
    {
        // Fetch locations
        $locations = $lr->findAllWithUser(); // Assuming you have a custom method findAllWithUser in LocationRepository to join User entity
    
        // Fetch images
        $imagesByLocation = [];
        foreach ($locations as $location) {
            $imagesByLocation[$location->getIdLocation()] = $imageRepository->findBy(['location' => $location]);
        }
    
        return $this->render('location/page-dashboard-listing.html.twig', [
            'locations' => $locations,
            'imagesByLocation' => $imagesByLocation,
        ]);
    }
    #[Route('/supprimer/{idl}', name: 'app_supprimer_location')]
    public function removeloca(ManagerRegistry $em, Request $request, LocationRepository $lr,ImageRepository $imageRepository, $idl): Response
    {

        $loca = $lr->find($idl);
        if (!$loca) {
            // Handle not found scenario, for example:
            throw $this->createNotFoundException('Location not found');
        }
         // Find images associated with the location
        $images = $imageRepository->findByLocation($loca);

        // Delete each associated image
        
        // Optionally, delete the image from the database
        $em = $em->getManager();
        $em->remove($images);
        
       
        $em->remove($loca);
        $em->flush();
        return $this->redirectToRoute('app_location_back_affiche');
    }
    
    #[Route('/details/{idl}', name: 'app_details')]
    public function details(LocationRepository $lr, $idl): Response
    {
        $location = $lr->find($idl);


        return $this->render('location/page-car-single-v1.html.twig', [
            'location' => $location,

        ]);
    }
    #[Route('/detail', name: 'app_detail')]
    public function detail(): Response
    {
        return $this->render('produit/page-car-single-v1.html.twig');
    }

    #[Route('/modifier/{idl}', name: 'app_modifier_location')]
    public function editLocation(ManagerRegistry $em, Request $request, LocationRepository $lr, ImageRepository $imageRepository, $idl): Response
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
            // Handle image upload if form is submitted
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                // Handle image upload
                // Add validation logic here
    
                // Generate a unique filename using the original file extension
                $originalFilename = $imageFile->getClientOriginalName();
                $extension = $imageFile->getClientOriginalExtension();
                $newFilename = uniqid() . '.' . $extension;
    
                // Move the uploaded file to the desired location
                $imageFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads',
                    $newFilename
                );
    
                // Retrieve the existing image associated with the location
                $existingImage = $imageRepository->findOneBy(['location' => $location]);
    
                if ($existingImage) {
                    // Remove the existing image file from the file system
                    $existingImagePath = $existingImage->getUrl();
                    // Delete the image file if it exists
                    if (file_exists($existingImagePath)) {
                        unlink($existingImagePath);
                    }
                } else {
                    // Create a new image entity if no existing image found
                    $existingImage = new Image();
                    $existingImage->setLocation($location);
                    $em->persist($existingImage);
                }
    
                // Update the image URL
                $existingImage->setUrl('/uploads/' . $newFilename);
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
